<button class="closeOderDetail"><i class="fa fa-times" aria-hidden="true"></i></button>
<div class="orderDetail">
    <p class="title-order">Chi tiết sản phẩm đặt trước {{Support::show($pre_order->product,'name')}}</p>
    <div class="row">
        <div class="col-lg-9">
            <p class="fs-24 fw-b mb-15">Thông tin đơn đặt hàng</p>
            <div class="detailMain">
                <div class="status-pre-order">
                    <div class="info-order-detail">Tình trạng: 
                        <span class="@if(support::show($pre_order,'status') == 2) order-success @else order-code @endif">{{Support::getStatusPreOrder(support::show($pre_order,'status'))}}
                        </span>
                    </div>
                    @if(Support::show($pre_order,'notification_have_product') == 1)
                        @if(Support::show($pre_order,'status') == 2)
                        <div class="button-sent-mail-customer">
                            <button class="sent_has_product" data-pre-order-id="{{Support::show($pre_order,'id')}}" data-url="/esystem/sentMailHaveProduct">Gửi thông báo có hàng</button>
                        </div>
                        @endif
                    @else
                        <div class="button-sent-mail-customer">
                            <button class="order-success" style="pointer-events: none">Đã gửi thông báo có hàng</button>
                        </div>
                    @endif
                </div>
            </div>
            <div class="info-user-order">
                <p class="title-info fs-24 fw-b mb-15">Thông tin khách hàng</p>
                <div class="detailMain">
                    <p>Họ và tên: {{Support::show($pre_order,'name')}}</p>
                    <p>Địa chỉ nhận hàng: 
                        {{
                            Support::show($pre_order,'address')
                            .' - '.Support::getNameOrderAddressDetail($pre_order,'district')
                            .' - '.Support::getNameOrderAddressDetail($pre_order,'province') 
                        }}
                    </p>
                    <p>Số điện thoại: {{Support::show($pre_order,'phone')}}</p>
                    <p>Email: {{Support::show($pre_order,'email')}}</p>
                    @if(Support::show($pre_order,'content') !== null)
                        <p>Lời nhắn: <b style="color:#fa4410">{{Support::show($pre_order,'content')}}</b></p>
                    @endif
                </div>
            </div>
            <div class="items-order">
                <p class="title-table fs-24 fw-b mb-15">Sản phẩm đặt hàng</p>
                <div class="detailMain">
                    <table>
                        <thead>
                            <th>Sản phẩm</th>           
                            <th>Số lượng</th>
                            <th>Jcoin</th>
                            <th>Giá đặt cọc</th>         
                        </thead>
                        <tbody>
                            <?php $product = $pre_order->product ?>
                            <tr>
                                <td>
                                    <a href="{{Support::show($product,'slug')}}" target="_blank">
                                        <img src="{%IMGV2.product.img.390x0%}" alt="{%AIMGV2.product.img.alt%}" title="{%AIMGV2.product.img.title%}">
                                    </a>
                                    <a href="{{Support::show($product,'slug')}}" target="_blank">
                                        {{Support::show($product,'name')}}                              
                                    </a>
                                </td>
                                <td>{{Support::show($pre_order,'qty')}}</td>
                                <td>{{Support::show($product,'jcoin') == null ? 0 : Support::show($product,'jcoin')}}</td>
                                <td>{{Support::show($pre_order,'price')}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="status-order">
                <p class="title-status fs-24 fw-b mb-15">Lịch sử đặt hàng</p>
                <div class="detailMain">
                    <p>Đặt hàng lúc: {{Support::showDateTime($pre_order->created_at)}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
