@extends('layouts.app')
@section('title', 'Formularios')

@section('content')
@php
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
    $VIS_tipo = session('VIS_tipo');
@endphp

    <div class="text-center" >
        <h2 class="text-primary fs-2">Formularios</h2>
    </div>
    {{-- @dump($formularios) --}}
    <div class="container row p-0">
        <form method="Post" id="frm_formulario_nuevo" action="/formulario/store">
        </form>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-light text-shadow">NUEVO FORMULARIO</div>
                <div class="card-body text-primary">
                    <div class="my-3">
                        <label for="inputPassword4" class="form-label">Nombre del establecimiento</label>
                        <input type="text" class="form-control" disabled value="{{$EST_nombre}}">
                    </div>
                    <div class="my-3">
                        <label for="inputPassword4" class="form-label">Tipo de establecimiento</label>
                        <input type="text" class="form-control" disabled value="{{$VIS_tipo}}">
                    </div>
                    <div class="my-3">
                        <label for="inputPassword4" class="form-label">Seleccione un formulario para asociar a la visita</label>
                        <select class="form-select" aria-label="Default select example">
                            <option selected>Seleccione una opción</option>
                            @foreach ($formularios as $formulario)
                                <option value="{{ $formulario['FRM_titulo'] }}">
                                    {{ $formulario['FRM_titulo'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-light text-shadow">FORMULARIOS ASOCIADOS</div>
                <div class="card-body text-primary">
                    :
                    <dl>
                        <dt>VISITA
                        <dd>Señórula montada en una escóbula
                        <dt>Oreja
                        <dd>Sesenta minutejos
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready( function () {
            $('#btn_formulario_nuevo').click(function (){
                $("#frm-formulario-nuevo")[0].reset();
            });

            $('#formularios #table_formularios_wrapper .row:first-child').append($('#div_formulario_nuevo'))
        })
    </script>

@endsection
