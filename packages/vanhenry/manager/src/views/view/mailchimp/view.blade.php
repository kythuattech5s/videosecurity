@extends('vh::master')
@section('content')
@include('vh::static.headertop')
<div id="maincontent">
	<div class="row">
		<div class="col-lg-6">
			@foreach($allCampaigns as $item)
			<div class="mailchimp_item">
				<p>{{$item->type}}</p>
			</div>
			@endforeach
		</div>
		<div class="col-lg-6">
			@foreach($allTemplates as $item)
			<div class="mailchimp_item">
				<p>{{$item->name}}</p>
				<p><span>Tạo bởi: </span><strong>{{$item->created_by}}</strong></p>
				<a href="https://us1.admin.mailchimp.com/templates/edit?id={{$item->id}}" title="" class="smooth" target="_blank">Sửa</a>
				<a href="https://us1.admin.mailchimp.com/templates/edit?id={{$item->id}}" title="" class="smooth" target="_blank">Xóa</a>
			</div>
			@endforeach
		</div>
	</div>
	@include('vh::static.footer')
</div>
@stop