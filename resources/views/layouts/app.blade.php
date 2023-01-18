<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        {{-- <meta charset="utf-8"> --}}
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <title>.:MNP:.</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">

        <!-- Styles -->
        <link rel=StyleSheet href="/bootstrap5/css/custom3.css" type="text/css">
        <link rel=StyleSheet href="/bootstrap5/css/custom2.css" type="text/css">
        {{-- <script
            src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
            crossorigin="anonymous">
        </script> --}}

        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel=StyleSheet href="/DataTables/datatables.min.css" type="text/css">
        <script src="/DataTables/datatables.min.js"></script>
        <script src="/js/custom.js"></script>

        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>


    </head>
    <body class="font-sans antialiased bg-secondary bg-gradient">
        {{-- <x-jet-banner /> --}}
        @livewire('navigation-menu')

        <!-- Page Heading -->
        <!-- <header class="d-flex py-3 bg-white shadow-sm border-bottom"> -->
            <!-- <div class="container"> -->
                {{-- {{ $header }} --}}
            <!-- </div> -->
        <!-- </header> -->

        <!-- Page Content -->
        <main class="container-lg my-5 bg-light bg-gradient pb-1">
            {{-- {{ $slot }} --}}
            @if ( isset($slot) )
                {{ $slot }}
            @else
                @yield('content')
            @endif
            <!-- Modal para la galeria de archivos individual -->
            <div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog modal-dialog-scrollable modal-xl ">
                    <div class="modal-content ">
                        <div class="modal-header" id="modal_header"><p class="p-0 modal-title"></p>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                        </div>
                        <div class="modal-body text-center" id="modal_body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @stack('modals')
        @livewireScripts
        @stack('js')
        <script>
            Livewire.on('success',(message)=>{
                Swal.fire(
                 '¡Correcto!',
                 message,
                 'success'
                )
            });

            Livewire.on('danger',(message)=>{
                Swal.fire(
                 '¡Error',
                 message,
                 'error'
                )
            });

            $(document).on('mouseover', 'ul#nav_2 li', function () {
                $(this).addClass('p-1 rounded-pill bg-secondary');
            });
            $(document).on('mouseleave', 'ul#nav_2 li', function () {
                $(this).removeClass('p-1 rounded-pill bg-secondary');
            });
        </script>
    <style>
        .text-shadow{
            text-shadow: 2px 0px 2px #3a3b3b;
        }
        .box-shadow{
            box-shadow: 1px 1px 3px black;
        }
    </style>
    </body>
</html>
