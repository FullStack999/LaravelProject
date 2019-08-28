<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Industry') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('/js/plugins.js') }}"></script>

</head>
<body id="page-top">

@yield('content')

@if(Auth::check())
    <div class="content-wrapper">
        @include('layouts.sidebar')
        <div id="content">

        </div>
    </div>

    @yield('user1')
    @yield('user2')
    @yield('user3')
    @yield('user4')
    @yield('user5')
    @yield('user6')
    @yield('user7')
    @yield('admin')
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fa fa-angle-up"></i>
    </a>


    {{--Notifications modal--}}

    <div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <h5 class="modal-body"></h5>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Դուրս գալ համակարգից</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Ոչ</button>
                    <a id="logout" class="btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        Այո
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Scripts -->
<script src="{{ asset('/js/fontawesome-all.min.js') }}"></script>
<script src="{{ asset('/js/app.js') }}"></script>
</body>
</html>