<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>:: MNP ::</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito';
            background: #f7fafc;
        }
    </style>
</head>
<body>
    <div class="container-fluid fixed-top p-4">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                @if (Route::has('login'))
                    <div class="">
                        @auth
                            <a href="{{ url('/panel') }}" class="text-muted">Panel</a>
                        @else
                            <a href="{{ route('login') }}" class="text-muted">Ingresar</a>
                            {{-- <a href="{{ route('acceso') }}" class="text-muted">Ingresar</a> --}}
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container-fluid my-5 pt-5 px-5" >
        <p class=" text-center px-4 ">
            <img class="img-fluid"  src="img/logoinicio.png" alt="Defensor del Pueblo" >
        </p>
    </div>
</body>
</html>
