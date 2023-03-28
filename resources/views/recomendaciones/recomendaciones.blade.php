@extends('layouts.app')
@section('title', 'Cuestionario')


@section('content')
    @mobile
    <div class="container-fluid row border-top border-bottom p-3">
        <div class="col">
            <a class="text-decoration-none" href="/establecimientos/historial/{{ $establecimiento['EST_id'] }}" >
                <i class="bi bi-arrow-90deg-left"></i> Historial
            </a>
        </div>
        <div class="col">
            <a class="text-decoration-none" href="/formulario/adjuntos/{{ $establecimiento['EST_id'] }}" >
                <i class="bi bi-folder-symlink"></i> Archivos adjuntos
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
                    <a class="text-decoration-none" href="/establecimientos/historial/{{ $establecimiento['EST_id'] }}" >
                        <i class="bi bi-arrow-90deg-left"></i> Historial
                    </a>
                </li>
                {{-- <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/imprimir/{{$rec->FK_FRM_id}}" >
                        <i class="bi bi-printer"></i> Vista para imprimir formulario</span>
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/responder/{{$rec->FK_FRM_id}}" >
                        <i class="bi bi-ui-checks-grid"></i> Responder cuestionario
                    </a>
                </li> --}}
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/formulario/adjuntos/{{ $establecimiento['EST_id'] }} " >
                        <i class="bi bi-folder-symlink"></i> Archivos adjuntos
                    </a>
                </li>
                </ul>
            </div>
        </div>
    </nav>
    @enddesktop
        {{-- SUBMENU  --}}

    <h2 class="text-center py-2 text-primary">Historial de Observaciones</h2>
    <h3 class="text-center py-2 text-primary">{{ $establecimiento['EST_nombre']; }}</h3>

    @include('includes.recomendaciones', ['nueva_recomendacion' => true])



    <script>
        /* CODIGO PARA OBSERVACIONES/RECOMENDACIONES */

        /* Adiciona un input file + una descripcion para nuevo archivo o documento */
        let a=0
        $(document).on('click', '.nuevo-adjunto', function(){

                let id = $(this).attr('id').replace(/[^0-9]/g,'');

            $("#archivos").append('<fieldset id="archivos_'+a+'" class="hover border px-2 mx-2"><legend class="fs-6 float-none w-auto p-2">Archivos <i class="btn p-0 text-danger bi bi-trash eliminar-archivo" id="eliminar_archivo_'+a+'"></i></legend>   <input type="file" class="form-control col-sm" id="ARC_archivo_'+a+'" name="ARC_archivo[]" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture />     <p id="ARC_archivo_'+a+'_err" class="text-danger error col"></p><label for="formFileSm" class="form-label">Descripción:</label><input type="text" class="form-control" name="ARC_descripcion[]"><p id="ARC_descripcion_'+a+'_err" class="text-danger error col"></p></fieldset>');
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
                url: "/recomendaciones/nuevaRecomendacion",
                type: 'post',
                data: formData, //$(".form-recomendaciones").serialize(),
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
                    jQuery.each(data.errors, function( key, value ){
                        let error = ('#'+key+'_err').replace('.','_');
                        $(error).append( value );
                    });

                    /*valida input file vacio*/
                    $('#form_recomendaciones_1 :input[type="file"]').each(function () {
                        // console.log($(this).find('input [type=file]'));
                        $(this).empty();
                        if(!$(this).val() ){
                            console.log('vacio');
                            $('#'+$(this).attr('id')+'_err').append('El dato es necesario!');
                            // console.log( $('input[type="file"]').attr('name') );
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
                        // location.reload();
                    }
                    // window.location.reload();
                },
                error: function(response){ console.log(response) }
            });
        });

        /*agrega inputs para nuevos archivos*/
        let j=0;
        $(document).on('click', '.nuevo-archivo', function(){
            //let id = $(this).attr('id').replace(/[^0-9]/g,'');
            $(".archivos").append('<div class="input-group input-group-sm p-1 adjunto" id="adjunto_'+j+'"> <input type="file" class="form-control input-archivo" id="archivo_'+j+'" name="REC_archivo[]" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture /> <span class="input-group-text">Descripción:</span>  <input type="text" class="form-control" id="descripcion_'+j+'" name="ARC_descripcion[]">   <span class="input-group-text btn-danger text-light rounded remover-adjunto" id="remover_adjunto_'+j+'">    <i class="text-dark bi bi-trash"></i> </span>     <div class="container row">    <small class="text-danger col" id="archivo_'+j+'_err"></small><small class="text-danger col" id="descripcion_'+j+'_err"></small></div></div>');
            ++j;
        });

        /*Elimina el contenedor de archivos adjuntos*/
        $(document).on('click', '.remover-adjunto', function(){
            let id = $(this).attr('id').replace(/[^0-9]/g,'');
            $("#adjunto_"+id).remove();
        });

        /*Agrega nuevo cumplimiento*/
        function agregarCumplimiento( recomendacion, fecha, id ){
            $(".archivos").empty();
            $('textarea[name="REC_detallesCumplimiento"]').val('');
            $(".val-recomendacion, .val-fecha-recomendacion, .rec-id").empty();
            $(".val-recomendacion").append(recomendacion);
            $(".val-fecha-recomendacion").append(fecha);
            $(".rec-id").val(id);
        }

        /*Guarda el cumplimiento de una observación*/
        $(document).on('click', '#guardar_cumplimiento', function(e){
        // $("#guardar_cumplimiento").on('click', function(e){
            e.preventDefault();
            let formData = new FormData($('#recomendaciones_form')[0]);
            $.ajax({
                async: true,
                // headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: "/recomendaciones/cumplimiento",
                type: 'POST',
                data: formData,//$('#recomendaciones_form').serialize(),
                contentType: false,
                processData: false,
                beforeSend: function () {
                    /*laravel solo valida una campo de imagen. Entonces La validación se hace manualmente*/
                    if ( $('.adjunto').length ) {
                        let vacio;
                        $("#recomendaciones_form :input").each(function () {
                            let id = $(this).attr('id');
                            $('#'+id+'_err').empty();
                            if ( $('#'+id ).val() == '' ) {
                                // console.log('vacio');
                                $('#'+id+'_err').append('El dato es necesario!');
                                vacio = true;
                            }else{
                                $('#'+id+'_err').empty();
                            }
                        });
                        if (vacio) {
                            return false;
                        }
                    }
                },
                success: function (data, response) {
                    $('small.error').empty();
                    // Muestra los errores de validacion
                    console.log(data.errors);
                    jQuery.each(data.errors, function(key, value){
                        // console.log(key, value);
                        $('#'+key+'_err').append( '<p>'+value+'</p>' );
                    });
                    if(data.success){
                        $('#guardar_cumplimiento').addClass('d-none');
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
                error: function(response){  }
            });
        });

    </script>
@endsection

