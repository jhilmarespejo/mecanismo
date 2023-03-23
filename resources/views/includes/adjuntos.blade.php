
{{-- @section('content') --}}
@php
// $id = null; $elemento = []; $img = []; $i=0;
// foreach($adjuntos as $k=>$adjunto){
//     if( $id != $adjunto->ADJ_id ){
//         array_push($elemento, ['ADJ_id' => $adjunto->ADJ_id, 'FK_FRM_id' => $adjunto->FK_FRM_id, 'ADJ_titulo' => $adjunto->ADJ_titulo, 'ADJ_fecha' => $adjunto->ADJ_fecha, 'ADJ_responsables' => $adjunto->ADJ_responsables, 'ADJ_entrevistados' => $adjunto->ADJ_entrevistados, 'ADJ_resumen' => $adjunto->ADJ_resumen, 'FRM_titulo' => $adjunto->FRM_titulo, 'EST_nombre' => $adjunto->EST_nombre]);
//     }
//     $id=$adjunto->ADJ_id;
// }
// dump( $elemento);//exit;
@endphp

<div class="container p-sm-5 p-0" id="div_adjuntos">

    @if( count($adjuntos) == 0 )
    <div class="alert alert-danger mx-5 mt-2 text-center" role="alert">
        Aún no se asignaron archivos adjuntos a este establecimiento
    </div>
    @endif

    <form action="{{ route('formulario.adjuntosNuevo') }}" id="nuevo_adjunto_form" enctype="multipart/form-data">
        <div class="col-sm">
            <div class=" px-3">
                <label ><b>Archivo</b></label>
                <div class="">
                    <input type="file" name="ARC_archivo" id="ARC_archivo" class="form-control" accept="image/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture>
                    <small id="ARC_archivo_err" class="text-danger"></small>
                </div>
                    <input type="hidden" name="FK_FRM_id" value="{{$FRM_id}}">
                <div class="my-3">
                    <label class="form-label"><b>Descripción del archivo:</b></label>
                    <textarea name="ARC_descripcion" id="ARC_descripcion" class="form-control" cols="30" rows="1">{{ old('ARC_descripcion.0')}}</textarea>
                    <small id="ARC_descripcion_err" class="text-danger"></small>

                </div>
            </div>
            <div class="border-bottom px-3" id="nuevo_elemento"></div>

        </div>
    </form>
    <div class="m-2 text-center text-shadow box-shadow bg-success text-light btn" id="btn_enviar_archivo">
        Enviar archivo
    </div>
    <span class="d-none btn btn-primary box-shadow text-shadow text-white" role="button" id="btn_cargando_adjunto" disabled>
        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
        Cargando...
    </span>
    <hr>
    <div class="container">
        <legend class="text-primary fs-4 text-center"> Archivos </legend>
        @foreach ( $adjuntos as $adjunto )
            @include( 'includes.archivos', [ 'archivos' => $adjuntos, 'id' => $adjunto->ARC_id ] )
        @endforeach
    </div>

</div>



<script>
    $(document).ready(function(){
        $('.titulo').click(function(){
            $('#card_body_1').slideToggle(function(){ });
        });
        $('#btn_guarda_adjuntos').click( function(){
            $(this).addClass('d-none');
            $('#btn_guarda_spiner').removeClass('d-none');
        } );
    });

    $("#btn_enviar_archivo").click(function(e){
        // e.preventDefault();
        $(this).addClass('d-none');
        $('#btn_cargando_adjunto').removeClass('d-none');
        var formData = new FormData($('#nuevo_adjunto_form')[0]);
        $('small').empty();
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: $('#nuevo_adjunto_form').attr("action"),
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () { },
            success: function (data, response) {

                jQuery.each(data.errors, function( key, value ){
                    $('#'+key+'_err').append( value );

                    $(this).removeClass('d-none');
                    $('#btn_cargando_adjunto').addClass('d-none');
                });
                if(data.success){
                    Swal.fire({
                        text: data.success,
                        icon: 'success',
                        confirmButtonText: 'ok!'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload()
                        }
                    })
                }
            },
            error: function(response){ }
        });
    });

</script>

