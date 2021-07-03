<?php
$userAgency 			= $order->agency()->where('act', 1)->first();
$userTechnician 		= $order->technician()->where('act', 1)->first();
$orderUserAgency		= $order->agencies()->where('created_at', '>', new \DateTime('15 minutes ago'))->get();
$orderUserTechnicians	= $order->technicians()->where('created_at', '>', new \DateTime('15 minutes ago'))->orderBy('created_at', 'desc')->get();

$customerLat = $order->getLat();
$customerLong = $order->getLong();

?>
@if($order->pushed_carrier == 1)
	<p>Bạn đã bắn đơn lên hãng vận chuyển <b>{{\Support::show($order->carrier, 'name')}}</b></p>
@elseif($userAgency != null && ($order->agency_service_id == 1 || $order->agency_service_id == 2))
	<!-- nếu đã có đại lý xác nhận và công việc = Lấy hàng - Vận chuyển - Lắp đặt or lấy hàng vận chuyển -->
	Đã có đại lý <b>{{\Support::show($userAgency, 'name')}}</b> xác nhận <b>{{\Support::show($order->agencyService, 'name')}}</b>
@elseif($userTechnician != null && $order->technician_service_id == 1)
	<!-- nếu đã có kỹ thuật viên xác nhận và công việc = Lấy hàng - Vận chuyển - Lắp đặt -->
	Đã có kỹ thuật viên <b>{{\Support::show($userTechnician, 'name')}}</b> xác nhận <b>{{\Support::show($order->technicianService, 'name')}}</b>
@elseif($orderUserAgency->count() > 0)
	<!-- nếu đã bắn cho đại lý và đang trong khoảng 15 phút xác nhận -->
	Đang đợi đại lý xác nhận vui lòng quay trở lại lúc <b>{{Carbon::parse($orderUserAgency->first()->created_at)->addMinutes(15)->format('H:i:s')}}</b>
@elseif($orderUserTechnicians->count() > 0)
	<!-- nếu đã bắn cho ktv và đang trong khoảng 15 phút xác nhận -->
	Đang đợi kỹ thuật viên xác nhận vui lòng quay trở lại lúc <b>{{Carbon::parse($orderUserTechnicians->first()->created_at)->addMinutes(15)->format('H:i:s')}}</b>
@elseif(Support::show($order,'agency_service_id') == OrderHelper::AGENCY_PICK_UP_TRANSPORT_INSTALLATION && $order->status == OrderHelper::STATUS_SHIPPING || Support::show($order,'agency_service_id') == OrderHelper::AGENCY_PICK_UP_TRANSPORT && $order->status == OrderHelper::STATUS_SHIPPING)
	<p>Đang đợi bên đại lý giao hàng cho khách hàng</p>
@else
	<form action="esystem/shoot-to-carrier" method="post" class="carrier" onsubmit="FORM.ajax($(this), 'shoot-to-carrier'); return false;">
		@csrf
		<input type="hidden" name="order" value="{{\Support::show($order, 'id')}}">
		<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Chọn hãng vận chuyển</p>
		<select name="carrier">
			@foreach(App\Models\Carrier::where('act', 1)->get() as $carrier)
			<option value="{{\Support::show($carrier, 'id')}}">{{\Support::show($carrier, 'name')}}</option>
			@endforeach
		</select>
		@if($userAgency != null && $order->agency_service_id == 3)
			<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Lấy hàng ở đại lý</p>
			<table>
				<tr style="background: #d2d2d2;">
					<th>Tên đại lý</th>
					<th>Khoảng cách tới khách hàng</th>
				</tr>
				<tr>
					<td>
						<label>
							{{\Support::show($userAgency, 'name')}} 
							<input type="radio" name="agency_pick_up" value="{{\Support::show($userAgency, 'id')}}" checked>
						</label>	
					</td>
					<td>
						@if(empty($userAgency->getLat()) || empty($userAgency->getLong()))
							Thiếu thông tin tọa độ đại lý
						@elseif(empty($customerLat) || empty($customerLong))
							Thiếu thông tin tọa độ khách hàng
						@else
							<?php
								$distance = \Jager::getDistanceBetweenCoordinates($userAgency->getLat(), $userAgency->getLong(), $customerLat, $customerLong, 'KM');
							?>
							{{number_format($distance, 3, ',', '.').' Km'}}
						@endif
					</td>
				</tr>
			</table>
		@else
			<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Lấy hàng ở nhà kho</p>
			<?php
				$depots = App\Models\Depot::where('act', 1)
							->with('province')
							->with('district')
							->with('wards')
							->get();
				// Sắp xếp danh sách nhà kho theo khoảng tới khách hàng cách tăng dần
				$depots = $depots->sortBy(function($v, $k) use($customerLat, $customerLong){
					$lat = $v->getLat();
					$long = $v->getLong();

					if (empty($lat) || empty($long)) {
						$v->setDistanceToCustomer('Thiếu thông tin tọa độ của nhà kho');
						return 9999999999;
					}

					if (empty($customerLat) || empty($customerLong)) {
						$v->setDistanceToCustomer('Thiếu thông tin tọa độ của khách hàng');
						return 9999999999;
					}

					$distance = \Jager::getDistanceBetweenCoordinates($lat, $long, $customerLat, $customerLong, 'KM');
					$v->setDistanceToCustomer(number_format($distance, 3, ',', '.').' Km');
					return $distance;
				});
			?>
			<table>
				<tr style="background: #d2d2d2;">
					<th>Tên nhà kho</th>
					<th>Khoảng cách tới khách hàng</th>
				</tr>
				@foreach($depots as $k => $depot)
				<tr>
					<td>
						<label>
							{{\Support::show($depot, 'name')}} 
							<input type="radio" name="depot_pick_up" value="{{\Support::show($depot, 'id')}}" {{$k == 0 ? 'checked' : ''}}>
						</label>	
					</td>
					<td>
						{{$depot->distanceToCustomer}}
					</td>
				</tr>
				@endforeach
			</table>
		@endif
		<button type="submit" style="padding: 5px 10px; background: #fa4410; color: #fff; margin-top: 10px;">Xác nhận</button>
	</form>
@endif