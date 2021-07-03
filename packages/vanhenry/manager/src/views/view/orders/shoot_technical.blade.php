<?php
$technicals = App\Models\UserTechnician::where('act', 1)
				->with('province')
				->with('district')
				->with('wards')
				->where('user_id','!=',$order->user_id)
				->get();
$customerLat = $order->getLat();
$customerLong = $order->getLong();
// Sắp xếp danh sách ktv theo khoảng tới khách hàng cách tăng dần
$technicals = $technicals->sortBy(function($v, $k) use($customerLat, $customerLong){
	$lat = $v->getLat();
	$long = $v->getLong();

	if (empty($lat) || empty($long)) {
		$v->setDistanceToCustomer('Thiếu thông tin tọa độ của kỹ thuật viên');
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

$agencys = App\Models\UserAgency::where('act', 1)->get();
$depots = App\Models\Depot::where('act', 1)->get();

$technicianServices = App\Models\TechnicianService::all();

$userTechnician 		= $order->technician()->where('act', 1)->first();
$userAgency 			= $order->agency()->where('act', 1)->first();
$orderUserAgency		= $order->agencies()->where('created_at', '>', new \DateTime('15 minutes ago'))->get();
$orderUserTechnicians	= $order->technicians()->where('created_at', '>', new \DateTime('15 minutes ago'))->orderBy('created_at', 'desc')->get();
?>

@if($userTechnician != null)
	<!-- nếu đã có ktv xác nhận -->
	Đã có kỹ thuật viên <b>{{\Support::show($userTechnician, 'name')}}</b> xác nhận <b>{{\Support::show($order->technicianService, 'name')}}</b>
@elseif($userAgency != null && $order->agency_service_id == 2 && $order->status < OrderHelper::STATUS_DELIVERED)
	Đã có đại lý <b>{{\Support::show($userAgency, 'name')}}</b> xác nhận <b>{{\Support::show($order->agencyService, 'name')}}</b>
@elseif($userAgency != null && $order->agency_service_id == 1)
	Đã có đại lý <b>{{\Support::show($userAgency, 'name')}}</b> xác nhận <b>{{\Support::show($order->agencyService, 'name')}}</b>
@elseif($orderUserAgency->count() > 0 && $order->agency_id == 0)
	<!-- nếu đã bắn cho đại lý và đang trong khoảng 15 phút xác nhận -->
	Đang đợi đại lý xác nhận vui lòng quay trở lại lúc <b>{{Carbon::parse($orderUserAgency->first()->created_at)->addMinutes(15)->format('H:i:s')}}</b>
@else
<form action="esystem/shoot-to-technician" method="post" class="technician" onsubmit="FORM.ajax($(this), 'shoot-to-technician'); return false;">
	@csrf
	<input type="hidden" name="order" value="{{\Support::show($order, 'id')}}">
	@if($orderUserTechnicians->count() > 0)
		<p>Đang trong quá trình đợi kỹ thuật viên xác nhận đơn hàng. Thời gian xác nhận sẽ kết thúc lúc <b>{{Carbon::parse($orderUserTechnicians->first()->created_at)->addMinutes(15)->format('H:i:s')}}</b>. Bạn vẫn có thể chọn thêm kỹ thuật viên để bắn đơn hàng cho họ.</p>
		<p>Công việc hiện tại đã chọn cho kỹ thuật viên: {{\Support::show($orderUserTechnicians->first()->service, 'name')}}</p>
	@endif
	<div class="technician-list">
		<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Chọn kỹ thuật</p>
		<table>
			<tr style="background: #d2d2d2;">
				<th>Tên kỹ thuật viên</th>
				<th>Khoảng cách tới khách hàng</th>
			</tr>
			@foreach($technicals as $technical)
			<tr>
				<td>
					<label style="display: block;">
						{{\Support::show($technical, 'name')}} 
						<input type="checkbox" name="technical[]" value="{{\Support::show($technical, 'id')}}">
						@if($order->userTechnicianRefusedOrder->contains('user_technician_id', $technical->id))
							<span style="color: red">Đã từng từ chối</span>
						@endif
						&nbsp;
						@if($orderUserTechnicians->contains('user_technician_id', $technical->id))
							<span style="color: #F7B217;">Đang đợi xác nhận</span>
						@endif
					</label>	
				</td>
				<td>
					{{$technical->distanceToCustomer}}
				</td>
			</tr>
			@endforeach
		</table>
	</div>
	@if($orderUserTechnicians->count() > 0)
		<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Công việc đã chọn:</p>
		<select name="service">
			<option value="{{\Support::show($orderUserTechnicians->first()->service, 'id')}}">{{\Support::show($orderUserTechnicians->first()->service, 'name')}}</option>
		</select>
	@else
		<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Chọn công việc:</p>
		<select name="service">
			@foreach($technicianServices as $ts)
			@if($ts->id == 1)
				<?php
					$agencyAction = $order->agency_service_id != 0 && $order->agency_service_id != 3;
					$carrierAction = $order->pushed_carrie == 1;
				?>
				<option value="{{\Support::show($ts, 'id')}}" {{$agencyAction || $carrierAction ? 'disabled' : ''}}>
					{{\Support::show($ts, 'name')}}
					{{$agencyAction ? '(Không thể chọn do đã chọn đại lý vận chuyển)' : ''}}
					{{$carrierAction ? '(Không thể chọn do đã chọn hãng vận chuyển)' : ''}}
				</option>
			@elseif($ts->id == 2)
				<?php
					$agencyAndDepotNotAvailable = $order->agency_service_id != 2 && $order->pushed_carrie == 0;
				?>
				<option value="{{\Support::show($ts, 'id')}}" {{$agencyAndDepotNotAvailable ? 'disabled' : ''}}>
					{{\Support::show($ts, 'name')}}
					{{$agencyAndDepotNotAvailable ? '(Không thể chọn do chưa chọn đại lý vận chuyển hoặc chưa chọn hãng vận chuyển)' : ''}}
				</option>
			@endif
			@endforeach
		</select>
	@endif
	@if($order->agency_id == 0)
		<div class="choose-depot">
			@if($orderUserTechnicians->count() > 0 && $orderUserTechnicians->first()->service->id == 1)
				<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Kho đã chọn:</p>
				<select name="depot">
					<option value="{{\Support::show($order->depot->first(), 'id')}}">{{\Support::show($order->depot->first(), 'name')}}</option>
				</select>
			@elseif(!$agencyAction && !$carrierAction)
				<p style="font-size: 20px; font-weight: bold; text-transform: uppercase;">Chọn kho</p>
				<select name="depot">
					@foreach($depots as $depot)
					<option value="{{\Support::show($depot, 'id')}}">{{\Support::show($depot, 'name')}}</option>
					@endforeach
				</select>
			@endif
		</div>
	@endif
	<button type="submit" style="padding: 5px 10px; background: #fa4410; color: #fff; margin-top: 10px;">Xác nhận</button>
</form>
@endif