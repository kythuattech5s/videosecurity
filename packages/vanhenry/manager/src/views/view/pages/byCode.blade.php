<?php 
    $dataChart = $byOrder->select('o.*','od.*',DB::Raw('Sum(od.price*od.qty) as totalPrice'),DB::Raw('Sum(od.qty) as totalQty'),'o.id as order_id','o.code')->orderBy('totalPrice','DESC')->get();
    $arrayName = json_encode($dataChart->pluck('code'));
    $arrayQty = json_encode($dataChart->pluck('totalQty'));
    $arrayPrice = json_encode($dataChart->pluck('totalPrice'));
 ?>
<section class="container-fluid">
    <a href="javascript:history.back()" class="prev-comparess mb-3"> <i class="fa fa-angle-left" aria-hidden="true"></i> Trở lại </a>
    <p class="title-top-alls text-30 opensan-semi mb-3">Doanh thu theo sản phẩm bán chạy</p>
    <div class="form-top-comparess mb-4">
        <!-- <div class="group-btn-top-comparess mb-3">
            <a href="javascript:void(0)">
                <img src="/admin/theme_2/frontend/images/top-comparess-1.png">
                <span>Xuất báo cáo</span>
            </a>
            <a href="javascript:void(0)">
                <img src="/admin/theme_2/frontend/images/top-comparess-2.png">
                <span>Nhân bản</span>
            </a>
        </div>
        <div class="row m0 flex sdate" style="position: relative;">
            <input class="datepicker textcenter" placeholder="Từ" type="text" style="overflow: hidden;padding-right: 18px;" value="">
            <input type="hidden" name="from-created_at" value="">
            <span style="display: block;height: 1px;background: #000;width: 13px;z-index: 9999;position: absolute;left: 45%; top:50%"></span>
            <input class="datepicker textcenter" placeholder="Tới" type="text" style="overflow: hidden;padding-right: 18px;" value="">
            <input type="hidden" name="to-created_at" value="">
        </div> -->
    </div>
    <div class="box-alls p-3 mb-3">
        <canvas id="detail_chart" class="chart-compare" height="270" data-value="{{$arrayQty}}" data-labels="{{$arrayName}}" data-price="{{$arrayPrice}}" ></canvas>
    </div>
    <div class="box-alls p-3 mb-3">
        <!-- <div class="form-filter-table-compare">
            <form>
                <div class="form_class_select">
                    <div class="select-filter-compare">
                        <select class="form-control">
                            <option>Bộ lọc</option>
                            <option>Tên khách hàng</option>
                            <option>Email khách hàng</option>
                            <option>Loại sản phẩm nhãn hiệu</option>
                            <option>trạng thái xuất kho</option>
                        </select>
                    </div>
                    <div class="select-filter-compare">
                        <select class="form-control">
                            <option>thuộc tính</option>
                            <option>Tên khách hàng</option>
                            <option>Email khách hàng</option>
                            <option>Loại sản phẩm nhãn hiệu</option>
                            <option>trạng thái xuất kho</option>
                        </select>
                    </div>
                    {{-- 
                        <!-- các điều kiện sau khi lọc xong -->
                    <div href="#" class="tag-form-filter-compare">Loại bán(chi tiết): Sản phẩm </div> --}}
                </div>
                <div class="submit_class_select">
                    <button type="submit">Thống kê</button>
                </div>
            </form>
        </div> -->
        <table class="table-compares display responsive nowrap cell-border" id="">
            <thead>
                <tr>
                    <th> Mã đơn hàng </th>
                    <th> Số lượng bán ra</th>
                    <th> Tiền hàng </th>
                    <th> Tổng chiết khấu </th>
                    <th> Thuế</th>
                    <th> Tiền hàng trả lại</th>
                    <th data-priority="2">Doanh thu <img src="/admin/theme_2/frontend/images/arrow-up-table.png"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataChart as $code)
                <tr>
                    <td>{{$code->code}}</td>
                    <td>{{$code->qty}}</td>
                    <td>{{number_format($code->qty*$code->total_final,0,'','.')}} đ</td>
                    <td>{{number_format($code->total,0,'','.')}} đ</td>
                    <td>0</td>
                    <td></td>
                    <td>{{number_format($code->totalPrice,0,'','.')}} đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
<section class="box-bottom-content-compares pl-3 pr-3">
    <div class="content-table-bottom-compares">
        <table class="table-bottom-compares display responsive nowrap cell-border " id="">
            <thead>
                <tr>
                    <!-- <td> Tổng </td>
                    <td></td>
                    <td>{{ array_sum(json_decode($dataChart->pluck('totalQtyProduct'))) }}</td>
                    <td>{{ array_sum(json_decode($dataChart->pluck('totalQtyProduct'))) }}</td>
                    <td>{{ number_format(array_sum(json_decode($dataChart->pluck('totalPrice'))),0,'','.') }}đ</td>
                    <td>{{ number_format(array_sum(json_decode($dataChart->pluck('total_final')))-array_sum(json_decode($dataChart->pluck('total'))),0,'','.') }}đ</td>
                    <td data-priority="1"></td>
                    <td data-priority="2">{{ number_format(array_sum(json_decode($dataChart->pluck('totalPrice'))),0,'','.') }}đ</td> -->
                </tr>
            </thead>
        </table>
    </div>
</section>