@if(!isset($notUserFont))
<style type="text/css">
* { font-family: BZar_0, DejaVu Sans, sans-serif; }
</style>
@endif
<div class="html">
	<h3 style="text-align: center;">THÔNG TIN KHÁCH HÀNG</h3>
	<table style="border-collapse: collapse;border: 1px solid #ccc;width: 100%;">
		<tr>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Họ tên:</td>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{{$dataItem->cname}}</td>
		</tr>
		<tr>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Số điện thoại:</td>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;"><a href="tel:{{$dataItem->cphone}}">{{$dataItem->cphone}}</a></td>
		</tr>
		<tr>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Địa chỉ:</td>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{{$dataItem->caddress}}</td>
		</tr>
		<tr>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Email:</td>
			<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;"><a href="mailto:{{$dataItem->cmail}}">{{$dataItem->cmail}}</a></td>
		</tr>
	</table>
	<h3 style="text-align: center;">THÔNG TIN ĐƠN HÀNG</h3>
	<?php
	$details = json_decode($dataItem->detail, true);
	?>
	<table id="order_pcb" style="border-collapse: collapse;border: 1px solid #ccc;width: 100%; margin-bottom: 30px;">
		<tr>
			<th style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">STT</th>
			<th style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Trạng thái</th>
			<th style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Sản phẩm</th>
			<th style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Link sản phẩm</th>
			<th style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Số lượng</th>
			<th style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Đơn giá</th>
			<th style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">Thành tiền</th>
		</tr>
		<tbody>
			@foreach($details as $k => $v)
			<tr>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{{$k + 1}}</td>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">
					@if(!isset($v['status']) || $v['status'] == '' || $v['status'] == 1)
					Có sẵn
					@else
					Order
					@endif
				</td>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;"><strong>{!! $v['name'] !!}</strong></td>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{!! $v['link'] !!}</td>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{!! $v['quantity'] !!}</td>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;"><strong>{!! $v['price'] !!}</strong></td>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;">{!! $v['subtotal'] !!}</td>
			</tr>
			@endforeach
			<tr>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;" colspan="6"><strong>Tổng tiền</strong></td>
				<td style="border-collapse: collapse;border: 1px solid #ccc;padding: 5px;"><strong style="color:#d29925">{{number_format($dataItem->total_money, 0, ',', '.')}} đ</strong></td>
			</tr>
		</tbody>
	</table>
</div>