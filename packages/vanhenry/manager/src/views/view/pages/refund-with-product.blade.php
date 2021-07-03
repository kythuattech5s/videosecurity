<main>
    <section class="container-fluid">
        @include('vh::view.pages.back')
        <p class="title-top-alls text-30 opensan-semi mb-3">Báo cáo trả hàng theo sản phẩm</p>
        @include('vh::view.pages.filter_date')
        <div class="box-alls p-3 mb-3">
            <canvas id="refund-with-product" class="chart-compare" data-value="{{$qtyRefundWithProducts->toJson()}}" data-labels="{{$nameProductWithRefund->toJson()}}" data-price="{{$moneyRefundWithProducts->toJson()}}"></canvas>
        </div>
        <div class="box-alls p-3 mb-3">
            <table class="chart-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                    	<th>Mã sản phẩm</th>
                    	<th>Tên sản phẩm</th>
                        <th>Số hàng trả lại</th>
                        <th>Tiền hàng trả lại</th>
                    </tr>
                </thead>
                <tbody>
                	@if(count($refundWithProducts) > 0)
                	@foreach($refundWithProducts as $key => $item)
                    <tr>
                        <td>{{\Support::show($item->product, 'code')}}</td>
                        <td>{{\Support::show($item->product, 'name')}}</td>
                        <td>{{$item->qty}}</td>
                        <td>{{number_format($item->total_money, 0, ',', '.')}} VND</td>
                    </tr>
                    @endforeach
                    <tr>
	                    <th>Tổng</th>
                        <th></th>
                        <th>{{$refundWithProducts->sum('qty')}}</th>
                        <th>{{number_format($refundWithProducts->sum('total_money'), 0, ',', '.')}} VND</th>
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