<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '.:MNP:.') }}</title>

    <link rel=StyleSheet href="/bootstrap5/css/bootstrap.css" type="text/css">
    <link rel=StyleSheet href="/bootstrap5/css/custom.css" type="text/css">
    <script src="/bootstrap5/js/jquery.min.js"></script>
    <script src="/bootstrap5/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">

    <link rel=StyleSheet href="/DataTables/datatables.min.css" type="text/css">
    <script src="/DataTables/datatables.min.js"></script>

    <link rel=StyleSheet href="/filterableSelectBox/jquery-editable-select.min.css" type="text/css">
    <script src="/filterableSelectBox/jquery-editable-select.min.js"></script>

</head>
<body class="font-sans antialiased bg-secondary bg-gradient">
    {{-- @include('menu') --}}
    @livewire('navigation-menu')
    <main class="container-xl my-5 bg-light bg-gradient">
        @yield('content')
    </main>
</body>
</html>
