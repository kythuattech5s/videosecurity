<main>
    <section class="container-fluid">
        @include('vh::view.pages.back')
        <p class="title-top-alls text-30 opensan-semi mb-3">Sản phẩm bán chạy</p>
        @include('vh::view.pages.filter_date')
        <div class="box-alls p-3 mb-3">
            <canvas id="selling-products" class="chart-compare" data-value="{{json_encode($qtyProductSellings)}}" data-labels="{{json_encode($nameProductSellings)}}" data-price="{{json_encode($revenueProductSellings)}}"></canvas>
        </div>
        <div class="box-alls p-3 mb-3">
            <table class="chart-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                    	<th>Mã sản phẩm</th>
                    	<th>Tên sản phẩm</th>
                        <th>Số lượng hàng bán ra</th>
                        <th>Tiền hàng</th>
                        <th>Số lượng đơn hàng</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                	@if(count($productSellings) > 0)
                	<?php
                		$qtyOrder = 0;
                	?>
                	@foreach($productSellings as $key => $item)
                    <tr>
                        <td>{{$item->product->code}}</td>
                        <td>{{$item->product->name}}</td>
                        <td>{{$item->qty}}</td>
                        <td>{{number_format($item->total_money, 0, ',', '.')}} VND</td>
                        <td>{{\FCHelper::qtyOrder($item->product->id, $objStartDate, $objEndDate)}}</td>
                        <td>{{number_format($item->total_money, 0, ',', '.')}} VND</td>
                    </tr>
                    <?php
                    	$qtyOrder += \FCHelper::qtyOrder($item->product->id, $objStartDate, $objEndDate);
                    ?>
                    @endforeach
                    <tr>
	                    <th>Tổng</th>
                        <th></th>
                        <th>{{$productSellings->sum('qty')}}</th>
                        <th>{{number_format($productSellings->sum('total_money'), 0, ',', '.')}} VND</th>
                        <th>{{$qtyOrder}}</th>
                        <th>{{number_format($productSellings->sum('total_money'), 0, ',', '.')}} VND</th>
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