@extends('layouts.app')
@section('title', 'Cuestionario')
@php
    $EST_id = session('EST_id');
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
@endphp

@section('content')
    @mobile
    <div class="container-fluid row border-top border-bottom p-3">
        <div class="col">
            <a class="text-decoration-none" href="/visita/historial/{{ $EST_id }}" >
                <i class="bi bi-arrow-90deg-left"></i> Página anterior
            </a>
        </div>
    </div>
    @endmobile
    @desktop
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                    <li class="nav-item p-1 px-3">
                        <a class="text-decoration-none" href="/visita/historial/{{  $EST_id }}" >
                            <i class="bi bi-arrow-90deg-left"></i> Página anterior
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    @enddesktop
        {{-- SUBMENU  --}}

    <h2 class="text-center py-2 text-primary">Historial de Observaciones</h2>
    <h3 class="text-center py-2 text-primary">{{ $TES_tipo. ': '. $EST_nombre; }}</h3>

    @include('recomendaciones.nueva_recomendacion', ['nueva_recomendacion' => true])

    <script>
        /* CODIGO PARA OBSERVACIONES/RECOMENDACIONES */

        /* Adiciona un input file + una descripcion para nuevo archivo o documento */
        let a=0
        $(document).on('click', '.nuevo-adjunto', function(){
            var id = $(this).attr('id').replace(/[^0-9]/g,'');

            $("#archivos").append(`
            <fieldset id="archivos_`+a+`" class="hover border px-2 mx-2">
                <legend class="fs-6 float-none w-auto p-2">Archivos <i class="fs-4 btn p-0 text-danger bi bi-trash eliminar-archivo" id="eliminar_archivo_`+a+`"></i></legend>
                <input type="file" class="form-control col-sm" id="ARC_archivo_`+a+`" name="ARC_archivo[]" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture />
                <p id="ARC_archivo_`+a+`_err" class="text-danger error col"></p>
                <label for="formFileSm" class="form-label">Descripción:</label>
                <input type="text" class="form-control" name="ARC_descripcion[]">
                <p id="ARC_descripcion_`+a+`_err" class="text-danger error col"></p>
            </fieldset>`);
                $('#archivos_'+a).hide().slideDown(800);
                ++a;
        });

        // Elimina un archivo
        $(document).on('click', '.eliminar-archivo', function(e){
            let id = $(this).attr('id').replace(/[^0-9]/g,'');
            console.log(id);
            $("#archivos_"+id).slideToggle(500, function()
            { $("#archivos_"+id).remove();  });
        });

        // Guardar nueva recomendación
        $(document).on('click', '.nueva-recomendacion', function(e){
            let id = $(this).attr('id').replace(/[^0-9]/g,'');
            e.preventDefault();
            var formData = new FormData( $('#form_recomendaciones_'+id)[0] );
            // console.log(formData);
            $.ajax({
                async: true,
                // headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: "/recomendaciones/guardarNuevaRecomendacion",
                type: 'post',
                data: formData,
                beforeSend: function () {
                    $('#cargando_'+id).removeClass('d-none');
                    $('#guardar_recomendacion_'+id).addClass('d-none');
                },
                processData: false,
                contentType: false,
                success: function (data, response) {
                    $('#cargando_'+id).addClass('d-none');
                    $('#guardar_recomendacion_'+id).removeClass('d-none');

                    $('small.error').empty();
                    $('p.error').empty();
                    jQuery.each(data.errors, function( key, value ){
                        let error = ('#'+key+'_err').replace('.','_');
                        $(error).append( value );
                    });

                    /*valida input file vacio*/
                    $('#form_recomendaciones_1 :input[type="file"]').each(function () {
                        // console.log($(this).find('input [type=file]'));
                        if(!$(this).val() ){
                            console.log( $(this).attr('id') );
                            $('#'+$(this).attr('id')+'_err').append('El dato es necesario!');
                            //console.log( $('input[type="file"]').attr('name') );
                        }else{
                            console.log('sss');
                        }
                    });

                    if(data.success){
                        $('#guardar_recomendacion_'+id).addClass('d-none');
                        Swal.fire({
                            text: data.success,
                            icon: 'success',
                            confirmButtonText: 'ok!'
                            }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload()
                            }
                        });
                    }
                },
                error: function(response){ console.log(response) }
            });
        });





    </script>
@endsection

