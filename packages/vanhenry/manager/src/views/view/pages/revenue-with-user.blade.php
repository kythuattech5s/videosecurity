<main>
    <section class="container-fluid">
        @include('vh::view.pages.back')
        <p class="title-top-alls text-30 opensan-semi mb-3">Doanh thu theo khách hàng</p>
        @include('vh::view.pages.filter_date')
        <div class="box-alls p-3 mb-3">
            <canvas id="revenue-with-user" class="chart-compare" data-labels="{{$nameUserRevenueWithUser->toJson()}}" data-price="{{$moneyUserRevenueWithUser->toJson()}}"></canvas>
        </div>
        <div class="box-alls p-3 mb-3">
            <table class="chart-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                    	<th>Tên khách hàng</th>
                        <th>SDT khách hàng</th>
                        <th>SL đơn hàng</th>
                    	<th>SL hàng bán ra</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                	@if(count($revenueWithUser) > 0)
                	@foreach($revenueWithUser as $key => $item)
                    <tr>
                        <td>{{\Support::show($item->user, 'name')}}</td>
                        <td>{{\Support::show($item->user, 'phone')}}</td>
                        <td>{{$item->qty_user_order}}</td>
                        <td>{{$item->user->orderDetails->sum('qty')}}</td>
                        <td>{{number_format($item->sum_total_final, 0, ',', '.')}} VND</td>
                    </tr>
                    @endforeach
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