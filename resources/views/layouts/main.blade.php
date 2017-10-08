<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials._head')
    @include('partials._style')
    @yield('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div id="wrapper">

    @include('partials._nav')

        <div id="page-wrapper">
            <div class="container-fluid">
                        @yield('content')
            </div>
        </div>
    {{--@include('partials._footer')--}}
    {{--@include('partials._logout')--}}


    </div>
    @include('partials._javascript')
    @yield('script')
</body>

</html>