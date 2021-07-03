<button class="closeOderDetail"><i class="fa fa-times" aria-hidden="true"></i></button>
<div class="orderDetail">
    <p class="title-order">Chi tiết hóa đơn {{Support::show($order,'code')}}</p>
    <div class="row">
        <div class="col-lg-9">
            @if($order->status !== OrderHelper::STATUS_CANCELLED)
            <div class="comfirmOrder">
                <ul class="nav nav-tabs">
                     <li class="active"><a data-toggle="tab" href="#agency">Đại lý</a></li>
                     <li><a data-toggle="tab" href="#technician">Kỹ thuật viện</a></li>
                     <li><a data-toggle="tab" href="#tranport">Vận chuyển</a></li>
                </ul>
                <div class="tab-content">
                    <div id="agency" class="tab-pane fade in active">
                        @if(Support::show($order,'status') == OrderHelper::STATUS_PENDING_CONFIRM && Support::show($order,'agency_id') == 0)
                            <p>>> Chỉ định đại lý</p>
                            <div class="confirm-flex" style="display:flex">
                                <button class="show-modal-chooses-agency" dt-id="{{$order->id}}">Chọn đại lý</button>
                                <button class="active-order" dt-id="{{$order->id}}">
                                    Chuyển đơn cho đại lý
                                </button>
                            </div>
                        @endif
                        @if(Support::show($order,'agency_id') !== 0)
                        <div>
                            <p>Thông tin đại lý</p>
                            <p>Tên shop: {{Support::show($order->agency,'name') ?? ''}}</p>
                            <p>Người đại diện: {{Support::show($order->agency,'owner_name') ?? ''}}</p>
                            <p>Số điện thoại: {{Support::show($order->agency,'phone') ?? ''}}</p>
                            <p>Email: {{Support::show($order->agency,'email') ?? ''}}</p>
                            <p>Địa chỉ :
                            {{
                                Support::show($order->agency,'address')
                                .' - '.Support::getNameOrderAddressDetail($order->agency,'ward')
                                .' - '.Support::getNameOrderAddressDetail($order->agency,'district')
                                .' - '.Support::getNameOrderAddressDetail($order->agency,'province') 
                            }}
                            </p>
                            <p>
                                Dịch vụ: {{OrderHelper::getServiceAgency($order->agency_service_id,$order->payment_status)}}
                            </p>
                        </div>
                        @endif
                        @if($order->depot->first() !== null)
                            Đơn hàng này đã lựa chọn kho là điểm lấy hàng !
                        @endif
                    </div>
                    <div id="technician" class="tab-pane fade">
                        @include('vh::view.orders.shoot_technical')
                    </div>
                    <div id="tranport" class="tab-pane fade">
                        @include('vh::view.orders.shoot_carrier')
                    </div>
                </div>
            </div>
            @else 
            <div class="cancelOrder">
                <p class="title-info fs-24 fw-b mb-15">Lý do hủy đơn hàng</p>
                <div class="detailMain">
                    <p>Lý do hủy đơn: {{OrderHelper::getReasonOrderRefund($order->reasonCancel->reason_cancel_order)}}</p>
                </div>
            </div>
            @endif
            <?php $imgAcceptance = $order->imgAcceptances->first(); ?>
            @if($imgAcceptance !== null)
                @include('vh::view.orders.gallery_acceptance')
            @endif
            <div class="agency_modal modal-all">
            </div>
            <div class="technician_modal modal-all">
            </div>
            @if($order->reasonRefund !== null)
            <div class="request_a_return">
                <div class="comfirmOrder">
                    <p class="title-info fs-24 fw-b mb-15">Yêu cầu đổi trả</p>
                    <div class="reason-cancel__order">
                        <p class="title-twos__alls">Lý do đổi trả:</p>
                        <ul class="list-cancel__order">
                            <li>
                                <p>Yêu cầu bởi: <span>Người mua</span></p>
                                <p>Yêu cầu vào lúc: <span>{{Support::showDateTime($order->updated_at)}}</span></p>
                            </li>
                            <li>
                                <p>Lý do hủy đơn: 
                                    <span>{{OrderHelper::getReasonOrderRefund($order->reasonRefund->reason_refund_order_status)}}</span>
                                </p>
                            </li>
                            <?php $imgs = json_decode($order->reasonRefund->imgs); ?>
                            @if($imgs !== null)
                            <li class="show-img">
                                @foreach($imgs as $item)
                                <a href="{{$item->path.$item->name}}" class="fancybox" rel="group"> 
                                    <img src="{%IMGV2.item.img.390x0%}" alt="{%AIMGV2.item.img.alt%}" title="{%AIMGV2.item.img.title%}">
                                </a>
                                @endforeach
                            </li>
                            @endif
                            <li>Tình trạng yêu cầu: 
                                @if($order->reasonRefund->confirmation == OrderHelper::WAITING_ECO_TO_CONFIRM)
                                    <p class="text-info">Đợi xác nhận</p>
                                @elseif($order->reasonRefund->confirmation == OrderHelper::ECO_AGREE)
                                    <p class="text-success">Đồng ý đổi trả</p>
                                @else 
                                    <p class="text-danger">Từ chối đổi trả</p>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="confirm-flex">
                    <button class="comfirmArgee" onclick="ajaxClickBox(this)" data-url="esystem/agreeRefund" data-title="Đồng ý đổi trả" data-message = 'Bạn có đồng ý đổi trả cho khách không' data-agree = 'Đồng ý' data-refuse = 'Hủy' data-order-id="{{$order->id}}" data-type="1">Đồng ý đổi trả</button>
                    <button class="comfirmRefuse" onclick="ajaxClickBox(this)" data-url="esystem/agreeRefund" data-title="Đồng ý đổi trả" data-message = 'Bạn có đồng ý đổi trả cho khách không' data-agree = 'Đồng ý' data-refuse = 'Hủy' data-order-id="{{$order->id}}" data-type="2">Không đồng ý</button>
                </div>
            </div>
            @endif
            <div class="info-user-order">
                <p class="title-info fs-24 fw-b mb-15">Thông tin khách hàng</p>
                <div class="detailMain">
                    <p>Họ và tên: {{Support::show($order,'name')}}</p>
                    <p>Địa chỉ nhận hàng: 
                    {{
                        Support::show($order,'address')
                        .' - '.Support::getNameOrderAddressDetail($order,'ward')
                        .' - '.Support::getNameOrderAddressDetail($order,'district')
                        .' - '.Support::getNameOrderAddressDetail($order,'province') 
                    }}
                    </p>
                    <p>Số điện thoại: {{Support::show($order,'phone')}}</p>
                    <p>Email: {{Support::show($order,'email')}}</p>
                </div>
            </div>
            <?php 
                $arrayOrderDetail = $order->orderProducts->toArray();
                $sale_ids = data_get($arrayOrderDetail,'*.promotion_id');
                $sale_types = data_get($arrayOrderDetail,'*.promotion_type');
                $arrayVoucherId = array_unique(data_get($arrayOrderDetail,'*.voucher_id'),SORT_REGULAR);
                $voucher_ids = [];
                foreach ($arrayVoucherId as $key => $voucher_id) {
                    if($voucher_id !== null){
                        $voucher_ids[] = $voucher_id;
                    }
                }

                $sales = [];
                foreach($sale_types as $key => $type){
                    $data = [];
                    if($sale_ids[$key] !== null){
                        $data['type'] = $type;
                        $data['id'] = $sale_ids[$key];
                        $sales[] = $data;
                    }
                }
                $sales = array_unique($sales,SORT_REGULAR);
            ?>
            @if( count($sales) !== 0 OR count($voucher_ids) !== 0)
            <div class="promotion-order">
                <p class="title-info fs-24 fw-b mb-15">Chương trình khuyến mãi được hưởng</p>
                <div class="detailMain">
                    <table>
                        <thead>
                            <th>Tên chương trình</th>
                            <th>Khuyến mãi</th>
                        </thead>
                        <tbody>
                            @if(count($sales) !== 0)
                                @foreach($sales as $sale)
                                    <?php $saleItem =  Support::getSaleOrder($sale); ?>
                                    @if($sale['type'] == 'combo')
                                    <tr>
                                        <td><span class="sale_combo">COMBO</span>{{' '.Support::show($saleItem,'name')}}</td>
                                        <td>
                                            <?php 
                                                $orderDetails = Support::findProductSale($sale,$order); 
                                            ?>
                                            @if($saleItem->type == \App\Helpers\ComboConstant::TYPE_COMBO_SALE)
                                                Mua {{$saleItem->buy_min}} sản phẩm để được giảm {{Currency::showMoney($saleItem->discount)}}
                                            @else
                                                Mua {{$saleItem->buy_min}} sản phẩm để được giảm {{$saleItem->discount}} %
                                            @endif
                                        </td>
                                    </tr>
                                    @elseif($sale['type'] == 'flash_sale')
                                        <?php $orderDetails = Support::findProductSale($sale,$order); ?>
                                        @foreach($orderDetails as $orderDetail)
                                        <tr>
                                            <td>
                                                <span class="sale_flash_sale">FLASHSALE</span>{{' '.Support::show($saleItem,'name')}}
                                            </td>
                                                <?php 
                                                    $flash_sale_product = Support::findSalePromotionProduct($sale,$orderDetail->product_id); 
                                                    $product = Support::getProduct($orderDetail->product_id);
                                                ?>

                                            <td>
                                                Mua sản phẩm <b style="color:#fa4410">{{$product->name}}</b> được giảm giá {{Currency::showMoney($product->price - $flash_sale_product->price)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    @elseif($sale['type'] == 'promotion')
                                    <tr>
                                        <?php $orderDetails = Support::findProductSale($sale,$order);?>
                                        @foreach($orderDetails as $orderDetail)
                                        <?php 
                                            $promotion_product = Support::findSalePromotionProduct($sale,$orderDetail->product_id); 
                                            $product = Support::getProduct($orderDetail->product_id);
                                        ?>
                                        <td><span class="sale_promotion">PROMOTION</span>{{' '.Support::show($saleItem,'name')}}</td>
                                        <td>Mua sản phẩm <b style="color:#fa4410">{{$product->name}}</b> được giảm giá {{Currency::showMoney($product->price - $promotion_product->price)}}</td>
                                        @endforeach
                                    </tr>
                                    @elseif($sale['type'] == 'deal')
                                        <?php 
                                            $saleOrder = Support::getSaleOrder($sale);

                                            $orderDetails = Support::findProductSale($sale,$order);

                                            $productMains = \App\Models\DealProductMain::whereIn('product_id',$orderDetails->pluck('product_id'))->where('deal_id',$sale['id'])->get();

                                            $productSubs = \App\Models\DealProductSub::whereIn('product_id',$orderDetails->pluck('product_id'))->where('deal_id',$sale['id'])->get();
                                        ?>
                                        @foreach($productSubs as $product)
                                        <tr>

                                            <td><span class="sale_deal">DEAL</span>{{' '.Support::show($saleItem,'name')}}</td>
                                            
                                                <?php 
                                                    $productMain =  Support::getProduct($productMains[0]->product_id);
                                                    $productSub = Support::getProduct($product->product_id);
                                                ?>
                                                
                                            <td>
                                                Mua sản phẩm <b style="color:#fa4410">{{Support::show($productMain,'name')}}</b> kèm sản phẩm <b style="color:#fa4410">{{Support::show($productSub,'name')}}</b>
                                                được giảm {{Currency::showMoney($productSub->price_old - $product->price)}} 
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif

                            @foreach($voucher_ids as $voucher_id)
                                <?php $voucher =  Support::getSaleOrder($order->voucher_id); ?>
                                <tr>
                                    <td><span class="sale_voucher">VOUCHER</span>{{' '.Support::show($voucher,'name')}}</td>

                                    <?php 
                                        if($voucher->type_code == \App\Helpers\VoucherConstant::TYPE_CODE_ALL_PRODUCT){
                                            if($voucher->type_voucher == \App\Helpers\VoucherConstant::TYPE_VOUCHER_SALE){
                                                if($voucher->type_discount == App\Helpers\VoucherConstant::TYPE_DISCOUNT_REDUCE){
                                                    $value = 'Mua tối thiểu '.Currency::showMoney($voucher->min_value_order).' để được giảm '.Currency::showMoney($voucher->discount);
                                                }else{
                                                    $value = 'Mua tối thiểu '.Currency::showMoney($voucher->min_value_order).' để được giảm '.$voucher->discount.' %';
                                                }
                                            }else{
                                                $value = 'Mua tối thiểu '.Currency::showMoney($voucher->min_value_order).' để được hoàn '.$voucher->discount.' % JCOIN';
                                            }
                                        }else{
                                            $sale = [];
                                            $sale['type'] = 'voucher';
                                            $sale['id'] =  $voucher_id;
                                            $order_details = Support::findProductSale($sale,$order); 
                                            foreach($order_details as $order_detail){
                                                $product = Support::getProduct($order_detail->product_id);
                                                if($voucher->type_voucher == \App\Helpers\VoucherConstant::TYPE_VOUCHER_SALE){
                                                    if($voucher->type_discount == App\Helpers\VoucherConstant::TYPE_DISCOUNT_REDUCE){
                                                        $value[$key] = 'Sản phẩm <b style="color:#fa4410">'.$product->name.'</b> áp dụng Voucher được giảm '.Currency::showMoney($voucher->discount);
                                                    }else{
                                                        $value[$key] = 'Sản phẩm <b style="color:#fa4410">'.$product->name.'</b> áp dụng Voucher được giảm '.$voucher->discount.' %';
                                                    }
                                                }else{

                                                    $value[$key] = 'Sản phẩm <b style="color:#fa4410">'.$product->name.'</b> áp dụng Voucher được hoàn '.$voucher->discount.
                                                    ' % JCOIN';
                                                }
                                            }
                                        }
                                    ?>

                                    @if($voucher->type_code == \App\Helpers\VoucherConstant::TYPE_CODE_ALL_PRODUCT)
                                        <td>
                                            {{$value}}
                                        </td>
                                    @else
                                        @foreach($order_details as $key => $order_detail)
                                        <td>
                                            {!!$value[$key]!!}
                                        </td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            <div class="items-order">
                <p class="title-table fs-24 fw-b mb-15">Sản phẩm trong hóa đơn</p>
                <div class="detailMain">
                    <table>
                        <thead>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Jcoin</th>
                            <th>Giá tiền</th>
                        </thead>
                        <tbody>
                            @foreach($order->orderProducts as $item)
                            <?php $product = $item->product ?>
                            <tr>
                                <td>
                                    <a href="{{Support::show($product,'slug')}}" target="_blank">
                                        <img src="{%IMGV2.product.img.390x0%}" alt="{%AIMGV2.product.img.alt%}" title="{%AIMGV2.product.img.title%}">
                                    </a>
                                    <a href="{{Support::show($product,'slug')}}" target="_blank">
                                        {{Support::show($product,'name')}}                              
                                    </a>
                                </td>
                                <td>{{Support::show($item,'qty')}}</td>
                                <td>{{Support::show($product,'jcoin') == null ? 0 : Support::show($product,'jcoin')}}</td>
                                <td>{{Currency::showMoney($item->price_at)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="total_money_order">
                <div class="detailMain">
                <p>Tiền tạm tính: {{Currency::showMoney($order->total)}}</p>
                <p>Số JCOIN sử dụng: {{$order->ecoin_use}}</p>
                <p>Số JCOIN được hoàn: {{$order->ecoin_plus}}</p>
                <p>Tổng tiền thanh toán: <b style="font-size: 18px">{{Currency::showMoney($order->total_final)}}</b></p>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="setting-order">
                <p class="title-table fs-24 fw-b mb-15">Cài đặt</p>
                <form action="/esystem/saveInfoOrder" class="filter-acceptance ajaxform" style="padding:10px" method="POST" accept-charset="UTF-8" data-success="CALLBACK_AJAX.callBack">
                    @csrf
                    <input type="hidden" name="order_id" value="{{Support::show($order,'id')}}">
                    <label>Thời gian lắp đặt dự kiến</label>
                    <input type="text" name="scheduled_installation_date" class="single-date" value="{{date('d/m/Y',strtotime($order->scheduled_installation_date))}}">
                    <label>Phí lắp đặt </label>
                    <input type="text" name="installation_fee" value="{{ $order->installation_fee > 0 ? $order->installation_fee : 0 }}">
                    <button>Lưu</button>
                </form> 
            </div>
            <div class="status-order">
                <p class="title-status fs-24 fw-b mb-15">Lịch sử đơn hàng</p>
                <div class="detailMain">
                    <p>Đơn hàng mới: {{Support::showDateTime($order->created_at)}}</p>
                    @if(Support::show($order,'status') == OrderHelper::STATUS_COMPLETED_INSTALL || Support::show($order,'status') == OrderHelper::STATUS_TIME_TO_EXCHANGE_EXPIRED)
                        <p>
                            Đơn hàng đã hoàn tất : {{Support::showDateTime($order->updated_at)}}
                        </p>
                    @endif
                </div>
            </div>
            <?php $asks = $order->asks ?>
            @if($asks->count() > 0)
            <div class="question-order">
                <p class="title-status fs-24 fw-b mb-15">Câu hỏi về đơn hàng</p>
                @foreach($asks as $item)
                <div class="question">
                    <p> Khách hàng: 
                        {{$item->user->name}}
                    </p>
                    <p>Kiểu tài khoản: {{OrderHelper::getRoleAccount($item->role)}} </p>
                    <p>Câu hỏi: <b>{{Support::show($item,'content')}}</b></p>
                    @if($item->act == 1)
                        <a style="pointer-events:none">Đã trả lời</a>
                    @else
                    <a class="rep_ask">Trả lời</a>
                    <form action="/esystem/rep-ask" class="ajaxform ask" method="POST">
                        @csrf
                        <input type="hidden" name="ask_id" value="{{Support::show($item,'id')}}">
                        <input type="textarea" name="content" id="" placeholder="Câu trả lời" >
                        <button> Trả lời</button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
