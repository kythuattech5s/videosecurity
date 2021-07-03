@extends('vh::master')
@section('content')
<div class="header-top aclr">
  <button class="nav-trigger pull-left" ></button>
  <div class="breadc pull-left">
    <i class="fa fa-home pull-left"></i>
    <ul class="aclr pull-left list-link">
      <li class="pull-left"><a href="{{$admincp}}">Trang chủ</a></li>
    </ul>
  </div>
  
  <a class="pull-right bgmain1 viewsite" target="_blank" href="">
    <i class="fa fa-external-link" aria-hidden="true"></i>
    <span  class="clfff">Xem website</span> 
  </a>
</div>
<div id="maincontent">
    <div id="mainedit" class="row">
    <div class="col-xs-12 p0">
      <p style="line-height: 40px;background: #e96a0c;text-align: center;color: #fff;">{{\Session::has("error")?\Session::get('error'):''}}</p>
      <div class="row m0 boxedit">
        <div class="col-xs-12">
        <h3 class="title-import">Lấy dữ liệu sản phẩm theo link sản phẩm trên www.digikey.com</h3>
        @if(\Session::has('typeNotify'))
          @if(\Session::get('typeNotify') == 'error')
          <div class="alert alert-danger">
            {{\Session::get('messageNotify')}}
          </div>
          @elseif(\Session::get('typeNotify') == 'success')
          <div class="alert alert-success">
            {{\Session::get('messageNotify')}}
          </div>
          @endif
        @endif
        <form action="esystem/crawl-excute" method="get">
          <div class="form-group">
            <p class="form-title" for="">Link sản phẩm cần lấy dữ liệu</p>
            <input type="text" name="link" placeholder="Link sản phẩm cần lấy dữ liệu" class="form-control">
          </div>
          <div class="form-group">
            <p class="form-title">Chọn danh mục cha</p>
            <div class="product_category">
              <?php
              \App\Helpers\Icgidi::recursivePc();
              ?>
            </div>
          </div>
          <div class="form-group">
            <p class="form-title">Số sản phẩm trong kho</p>
            <div class="storage">
              <input type="number" name="storage" min="1" value="1" placeholder="Số sản phẩm trong kho">
            </div>
          </div>
          <div class="form-group">
            <p class="form-title">Giá</p>
            <div class="price">
              <input type="number" name="price" min="0" value="0" placeholder="Giá sản phẩm">
            </div>
          </div>
          <div class="form-group">
            <button class="form-control btn btn-primary bgmain " type="submit">Lấy dữ liệu</button>
          </div>  
        </form>
        </div>
      </div>
    </div>
    </div>
    @include('vh::static.footer')
</div>
<style type="text/css">
  h3.title-import{
    text-align: center;
    font-size: 20px;
    text-transform: uppercase;
  }
  h3.title-import a{
    font-weight: bold;
  }
  .storage input, .price input {
    height: 34px;
    padding: 6px 12px;
    border: 1px solid #ccc;
    font-size: 13px;
  }
</style>
<script type="text/javascript">
$('form[action="esystem/crawl-excute"] button').click(function(event) {
  $(this).css('pointer-events', 'none');
  $(this).text('Đang xử lý...');
});
</script>
@stop