@extends('layouts.app')
@section('title', 'Cuestionarios')

@section('content')

    <div class="container px-2 py-4">
        <div class="row">
            <div class="col-sm" style="">
                <div class="card border-primary box-shadow mb-3">
                    <div class="text-center">
                        <img class=" img-fluid p-4" src="/img/icono-cantidad.png" class="card-img-top" alt="...">
                    </div>
                    <div class="card-body ">
                        <h5 style="height: 100px; "  class="card-title text-primary text-center">Cantidad de casos de tortura denunciados</h5>
                        <p class="display-4 card-text text-success text-shadow text-center">12</p>
                        <small class="text-primary text-center" > Fuente: Base de Datos de la Defensoría del Pueblo. (MARZO de 2023).</small>
                    </div>
                </div>
            </div>

            <div class="col-sm" style="">
                <div class="card border-primary box-shadow mb-3">
                    <div class="text-center">
                        <img class=" img-fluid p-4" src="/img/icono-porcentaje.png" class="card-img-top" alt="...">
                    </div>
                    <div class="card-body ">
                        <h5 style="height: 100px; "  class="card-title text-primary text-center">Porcentaje de hacinamiento en recintos penitenciarios en Bolivia</h5>

                        <p class="display-4 card-text text-success text-shadow text-center">-165%</p>
                        <small class="text-primary text-center" > Fuente: Base de Datos de Régimen Penitenciario de Bolivia. (MARZO de 2022)  </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm" style="">
                <div class="card border-primary box-shadow mb-3">
                    <div class="text-center">
                        <img class=" img-fluid p-4" src="/img/icono-porcentaje.png" class="card-img-top" alt="...">
                    </div>
                    <div class="card-body ">
                        <h5 style="height: 100px; "  class="card-title text-primary text-center">Porcentaje de detención preventiva</h5>

                        <p class="display-4 card-text text-success text-shadow text-center">65.67%</p>
                        <small class="text-primary text-center" > Fuente: Base de Datos de Régimen Penitenciario de Bolivia. (MARZO de 2022)  </small>
                    </div>
                </div>
            </div>

            <div class="col-sm" style="">
                <div class="card border-primary box-shadow mb-3">
                    <div class="text-center">
                        <img class=" img-fluid p-4" src="/img/icono-cantidad.png" class="card-img-top" alt="...">
                    </div>
                    <div class="card-body ">
                        <h5 style="height: 100px; "  class="card-title text-primary text-center">Cantidad de casos de muertes en centros penitenciarios </h5>
                        <ul class="list-group">
                            <li class="list-group-item py-1">Enfermedad: <strong>9</strong></li>
                            <li class="list-group-item py-1">Accidentes: <strong>0</strong></li>
                            <li class="list-group-item py-1">Suicidio: <strong>2</strong></li>
                            <li class="list-group-item py-1">Homicidio: <strong>1</strong></li>
                            <li class="list-group-item py-1">TOTAL: <strong>12</strong></li>
                        </ul>
                        <small class="text-primary text-center" > Fuente: Base de Datos de Régimen Penitenciario de Bolivia. (MARZO de 2022)  </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $('#btn_guarda_archivo').click(function (e) {
            $(this).addClass('d-none');
            $('#btn_cargando').removeClass('d-none');
        });
    </script>

    {{-- @if (Session::has('success'))
        <script>
            Swal.fire(
                '{{Session::get('success') }}',
            )
        </script>
    @endif
    @if(Session::has('warning'))
        <script>
            Swal.fire(
                '{{Session::get('warning') }}',
            )
        </script>
    @endif --}}
@endsection
