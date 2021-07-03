@extends('vh::master')
@section('content')
@include('vh::static.headertop')
<div id="maincontent">
    @include('vh::static.notify')
    @include('vh::static.quickmenu')
    @include('vh::static.access')
    @include('vh::static.history')
    @include('vh::static.statis')
    @include('vh::static.footer')
</div>
@stop