@extends('vh::master')
@section('content')
	@switch('$type')
		@case('hotProduct')
			@include('vh::view.pages.hotProduct',['data'=>$data])
		@break
		@case('byTime')
			@include('vh::view.pages.byTime',['data'=>$data])
		@break
		@case('byMadeIn')
			@include('vh::view.pages.byMadeIn',['data'=>$data])
		@break
		@case('byCode')
			@include('vh::view.pages.byCode',['data'=>$data])
		@break
		@case('byUser')
			@include('vh::view.pages.byUser',['data'=>$data])
		@break
		@case('byStaff')
			@include('vh::view.pages.byStaff',['data'=>$data])
		@break
		@case('refundByProduct')
			@include('vh::view.pages.refundByProduct',['data'=>$data])
		@break
		@default
			@include('vh::view.pages.refundByCode',['data'=>$data])
	@endswitch
@endsection
@section('css')
    <link rel="stylesheet" href="/admin/theme_2/frontend/css/char.css" />
    <link rel="stylesheet" href="/admin/theme_2/frontend/css/style.css" />
@endsection
@section('js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>
	<script src="/admin/theme_2/frontend/js/script_chart.js"></script>
@endsection