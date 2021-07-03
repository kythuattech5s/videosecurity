@extends('vh::master')
@section('content')
    @isset($type)
        @include('vh::view.pages.'.$type)
    @else
        @include('vh::view.pages.all')
    @endif
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="admin/theme_2/frontend/css/daterangepicker.css">
    <link rel="stylesheet" href="admin/theme_2/frontend/css/char.css" />
    <link rel="stylesheet" href="admin/theme_2/frontend/css/style.css" />
@endsection
@section('js')
    <script src="admin/theme_2/frontend/js/Chart.js"></script>
    <script src="admin/theme_2/frontend/js/Chart.bundle.js"></script>
    <script type="text/javascript" src="admin/theme_2/frontend/js/moment.min.js"></script>
    <script type="text/javascript" src="admin/theme_2/frontend/js/daterangepicker.js"></script>
    <script src="admin/theme_2/frontend/js/script_chart.js"></script>
@endsection
