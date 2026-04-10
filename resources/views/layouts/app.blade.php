<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Font -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin 2 -->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}" type="image/x-icon">
    
</head>

<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->
    @include('layouts.sidebar')
    <!-- End Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <!-- Topbar -->
    @include('layouts.topbar')
            <!-- End Topbar -->

            <!-- Content -->
            <div class="container-fluid">
                @yield('content')
            </div>

        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto text-center">
                <span>© {{ date('Y') }} SMK Muhammadiyah Kandanghaur</span>
            </div>
        </footer>

    </div>

</div>

<!-- JS -->
<script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('scripts')
</body>
</html>