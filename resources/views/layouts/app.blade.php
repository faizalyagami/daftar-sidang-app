<!DOCTYPE html>
<html lang="id">
@include('layouts.header')

<body>
    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.navbar')

        <div class="content-wrapper">
            @include('components.alerts')
            @yield('content')
        </div>
    </div>

    @include('layouts.footer')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>
