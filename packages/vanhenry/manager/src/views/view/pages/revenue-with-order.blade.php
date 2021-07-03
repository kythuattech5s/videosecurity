<main>
    <section class="container-fluid">
        @include('vh::view.pages.back')
        <p class="title-top-alls text-30 opensan-semi mb-3">Doanh thu theo đơn hàng</p>
        @include('vh::view.pages.filter_date')
        <div class="box-alls p-3 mb-3">
            <canvas id="revenue-with-order" class="chart-compare" data-labels="{{$codeRevenueWithOrder->toJson()}}" data-price="{{$moneyRevenueWithOrder->toJson()}}"></canvas>
        </div>
        <div class="box-alls p-3 mb-3">
            <table class="chart-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                    	<th>Mã đơn hàng</th>
                        <th>Số lượng hàng bán ra</th>
                        <th>Tiền hàng</th>
                    	<th>Tổng chiết khấu</th>
                        <th>Phí giao hàng</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                	@if(count($revenueWithOrder) > 0)
                    <?php $discountTotal = 0; ?>
                	@foreach($revenueWithOrder as $key => $item)
                    <tr>
                        <td>{{\Support::show($item, 'code')}}</td>
                        <td>{{$item->orderDetails->sum('qty')}}</td>
                        <td>{{number_format($item->orderDetails->sum(function($orderDetail){
                            return $orderDetail->price_old * $orderDetail->qty;
                        }), 0, ',', '.')}} VND</td>
                        <?php
                            $discount = $item->orderDetails->sum(function($orderDetail){
                                return ($orderDetail->price_old - $orderDetail->price) * $orderDetail->qty;
                            });
                            $discount = $discount < 0 ? 0 : $discount;
                            $discountTotal += $discount;
                        ?>
                        <td>{{number_format($discount, 0, ',', '.')}} VND</td>
                        <td>{{number_format($item->fee, 0, ',', '.')}} VND</td>
                        <td>{{number_format($item->total_final, 0, ',', '.')}} VND</td>
                    </tr>
                    @endforeach
                    <tr>
	                    <th>Tổng</th>
                        <th>{{$revenueWithOrder->sum(function($order){
                            return $order->orderDetails->sum('qty');
                        })}}</th>
                        <th>{{number_format($revenueWithOrder->sum(function($order){
                            return $order->orderDetails->sum(function($orderDetail){
                                return $orderDetail->price_old * $orderDetail->qty;
                            });
                        }), 0, ',', '.')}} VND</th>
                        <th>{{number_format($discountTotal, 0, ',', '.')}} VND</th>
                        <th>{{number_format($revenueWithOrder->sum('fee'), 0, ',', '.')}} VND</th>
                        <th>{{number_format($revenueWithOrder->sum('total_final'), 0, ',', '.')}} VND</th>
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