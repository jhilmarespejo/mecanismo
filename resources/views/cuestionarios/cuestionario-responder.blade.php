@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')
@php
    $aux = null;
    $a = [];
    $archivosRec = [];
    $archivosRecAcato = [];

    foreach ($recomendaciones as $k=>$rec){
        if ( $aux != $rec->REC_id ) {
            array_push($a, ['REC_id' => $rec->REC_id, 'REC_recomendacion' => $rec->REC_recomendacion, 'FK_FRM_id' => $rec->FK_FRM_id, 'REC_cumplimiento' => $rec->REC_cumplimiento, 'REC_fechaCumplimiento' => $rec->REC_fechaCumplimiento, 'REC_detallesCumplimiento' => $rec->REC_detallesCumplimiento, 'REC_fechaRecomendacion' => $rec->REC_fechaRecomendacion, 'REC_tipo' => $rec->REC_tipo, 'ARC_id' => $rec->ARC_id ] );
        } if( $rec->ARC_ruta != null ){
            if ($rec->ARC_tipo == 'recomemdacion') {
                array_push( $archivosRec, ['REC_id' => $rec->REC_id, 'ARC_ruta' => $rec->ARC_ruta, 'ARC_id' => $rec->ARC_id, 'ARC_descripcion' => $rec->ARC_descripcion, 'ARC_extension' => $rec->ARC_extension, 'ARC_tipo' => $rec->ARC_tipo, 'ARC_tipoArchivo' =>  $rec->ARC_tipoArchivo, 'FK_REC_id' =>  $rec->FK_REC_id] );
            }
            if ($rec->ARC_tipo == 'acato-recomendacion') {
                array_push( $archivosRecAcato, ['REC_id' => $rec->REC_id, 'ARC_ruta' => $rec->ARC_ruta, 'ARC_id' => $rec->ARC_id, 'ARC_descripcion' => $rec->ARC_descripcion, 'ARC_extension' => $rec->ARC_extension, 'ARC_tipo' => $rec->ARC_tipo, 'ARC_tipoArchivo' =>  $rec->ARC_tipoArchivo, 'FK_REC_id' =>  $rec->FK_REC_id] );
            }
        }
        $aux = $rec->REC_id;
    }
    //dump($a, $archivosRec, $archivosRecAcato, $aux);
    // dump( $elementos->toArray() );
    // exit
@endphp
<style>
    .hover:hover{
        background-color:  #eaeaea;
    }
    @media screen and (max-width: 380px) {
        ol, ul{padding-left: 10px;}
    }
</style>
@php
    $auxContadorCategorias = 1;
    $auxCategoriasArray = [];
    $archivos = [];
    $i='';

    /* ORDENA en forma de array las categorias, subcategorias y preguntas */
    foreach ($elementos as $key=>$elemento){
        /* Si la respuesta actual tiene una imagen, se guardan las rutas y otros en $archivos  */
        if( $elemento->ARC_ruta !='' ){
            array_push($archivos, ['RBF_id' => $elemento->RBF_id, 'ARC_ruta' => $elemento->ARC_ruta, 'ARC_id' => $elemento->ARC_id, 'ARC_tipoArchivo' => $elemento->ARC_tipoArchivo, 'ARC_extension' => $elemento->ARC_extension, 'ARC_descripcion' => $elemento->ARC_descripcion, 'FK_RES_id' => $elemento->FK_RES_id]);
        }
        // Verifica que no se repitan los elementos en el array cuando la preguna tiene archivos adjuntos
        if($i != $elemento->RBF_id){
            // dump($elemento->RBF_id);
            if ($elemento->categoria === null ) {
                $categoria = $elemento->subcategoria;
                $subcategoria = $elemento->categoria;
                $auxCategoriasArray[$categoria][$key] = $elemento;
            } else {
                $categoria = $elemento->categoria;
                $subcategoria = $elemento->subcategoria;
                $auxCategoriasArray[$categoria][$subcategoria][$key] = $elemento;
            }
        }
        $i = $elemento->RBF_id;
    } // END FOREACH
    // dump( $elementos->toArray(), $auxCategoriasArray );
    // $auxCategoriasArray = array_unique($auxCategoriasArray);
    // foreach ($auxCategoriasArray as $key => $value) {
    //     dump($value);
    // }
@endphp

<div class="container-fluid p-sm-3 p-0 mx-0" id="cuestionario" >

    @if ( count($elementos) > 0 )
        {{-- minimenu --}}
        @mobile
        <div class="container-fluid row border-top border-bottom p-3">
            <div class="col ">
                <a class="text-decoration-none fs-4" href="/establecimientos/historial/{{$elemento->EST_id}}" >
                <i class="bi bi-arrow-90deg-left"></i> </a>
            </div>
            <div class="col ">
                <a class="text-decoration-none fs-4" href="/cuestionario/imprimir/{{$elemento->FRM_id}}" >
                    <i class="bi bi-printer-fill"></i></span>
                </a>
            </div>
        </div>
        @endmobile

        @desktop
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="nav2">
            <div class="container-fluid">
              <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                    <li class="nav-item p-1 px-3">
                        <a class="text-decoration-none" href="/establecimientos/historial/{{$elemento->EST_id}}" >
                            <i class="bi bi-arrow-90deg-left"></i> Historial </a>
                    </li>
                    <li class="nav-item p-1 px-3" id="btn_imprimir">
                        <a class="text-decoration-none" href="/cuestionario/imprimir/{{$elemento->FRM_id}}" >
                            <i class="bi bi-printer"></i> Imprimir</span>
                        </a>
                    </li>
                </ul>
              </div>
            </div>
        </nav>
        @endmobile

        {{-- Encabezado --}}
        <div class="text-center head">
            <p class="m-0 p-0 fs-3" id="establecimiento">{{ $elemento->EST_nombre }}</p>
            <p class="text-primary m-0 p-0 fs-3" id="titulo"> {{ $elemento->FRM_titulo }}</p>
            <p class="text-primary m-0 p-0 fs-5" id="titulo">Responder/llenar cuestionario: {{ $elemento->FRM_version }}</p>
        </div>

        {{-- Cuestionario --}}
        <div class="row border m-sm-2 p-2 d-flex">
            {{-- boton para el plegar/desplegar el cuestionario --}}
            <legend class="text-primary fs-3 text-center" > Cuestionario</legend>

            @desktop
                <div class="form-switch fs-4">
                    <input class="form-check-input" type="checkbox" checked onclick="plegar_desplegar('frm_cuestionario')">
                </div>
                @include('includes.cuestionario_desktop')
            @enddesktop
            @mobile
                @include('includes.cuestionario_mobile')
            @endmobile
        </div>

        {{-- INCLUDE para Recomendaciones --}}
        <div class="row border m-sm-2 p-2 d-flex">
            {{-- boton para el plegar/desplegar las observaciones --}}
            <div class="form-switch fs-4">
                <input class="form-check-input chek-observaciones" type="checkbox" onclick="plegar_desplegar('accordion_observaciones')">
            </div>
            <legend class="text-primary fs-4 text-center" > Oservaciones identificadas</legend>
            @include('includes.recomendaciones')
        </div>

        {{-- INCLUDE para Adjuntos --}}
        <div class="row border m-sm-2 p-2 d-flex">
            {{-- boton para el plegar/desplegar los adjuntos --}}
            <div class="form-switch fs-4">
                <input class="form-check-input chek-adjuntos" type="checkbox" onclick="plegar_desplegar('div_adjuntos')">
            </div>
            <legend class="text-primary fs-4 text-center" > Archivos adjuntos</legend>
            @include('includes.adjuntos')
        </div>

    @else
        <div class="text-center head">
            
            {{-- ARREGLAR AQUI --}}
            {{-- <p class=" m-0 p-0" id="establecimiento" style="font-size: 20px">Establecimiento: {{ $rec->EST_nombre }}</p> --}}


            {{-- <p class="text-primary m-0 p-0" id="titulo" style="font-size: 30px" >Responder/llenar cuestionario: {{ $elemento->FRM_version }}</p> --}}
        </div>
        @if(Auth::user()->rol == 'Administrador' )
            <div class="alert alert-warning p-3">
                <a class="btn btn-danger bt-lg text-decoration-none" href="/cuestionario/{{$FRM_id}}">Debe organizar preguntas para éste cuestionario </a>
            </div>
        @else
            <div class="alert alert-warning p-3 btn btn-danger bt-lg text-decoration-none">
                El cuestionario aún no está disponible
            </div>
        @endif
    @endif

</div> {{-- /container --}}


<script>

    function plegar_desplegar(totoggle) {
        $('#'+totoggle).toggle("slow");
    }

    $(document).ready(function() {
        $('#accordion_observaciones').toggle("slow");
        $('#div_adjuntos').toggle("slow");
        // Evita enviar formulario al presionar Enter
        $("form").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });
    });



    function confirmaCuestionario( FRM_id ){
        $.ajax({
                async: true,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: '/cuestionario/confirmaCuestionario',
                type: 'POST',
                data: {estado: 'completado', FRM_id: FRM_id },
                // contentType: false,
                // processData: false,
                beforeSend: function () { },
                success: function (data, response) {
                    // console.log(data.message);
                    Swal.fire(data.message);
                },
                //complete : function(data, response) {},
                error: function(response){  }
            });
    }

    /*Boton para confirmar los datos del formulario*/


   /* Guarda cada respuesta del formulario cuando se el mouse se mueve a la siguiente pregunta*/
   $(".frm-respuesta").focusout(function(e){
        e.preventDefault();
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        let formData = new FormData($('#frm_'+id)[0]);
        console.log(formData);
        $.ajax({
            async: true,
            // headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: '/cuestionario/guardarRespuestasCuestionario',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                if( $('input.archivo-'+id).val() !== '' ){
                    $('.archivo-correcto').addClass('d-none');
                    $('input.archivo-'+id).addClass('d-none');
                    $('.spiner-'+id).removeClass('d-none');
                } else {
                }
            },
            success: function (data, response) {
                if( data.message === 'sin_respuesta' ){
                    console.log('sin_respuesta');
                    $('#frm_'+id).children('div').find('span.marca').html('<i class="bi bi-exclamation-triangle text-danger fs-5"></i>');
                    $('#frm_'+id).children('div').find('input.resp').addClass('border border-2 border-danger');
                } if( data.message === 'correcto' ){
                    console.log('OK');
                    $('#frm_'+id).children('div').find('input.resp').removeClass('border border-2 border-danger');
                    $('#frm_'+id).children('div').find('span.marca').empty();
                    $('#frm_'+id).children('div.complemento i').empty();
                } if( data.message === 'archivos_correcto' ){
                    console.log('archivos_correcto');
                    $('.spiner-'+id).addClass('d-none');
                    $('.archivo-correcto').removeClass('d-none');
                }
            },
            //complete : function(data, response) {},
            error: function(response){  }
        });
    });


    // $(".col-respuestas").focusout(function(e){
        //     var data = [];
        //     var x = [];
        //     var RES_tipoRespuesta = null;
        //     var RES_complementoRespuesta = null;
        //     var FK_RBF_id = null;
        //     var RES_respuesta = null;
        //     var RES_complemento = null;
        //     var RES_adjunto = null;

        //     $(this).find(":input[type='hidden']").each(function(key, val){
        //         if( $(this).attr('name') == 'RES_tipoRespuesta' ){
        //             RES_tipoRespuesta = $(this).val()
        //         }
        //         if( $(this).attr('name') == 'RES_complementoRespuesta' ){
        //             RES_complementoRespuesta = $(this).val()
        //         }
        //         if( $(this).attr('name') == 'FK_RBF_id' ){
        //             FK_RBF_id = $(this).val()
        //         }

        //     });
        //     if($(this).find(":input[type='checkbox']").length != 0 ){
        //         $(this).find("input[type='checkbox']:checked").each(function(key, val){
        //                 x.push($(this).val());
        //             });
        //             var x = JSON.stringify(Object.assign({}, x)).toString();
        //             RES_respuesta = x;
        //     }else if( $(this).find(":input[type='radio']").length != 0 ){
        //         RES_respuesta= $(this).find("input[type='radio']:checked").val();
        //     }

        //     // else if( $(this).find("input[type='text']").length != 0 ){
        //     //     RES_respuesta= $(this).find("input[type='text']").val();
        //     // }else if( $(this).find("input[type='number']").length != 0){
        //     //     RES_respuesta= $(this).find("input[type='number']").val();
        //     // }

        //     else if( $(this).find("input[name='RES_respuesta']").length != 0 ){
        //         RES_respuesta= $(this).find("input[name='RES_respuesta']").val();
        //     }

        //     if( $(this).find("input[name='RES_complemento']").length != 0 ){
        //         RES_complemento = $(this).find("input[name='RES_complemento']").val();
        //     }
        //     if( $(this).find("input[name='RES_adjunto']").length != 0 ){
        //         RES_adjunto = $(this).find("input[name='RES_adjunto']").val();
        //         // var RES_adjunto = $(this).find("input[name='RES_adjunto']").files[0];
        //         // console.log(RES_adjunto);
        //     }


        //     // efecto rojo en el input cuanto esta vacío
        //     if(RES_respuesta == null || RES_respuesta == ''){
        //         console.log('VACIO');
        //         $(this).children('div').find('span.marca').html('<i class="bi bi-exclamation-triang text-danger"></i>');
        //         $(this).children('div').find('input.resp').addClass('border border-2 border-danger');
        //         // $(this).children('div').find('input').removeClass('border border-2 border-danger')
        //     } else {
        //         console.log('OK');
        //         // $(this).children('div').find('span.marca').html('<i class="bi bi-check-square text-success"></i>');
        //         $(this).children('div').find('input.resp').removeClass('border border-2 border-danger');
        //         $(this).children('div').find('span.marca').empty();
        //         // $(this).children('div').find('input').addClass('border border-1 border-success')
        //     }
        //     $.ajax({
        //         async: true,
        //         headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        //         url: '/cuestionario/guardarRespuestasCuestionario',
        //         type: 'POST',
        //         data: {RES_respuesta: RES_respuesta, FK_RBF_id: FK_RBF_id, RES_tipoRespuesta: RES_tipoRespuesta, RES_complementoRespuesta: RES_complementoRespuesta, RES_complemento: RES_complemento, RES_adjunto: RES_adjunto},
        //         // enctype: 'multipart/form-data',
        //         // processData: false,
        //         // contentType: 'application/octet-stream',
        //         dataType: "json",

        //         beforeSend: function () {},
        //         success: function (data, response) {},
        //         error: function(response){ console.log(response) }
        //     });
        //     e.preventDefault();
        // });

    // var i = 0;
    // var j = 0;
    /* Adiciona controles para una nueva recomendacion */
        // $(document).on('click', '#btn_adicionar_recomendacion', function () {
        //     ++i;
        //     $("#recomendaciones").append('<form id="form_recomendaciones_'+i+'" method="POST" enctype="multipart/form-data" action="javascript:void(0)" ><div class="form-floating border-bottom row" id="recomendacion_'+i+'"><textarea style="height: 80px" name="REC_recomendacion" class="form-control" placeholder=""></textarea><label>Recomendación - 1</label></div><div class="row my-1 " id="archivos_'+i+'" >  </div><input type="hidden" name="FK_FRM_id" value="{{ $FRM_id }}">     <div id="botones_'+i+'"><span class="btn btn-danger nuevo-archivo" id="nuevo_archivo_'+i+'" ><i class="bi bi-file-earmark-plus adicionar-archivo"></i> Adicionar imagen o documento</span>   <span class="btn btn-primary d-none cargando" id="cargando_'+i+'" disabled>     <span class="spinner-border spinner-border-sm " ></span> Guardando... </span>   <span  class="btn btn-success guardar-recomendacion" id="guardar_recomendacion_'+i+'"><i class="bi bi-save2"></i> Guardar Recomendación</span></div>    </form> <hr/> ')
        // });

        /* Adiciona un input file + una descripcion para nuevo archivo o documento */
        // $(document).on('click', '.nuevo-archivo', function(){
        //     ++j;
        //     let id = $(this).attr('id').replace(/[^0-9]/g,'');

        //     $("#archivos_"+id).append('<div class="input-group input-group-sm"><input type="file" accept=".jpg, .jpeg, .png, .pdf" class="form-control" name="REC_archivo[]"><span class="input-group-text">Descripción:</span><input type="text" class="form-control" name="ARC_descripcion[]"></div>');
        // });

        // Guarda la recomendacion
        // $(document).on('click', '.guardar-recomendacion', function(e){
        //     let id = $(this).attr('id').replace(/[^0-9]/g,'');
        //     e.preventDefault();
        //     var formData = new FormData( $('#form_recomendaciones_'+id)[0] );
        //     // console.log(formData);
        //     $.ajax({
        //         async: true,
        //         headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        //         url: "{{ route('recomendaciones.nueva') }}",
        //         type: 'post',
        //         data: formData, //$(".form-recomendaciones").serialize(),
        //         beforeSend: function () {
        //             $('#cargando_'+id).removeClass('d-none');
        //             $('#guardar_recomendacion_'+id).addClass('d-none');
        //         },
        //         processData: false,
        //         contentType: false,
        //         success: function (data, response) {
        //             $('#cargando_'+id).addClass('d-none');
        //             $('#guardar_recomendacion_'+id).removeClass('d-none');

        //             $('#recomendacion_'+id+' textarea, #archivos_'+id+' input'+', #botones_'+id+' span').prop('disabled', true);
        //             $('#nuevo_archivo_'+id).addClass('bg-secondary');
        //             $('#guardar_recomendacion_'+id).text('Guardado');
        //         },
        //         error: function(response){ console.log(response) }
        //     });
        // });
</script>

@endsection

