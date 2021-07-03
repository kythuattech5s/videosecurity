<main>
    <section class="container-fluid">
        @include('vh::view.pages.back')
        <p class="title-top-alls text-30 opensan-semi mb-3">Doanh thu theo thời gian</p>
        @include('vh::view.pages.filter_date')
        <div class="box-alls p-3 mb-3">
            <canvas range-month="{{json_encode($rangeMonths)}}" data-month="{{json_encode($dataMonthsTotal)}}" id="detail-revenue-over-time" class="chart-compare" height="270"></canvas>
        </div>
        <div class="box-alls p-3 mb-3">
            <table class="chart-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                    	<th>Tháng</th>
                    	<th>Số lượng khách hàng</th>
                        <th>Số lượng đơn hàng</th>
                        <th>Số lượng hàng bán ra</th>
                        <th>Tiền hàng</th>
                        <th>Tiền hàng trả lại</th>
                        <th>Phí giao hàng</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                	@if(count($dataMonths) > 0)
                	<?php
                		$totalUser = 0;
                		$totalOrder = 0;
                		$totalQty = 0;
                		$totalMoney = 0;
                		$totalRefund = 0;
                		$totalFee = 0;
                		$revenue = 0;
                	?>
                	@foreach($dataMonths as $key => $item)
                    <tr>
                        <td>{{$key}}</td>
                    	<td>{{$item->groupBy('user_id')->count()}}</td>
                        <td>{{$item->count()}}</td>
                        <td>{{$item->sum(function($order){return $order->orderDetails->sum('qty');})}}</td>
                        <td>
                        	<?php
                        		// tổng tiền all đơn hàng trong tháng bao gồm cả đơn hàng bị hoàn lại
                        		$moneyOrder = $item->sum('total_final');
                    		?>
                        	{{
                        		number_format($moneyOrder , 0, ',', '.').' VND'
	                        }}
	                    </td>
                        <td>
                        	<?php
                        		// tổng tiền đơn hàng bị hoàn lại
                        		$moneyRefund = $item->filter(function($order){
                        			return $order->status == OrderHelper::STATUS_REFUND;
                        		})->sum('total_final');
                    		?>
                        	{{
                        		number_format($moneyRefund , 0, ',', '.').' VND'
	                        }}
	                    </td>
                        <td>{{number_format($item->sum('fee'), 0, ',', '.')}} VND</td>
                        <td>{{number_format($moneyOrder - $moneyRefund, 0, ',', '.')}} VND</td>
                    </tr>
                    <?php
                    	$totalUser += $item->groupBy('user_id')->count();
                		$totalOrder += $item->count();
                		$totalQty += $item->sum(function($order){
                			return $order->orderDetails->sum('qty');
                		});
                		$totalMoney += $moneyOrder;
                		$totalRefund += $moneyRefund;
                		$totalFee += $item->sum('fee');
                		$revenue += ($totalMoney - $totalRefund);
                    ?>
                    @endforeach
                    <tr>
	                    <th>Tổng</th>
	                    <th>{{$totalUser}}</th>
	                    <th>{{$totalOrder}}</th>
	                    <th>{{$totalQty}}</th>
	                    <th>{{number_format($totalMoney, 0, ',', '.')}} VND</th>
	                    <th>- {{number_format($totalRefund, 0, ',', '.')}} VND</th>
	                    <th>{{number_format($totalFee, 0, ',', '.')}} VND</th>
	                    <th>{{number_format($revenue, 0, ',', '.')}} VND</th>
	                </tr>
	                @else
	                <tr>
	                	<td colspan="8">Không có dữ liệu</td>
	                </tr>
	                @endif
                </tbody>
            </table>
        </div>
    </section>
</main>