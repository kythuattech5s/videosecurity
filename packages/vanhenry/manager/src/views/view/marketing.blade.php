@extends('vh::admin_2')
@section('content')
<main>
     <section class="container-fluid">
         <div class="row">
             {{-- <div class="col-lg-12">
                 <div class="box-alls p-3 mb-3">
                     <div class="row">
                         <div class="col-lg-4">
                             <div class="items-events">
                                 <div class="img-items-events mb-2">
                                     <a href="#"><img src="/admin/theme_2/frontend/images/item-maketing-1.jpg" /></a>
                                 </div>
                                 <div class="intros-items-events">
                                     <p>
                                         <a href="#" class="title-items-maketting text-18">[Đăng ký tài khoản để được giảm giá từ 0 - 99%] - [0h00 10.10.2020 - 23h59 10.10.2020]</a>
                                         <span class="p-1 text-pinks bg-light text-12 ml-3">Sắp diễn ra</span>
                                     </p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4">
                             <div class="items-events">
                                 <div class="img-items-events mb-2">
                                     <a href="#"><img src="/admin/theme_2/frontend/images/item-maketing-1.jpg" /></a>
                                 </div>
                                 <div class="intros-items-events">
                                     <p>
                                         <a href="#" class="title-items-maketting text-18">[Đăng ký tài khoản để được giảm giá từ 0 - 99%] - [0h00 10.10.2020 - 23h59 10.10.2020]</a>
                                         <span class="p-1 text-pinks bg-light text-12 ml-3">Sắp diễn ra</span>
                                     </p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4">
                             <div class="items-events">
                                 <div class="img-items-events mb-2">
                                     <a href="#"><img src="/admin/theme_2/frontend/images/item-maketing-1.jpg" /></a>
                                 </div>
                                 <div class="intros-items-events">
                                     <p>
                                         <a href="#" class="title-items-maketting text-18">[Đăng ký tài khoản để được giảm giá từ 0 - 99%] - [0h00 10.10.2020 - 23h59 10.10.2020]</a>
                                         <span class="p-1 text-pinks bg-light text-12 ml-3">Sắp diễn ra</span>
                                     </p>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div> --}}
             <div class="col-12">
                 <div class="box-alls p-3">
                     <p class="title-in-box text-30 opensan-semi pb-3 mb-3 border-bottom">Marketing</p>
                     <div class="row">
                         <div class="col-lg-4">
                             <div class="items-maketing media rounded align-items-center mb-3">
                                 <div class="img-maketing-items rounded-circle d-flex align-items-center justify-content-center mr-3">
                                     <a href="{{$admincp.'/view/promotions'}}"><img src="/admin/theme_2/frontend/images/maketing-icon-1.png"></a>
                                 </div>
                                 <div class="media-body">
                                     <p><a href="{{$admincp.'/view/promotions'}}" class="title-all-maketing text-24 d-flex mb-3 opensan-semi">Chương trình giảm giá</a></p>
                                     <p>Công cụ tăng đơn hàng bằng cách tạo chương trình giảm giá</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4">
                             <div class="items-maketing media rounded align-items-center mb-3">
                                 <div class="img-maketing-items rounded-circle d-flex align-items-center justify-content-center mr-3">
                                     <a href="{{$admincp.'/view/vouchers'}}"><img src="/admin/theme_2/frontend/images/maketing-icon-2.png"></a>
                                 </div>
                                 <div class="media-body">
                                     <p><a href="{{$admincp.'/view/vouchers'}}" class="title-all-maketing text-24 d-flex mb-3 opensan-semi">Mã giảm giá</a></p>
                                     <p>Công cụ tăng đơn hàng bằng cách tạo chương trình giảm giá</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4">
                             <div class="items-maketing media rounded align-items-center mb-3">
                                 <div class="img-maketing-items rounded-circle d-flex align-items-center justify-content-center mr-3">
                                     <a href="{{$admincp.'/view/combos'}}"><img src="/admin/theme_2/frontend/images/maketing-icon-3.png"></a>
                                 </div>
                                 <div class="media-body">
                                     <p><a href="{{$admincp.'/view/combos'}}" class="title-all-maketing text-24 d-flex mb-3 opensan-semi">Combo khuyến mãi</a></p>
                                     <p>Công cụ tăng đơn hàng bằng cách tạo chương trình giảm giá</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4">
                             <div class="items-maketing media rounded align-items-center mb-3">
                                 <div class="img-maketing-items rounded-circle d-flex align-items-center justify-content-center mr-3">
                                     <a href="{{$admincp.'/view/deals'}}"><img src="/admin/theme_2/frontend/images/maketing-icon-4.png"></a>
                                 </div>
                                 <div class="media-body">
                                     <p><a href="{{$admincp.'/view/deals'}}" class="title-all-maketing text-24 d-flex mb-3 opensan-semi">Mua kèm deal sốc</a></p>
                                     <p>Công cụ tăng đơn hàng bằng cách tạo chương trình giảm giá</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-4">
                             <div class="items-maketing media rounded align-items-center mb-3">
                                 <div class="img-maketing-items rounded-circle d-flex align-items-center justify-content-center mr-3">
                                     <a href="{{$admincp.'/view/flash_sales'}}"><img src="/admin/theme_2/frontend/images/maketing-icon-5.png"></a>
                                 </div>
                                 <div class="media-body">
                                     <p><a href="{{$admincp.'/view/flash_sales'}}" class="title-all-maketing text-24 d-flex mb-3 opensan-semi">Flash sale của shop</a></p>
                                     <p>Công cụ tăng đơn hàng bằng cách tạo chương trình giảm giá</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </section>
 </main>
@endsection
