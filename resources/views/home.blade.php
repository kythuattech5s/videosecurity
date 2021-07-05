@extends('index')
@section('content')
@include('tvs::video_css')
@include('tvs::video_view',['video_info' => $itemNews->img])
@include('tvs::video_js')
@endsection