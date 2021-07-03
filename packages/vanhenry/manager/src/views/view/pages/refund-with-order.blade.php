<main>
    <section class="container-fluid">
        @include('vh::view.pages.back')
        <p class="title-top-alls text-30 opensan-semi mb-3">Báo cáo trả hàng theo đơn hàng</p>
        @include('vh::view.pages.filter_date')
        <div class="box-alls p-3 mb-3">
            <canvas id="refund-with-order" class="chart-compare" data-labels="{{$codeOrderRefund->toJson()}}" data-price="{{$moneyOrderRefund->toJson()}}"></canvas>
        </div>
        <div class="box-alls p-3 mb-3">
            <table class="chart-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                    	<th>Mã đơn hàng</th>
                    	<th>Số lượng hàng trả lại</th>
                        <th>Tiền hàng trả lại</th>
                    </tr>
                </thead>
                <tbody>
                	@if(count($refundWithOrders) > 0)
                	@foreach($refundWithOrders as $key => $item)
                    <tr>
                        <td>{{\Support::show($item, 'code')}}</td>
                        <td>{{$item->orderDetails->sum('qty')}}</td>
                        <td>{{number_format($item->total_final, 0, ',', '.')}} VND</td>
                    </tr>
                    @endforeach
                    <tr>
	                    <th>Tổng</th>
                        <th>{{$refundWithOrders->sum(function($order){
                            return $order->orderDetails->sum('qty');
                        })}}</th>
                        <th>{{number_format($refundWithOrders->sum('total_final'), 0, ',', '.')}} VND</th>
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