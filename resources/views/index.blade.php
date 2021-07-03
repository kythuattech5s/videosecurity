<!DOCTYPE html>
<html itemscope="" itemtype="http://schema.org/WebPage" lang="vi">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {!!\vanhenry\helpers\helpers\SEOHelper::HEADER_SEO(@$currentItem?$currentItem:NULL)!!}
    <base href="{{url('/')}}">
    @yield('css')
    <script type="text/javascript">
        var messageNotify = "{{Session::get('messageNotify', '')}}";
        var typeNotify = "{{Session::get('typeNotify', '')}}";
    </script>
</head>
<body class="scrollstyle">
    @if (!isset($onlyShowContent))
        @include('header')
    @endif
    @yield('content')
    @if (!isset($onlyShowContent))
        @include('footer')
    @endif
    @yield('js')
</body>
</html>
