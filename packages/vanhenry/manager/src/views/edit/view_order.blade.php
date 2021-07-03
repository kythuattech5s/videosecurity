@extends('vh::master')
@section('content')
<?php $tableMap = $tableData->get('table_map',''); ?>
<input class="one hidden" dt-id="{{FCHelper::er($dataItem,'id')}}" ><!--Lưu id để xóa-->
<div class="header-top aclr">
	<button class="nav-trigger pull-left" ></button>
	<div class="breadc pull-left">
		<i class="fa fa-comments pull-left"></i>
		<ul class="aclr pull-left list-link">
			<li class="pull-left"><a href="{{$admincp}}/view/{{$tableMap}}">{{$tableData->get('name','')}}</a></li>
		</ul>
	</div>
	<a class="pull-right bgmain1 viewsite" href="{{base64_decode(\Request::input('returnurl'))}}">
	    <i class="fa fa-backward" aria-hidden="true"></i>
	    <span  class="clfff">Back</span> 
	</a>
</div>
<?php 
if($actionType=='edit'){
	$actionAjax = "$admincp/update/".$tableMap."/".FCHelper::er($dataItem,'id');
	$actionNormal = "$admincp/save/".$tableMap."/".FCHelper::er($dataItem,'id')."?returnurl=".Request::input('returnurl');  
}
else{
	$actionAjax = "$admincp/storeAjax/".$tableMap;
	$actionNormal = "$admincp/store/".$tableMap."?returnurl=".Request::input('returnurl'); 
}
?>
<style type="text/css">
table.table, table th,table tr, table tr td{
	border-collapse: collapse;
	border: 1px solid #cbcbcb !important;
}
table.infsum tr td:nth-child(2n+1){
	background: #00923f;
	color: #fff;
}
</style>
<style type="text/css">
table#order_pcb{
	width: 100%;
	border-collapse: collapse;
	border: 1px solid #ccc;
}
table#order_pcb tr, table#order_pcb td{
	border-collapse: collapse;
	border: 1px solid #ccc;
	padding: 10px;
}
button.export-order-pdf {
    border: none;
    padding: 0px 10px;
    background: green;
    color: #fff;
    height: 30px;
    line-height: 30px;
    margin-bottom: 10px;
    -webkit-transition: all 0.3s ease 0s;
    -moz-transition: all 0.3s ease 0s;
    -ms-transition: all 0.3s ease 0s;
    -o-transition: all 0.3s ease 0s;
    transition: all 0.3s ease 0s;
}
button.export-order-pdf:hover, .export-order-pdf.active {
	background: #E96A0C;
}
.export-order-pdf.active {
	pointer-events: none;
}
</style>
<div id="maincontent">
	<form action="{{$actionNormal}}" dt-ajax="{{$actionAjax}}" dt-normal="{{$actionNormal}}" method="post" id="frmUpdate">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="tech5s_controller" value="{{$tableData->get('controller','')}}">
		<div id="mainedit" class="row">
			<div class="col-xs-12 p0 table-responsive" style="background: #fff">
				@include('vh::pdf_view', ['notUserFont' => true])
					<button class="export-order-pdf" data-id="{{$dataItem->id}}"><i class="fa fa-file-pdf-o"></i> Gửi đơn hàng dưới dạng pdf cho khách hàng</button>
				</div>
			</div>
		</form>
		@include('vh::static.footer')
	</div>
	<script type="text/javascript">
		$(function() {
			$('select.status[name=status]').change(function(event) {
				var _this = $(this);
				var status = _this.val();
				var proId = _this.data('pro');
				var orderId = _this.data('order');
				$.ajax({
					url: 'esystem/update-status',
					dataType: 'json',
					global: false,
					data: {status: status, pro_id: proId, order_id: orderId}
				})
				.done(function(json) {
					if(json.code == 200){
						/*_this.find('option[value='+status+']').attr('selected', true);*/
						$.simplyToast(json.message,'success');
					}
					else $.simplyToast(json.message,'danger');
				})
			});
			$('.export-order-pdf').click(function(event) {
				event.preventDefault();
				var _this = $(this);
				var textButton = _this.text();
				$.ajax({
					url: 'esystem/export-pdf/'+$(this).data('id'),
					global: false,
					dataType: 'json',
					beforeSend: function(){
						_this.text('Đang xử lý...');
						_this.addClass('active');
					}
				})
				.done(function(json) {
					if(json.code == 200){
						$.simplyToast(json.message,'success');
					}
					else{
						$.simplyToast(json.message,'danger');
					}
					_this.text(textButton);
					_this.removeClass('active');
				})
			});
		});
	</script>
	@stop