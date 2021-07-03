@extends('vh::master')
@section('content')
@include('vh::static.headertop')
<div id="maincontent">
	<form action="{{$admincp}}/editRobot" method="post">
	{{csrf_field()}}
	<div class="sysedit row">
		<div class="col-md-11">
			<h3 class="title">Robots.txt</h3>
		</div>
		<div class="col-md-1">
			<button class="btn bgmain viewsite clfff">Update</button>
		</div>
		<div class="col-xs-12">
		<textarea class="robots"  name="content">{{$content}}</textarea>
		</div>
	</div>
	</form>
@include('vh::static.footer')
</div>
@stop