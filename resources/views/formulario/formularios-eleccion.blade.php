@extends('layouts.app')
@section('title', 'Formularios')

@section('content')
@php
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
    $EST_id= session('EST_id');
@endphp

    <div class="text-center" >
        <h2 class="text-primary fs-2">Formularios</h2>
    </div>
    {{-- @dump($formularios) --}}
    <div class="container row p-0">
{{-- @dump($errors) --}}
        <div class="col-md">
            <div class="card mb-3">
                <div class="card-header bg-primary text-light text-shadow">NUEVO FORMULARIO</div>
                <div class="card-body ">
                    <div class="my-3">
                        <label class="form-label"><b>Visita:</b></label>
                        <input type="text" class="form-control" disabled value="{{$VIS_tipo}}">
                    </div>
                    <div class="my-3">
                        <label class="form-label"><b>Nombre del establecimiento:</b></label>
                        <input type="text" class="form-control" disabled value="{{$EST_nombre}}">
                    </div>
                    <div class="my-3">
                        <label class="form-label"><b>Tipo de establecimiento:</b></label>
                        <input type="text" class="form-control" disabled value="{{$TES_tipo}}">
                    </div>
                    <form method="Post" id="" action="{{route('formulario.nuevo')}}">
                        @csrf
                        <input type="hidden" name="TES_tipo" value="{{$TES_tipo}}">
                        <input type="hidden" name="EST_nombre" value="{{$EST_nombre}}">
                        <input type="hidden" name="EST_id" value="{{$EST_id}}">
                        <div class="my-3">
                            <label class="form-label"><b>Seleccione una opcion:</b></label>
                            <select class="form-select" name="opcion" id="opcion">
                                <option value="" selected >...</option>
                                <option value="nuevo" >Crear nuevo formulario</option>
                                {{-- <option value="anterior">Formulario nuevo a partir de uno anterior</option> --}}
                                <option value="asignar">Asignar formulario a ésta visita</option>
                            </select>
                            @error('opcion')
                                <i class="bi bi-info-circle text-danger"> </i><small class="text-danger">{{$message}}</small>
                            @enderror
                            <br>
                            @error('FRM_id')
                                <i class="bi bi-info-circle text-danger"> </i><small class="text-danger">{{$message}}</small>
                            @enderror
                            <br>
                            @error('nuevo_formulario')
                                <i class="bi bi-info-circle text-danger"> </i><small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="my-3 d-none" id="nuevo_formulario">
                            <label class="form-label"><b>Ingrese el nombre del nuevo formulario:</b></label>
                            <input type="text" class="form-control" name="nuevo_formulario" id="input_nuevo_formulario" list="lista_formularios" autocomplete="off">
                            <datalist id="lista_formularios"> </datalist>
                        </div>

                        <div class="my-3 d-none" id="formularios">
                            <label class="form-label"><b>Seleccione un formulario:</b></label>
                            <select class="form-select " name="FRM_id" >
                                <option value="" selected >...</option>
                                @foreach ($formularios as $formulario)
                                    <option value="{{$formulario->FRM_id}}"  >{{$formulario->FRM_titulo}}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-center my-3"> <button class="btn btn-success btn-lg text-shadow box-shadow"  type="submit">Aceptar</button></p>
                    </form>


                </div>
            </div>
        </div>
        {{-- @dump($formularios) --}}


    </div>

    <script type="text/javascript">
        $(document).ready( function () {

            $('#opcion').change(function (e) {
                e.preventDefault();
                if( $(this).val() == 'anterior' || $(this).val() == 'asignar' ){
                    $('#nuevo_formulario').addClass('d-none').hide().fadeIn(600);
                    $('#formularios').removeClass('d-none').hide().fadeIn(600);
                    // alert(id);
                }else if( $(this).val() == 'nuevo' ){
                    $('#formularios').addClass('d-none').hide().fadeIn(800);
                    $('#nuevo_formulario').removeClass('d-none').hide().fadeIn(600);
                }
            });

            // $('#nuevo_formulario').change(function (e) {
            //     e.preventDefault();
            // });
            $('#input_nuevo_formulario').on('input', function(){
                // Obtener el valor del input
                var busqueda = $(this).val();

                // Verificar si la longitud de la búsqueda es mayor a 5 caracteres
                if (busqueda.length > 5) {

                    $.ajax({
                        url: "{{ route('formulario.sugerenciasFormularios') }}",
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        method: 'POST',
                        // data: { q: q },
                        // dataType: 'json',
                        data: {nuevo_formulario: busqueda},
                        success: function(response){
                            // Limpiar resultados anteriores
                            $('#lista_formularios').empty();
                            // Mostrar resultados
                            $.each(response, function(index, sugerencias){
                                // console.log(sugerencias.FRM_titulo);
                                $('#lista_formularios').append('<option value="'+sugerencias.FRM_titulo+'">');
                            });
                        }
                    });
                }
            });

        })
    </script>

@endsection
