@extends('vh::master')
@section('content')
@include('vh::static.headertop')
<div id="maincontent">
	<h3>{{\Session::has('status')?\Session::get('status'):''}}</h3>
	<div class="sysedit row">
		<div class="col-md-12">
			<div class="col-xs-10"><h3 class="title">Sitemap</h3></div>
			<div class="col-xs-2 textcenter">
				<button data-type="3" class="updateall">Cập nhật tất cả</button>
			</div>
		</div>
		@foreach($listSitemaps as $sitemap)
		<div class="col-xs-12 item-sitemap">
			<div class="col-xs-8">
				<h4>{{$sitemap->table}}</h4>
			</div>
			<div class="col-xs-2 textcenter">
				<button data-from="{{$sitemap->table}}" data-type="2" >Cập nhật tháng này</button>
			</div>
			<div class="col-xs-2 textcenter">
				<button data-from="{{$sitemap->table}}" data-type="1">Cập nhật tất cả</button>
			</div>
		</div>
		@endforeach
	</div>
<style type="text/css">
	.item-sitemap{
    background: #077737;
    color: #fff;
    text-transform: uppercase;
    margin: 3px 0px;
	}
	.item-sitemap button,.updateall{
		background: #ca6f12;
	    padding: 10px;
	    border: none;
	    text-transform: uppercase;
	    color:#fff;
	}
	.item-sitemap button:hover,.updateall:hover{
		background: #6f3a03;
	}
</style>
<script type="text/javascript">
	$(function() {
		$('.item-sitemap button,.updateall').click(function(event) {
			var frm = "<form action='{{$admincp}}/updateSitemap' method='post'>";
			frm += "<input type='hidden' name='_token' value='{{csrf_token()}}'/>";
			frm += "<input type='hidden' name='from' value='"+$(this).attr('data-from')+"'/>";
			frm += "<input type='hidden' name='type' value='"+$(this).attr('data-type')+"'/>";
			frm += "</form>";
			$(frm).appendTo('body').submit().remove();
		});
	});
</script>
@include('vh::static.footer')
</div>
@stop