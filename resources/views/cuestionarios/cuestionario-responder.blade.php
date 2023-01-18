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
        /*Si la respuesta actual tiene una imagen, se guardan las rutas y otros en $archivos  */
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
    // $auxCategoriasArray = array_unique($auxCategoriasArray);
    // dump( $auxCategoriasArray, $archivos );
@endphp

<div class="container-fluid p-sm-3 p-0 mx-0" id="cuestionario" >


    @if ( count($elementos) > 0 )
        <div class="text-center head">
            <p class="text-primary m-0 p-0" id="titulo" style="font-size: 30px" > {{ $elemento->FRM_titulo }} </p>
            <p class=" m-0 p-0" id="establecimiento" style="font-size: 20px">Establecimiento: {{ $elemento->EST_nombre }}</p>
            <p class="text-primary m-0 p-0" id="titulo" style="font-size: 30px" >Responder/llenar cuestionario: {{ $elemento->FRM_version }}</p>
        </div>

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-gear"></i></a>
            <button class="navbar-toggler bg-secondary " type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-three-dots"></i>
            </button>
            <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/establecimientos/historial/{{$elemento->EST_id}}" >
                        <i class="bi bi-arrow-90deg-left"></i> Historial
                    </a>
                </li>
                <li class="nav-item p-1 px-3" id="btn_imprimir">
                    <a class="text-decoration-none" href="/cuestionario/imprimir/{{$elemento->FRM_id}}" >
                        <i class="bi bi-printer"></i> Vista para imprimir formulario</span>
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/formulario/adjuntos/{{$elemento->FRM_id}}" >
                        <i class="bi bi-folder-symlink"></i> Archivos adjuntos
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/recomendaciones/{{$elemento->FK_EST_id}}/{{$elemento->FRM_id}}" >
                        <i class="bi bi-chat-right-dots"></i></i> Recomendaciones
                    </a>
                </li>
                </ul>
            </div>
            </div>
        </nav>

        <div class="row border m-sm-2 p-2 d-flex">
            <ol id="q">
                @foreach ($auxCategoriasArray as $keyCat=>$categorias)
                    <li>
                        <strong id="categorias">{{ $keyCat }}</strong>
                        {{-- cuando la categoria TIENE subcategoria (SC)--}}
                        <ol>
                            @foreach ($categorias as $keySC=>$subcategorias )
                                @if ( is_string($keySC) )
                                    <li class="mt-1" id="con_subcategorias">
                                        <strong >{{ $keySC }}</strong>
                                        <ol>
                                            @foreach ($subcategorias as $key=>$pregunta)
                                                <li>
                                                    <div class=" row border-bottom py-3 hover p-2 elementos">
                                                        <div class="col-sm-5 col-preguntas-sc" >
                                                            {{ $pregunta->BCP_pregunta }}
                                                            @if ($pregunta->BCP_tipoRespuesta == 'Afirmación' || $pregunta->BCP_tipoRespuesta == 'Lista desplegable')
                                                                {{-- <p class="m-0"><small>* Sólo marque una opción de respuesta</small></p> --}}
                                                            @endif
                                                        </div>

                                                        <div class="col-sm-7 col-respuestas-sc">
                                                            {{-- <div class="row "> --}}
                                                            <form method="POST" enctype="multipart/form-data" id="frm_{{$pregunta->RBF_id}}" class="frm-respuesta"> @csrf
                                                                @php
                                                                //*** copiar de aqui
                                                                    $opcionesSC = json_decode( $pregunta->BCP_opciones, true);
                                                                    $respuestasSC = json_decode( $pregunta->RES_respuesta, true);
                                                                    if ($respuestasSC === null) { $respuestasSC = []; }
                                                                    // dump($preg->RES_respuesta, $opciones)
                                                                @endphp
                                                                {{-- @if ( is_array($opciones) )
                                                                    @if ($pregunta->BCP_tipoRespuesta == 'Casilla verificación')
                                                                        @foreach ($opciones as $opcion)
                                                                            @if ( !isset($respuestas) )
                                                                                <div class='col-auto d-flex'>
                                                                                    <input type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}">&nbsp;{{ $opcion }}
                                                                                </div>
                                                                            @else
                                                                                <div class='col-auto d-flex'>
                                                                                    <input {{( in_array( $opcion, $respuestas ) )? 'checked': ''}} type='checkbox' name="RES_respuesta[]" value="{{$opcion}}">&nbsp;{{ $opcion }}
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif

                                                                    @foreach ($opciones as $k=>$opcion)
                                                                        @if ( $pregunta->BCP_tipoRespuesta == 'Afirmación' || $pregunta->BCP_tipoRespuesta == 'Lista desplegable' )
                                                                            <div class='col-auto d-flex'>
                                                                                <input {{ ($pregunta->RES_respuesta == $opcion)? 'checked':'' }} type='radio' name="RES_respuesta" value="{{ $opcion }}">&nbsp;{{ $opcion }}
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @endif --}}
                                                                @if ( is_array($opcionesSC) )
                                                                    <div class="{{($pregunta->BCP_tipoRespuesta == 'Casilla verificación')? 'group-check' : 'group-radio'}}" >
                                                                        @foreach ($opcionesSC as $opcion)
                                                                            @if ($pregunta->BCP_tipoRespuesta == 'Casilla verificación')
                                                                                <div class="col-auto d-flex">
                                                                                    <input {{ in_array($opcion, $respuestasSC)? 'checked':'' }} type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                                                                </div>
                                                                            @elseif ( $pregunta->BCP_tipoRespuesta == 'Afirmación' || $pregunta->BCP_tipoRespuesta == 'Lista desplegable' )
                                                                                <div class="col-auto d-flex">
                                                                                    <input {{ ($pregunta->RES_respuesta == $opcion)? 'checked':'' }} type='radio' name="RES_respuesta" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @endif

                                                                @if ($pregunta->BCP_tipoRespuesta == 'Numeral')
                                                                    <div class="row p-2"><input class="ms-2 col resp" type='number' size='10' min="0" name="RES_respuesta" value="{{$pregunta->RES_respuesta}}"><span class="col-1 marca"></span> </div>
                                                                @endif
                                                                @if ($pregunta->BCP_tipoRespuesta == 'Respuesta corta')
                                                                    <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$pregunta->RES_respuesta}}"> <span class="col-1 marca"></span> </div>
                                                                @endif
                                                                @if ($pregunta->BCP_tipoRespuesta == 'Respuesta larga')
                                                                    <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$pregunta->RES_respuesta}}"><span class="col-1 marca"></span> </div>
                                                                @endif
                                                                {{-- </div> --}}
                                                                @if ( $pregunta->BCP_complemento)
                                                                    <div class="row complemento px-3 py-1"> {{ $pregunta->BCP_complemento }} <input type="text" name='RES_complemento' value="{{$pregunta->RES_complemento}}"></div>
                                                                @endif
                                                                @if ( $pregunta->BCP_adjunto != null || $pregunta->BCP_adjunto != '' )

                                                                    <span>{{$pregunta->BCP_adjunto}}</span>
                                                                    <div class="row complemento px-3 py-1">
                                                                        <input type="file" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" class="archivo-{{$pregunta->RBF_id}}" capture name='RES_adjunto[]' multiple>
                                                                        <input type="hidden" name="ARC_descripcion" value="{{$pregunta->BCP_pregunta}}">

                                                                        {{-- Si existen archivos se hace una iteracion --}}
                                                                        <div class="col">
                                                                            @include('includes.archivos', ['archivos' => $archivos, 'id' =>  $pregunta->RES_id ])
                                                                        </div>
                                                                    </div>
                                                                    <span class="btn btn-success btn-sm text-light d-none spiner-{{$pregunta->RBF_id}}" disabled>
                                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                                        Cargando archivo...
                                                                    </span>
                                                                    <span class="d-none text-success archivo-correcto" style="height: 20px;">
                                                                        <i class="bi bi-check-circle"></i> Archivo almacenado correctamente!
                                                                    </span>
                                                                @endif
                                                                <input type="hidden" name="RES_tipoRespuesta" value="{{$pregunta->BCP_tipoRespuesta}}">
                                                                <input type="hidden" name="RES_complementoRespuesta" value="{{$pregunta->BCP_complemento}}">
                                                                <input type="hidden" name="FK_RBF_id" value="{{$pregunta->RBF_id}}">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                        {{-- cuando la categoria NO tiene subcategoria --}}
                        <ol>
                            @foreach ($categorias as $keyP=>$preg )
                                @if ( !is_string($keyP) )
                                    <li class="mt-1" id="sin_preguntas">
                                        <div class="row border-bottom py-3 hover elementos">
                                            <div class="col-sm-5 col-preguntas" >
                                                    {{ $preg->BCP_pregunta }}
                                                @if ($preg->BCP_tipoRespuesta == 'Afirmación' || $preg->BCP_tipoRespuesta == 'Lista desplegable')
                                                    {{-- <p class="m-0"><small>* Sólo marque una opción de respuesta</small></p> --}}
                                                @endif
                                            </div>
                                            <div class="col-sm-7 col-respuestas">
                                                <form method="POST" enctype="multipart/form-data" id="frm_{{$preg->RBF_id}}" class="frm-respuesta">@csrf
                                                    {{-- <div class="row"> --}}
                                                    @php
                                                    // *** arreglar aqui
                                                        $opciones = json_decode( $preg->BCP_opciones, true);
                                                        $respuestas = json_decode( $preg->RES_respuesta, true);
                                                        if ($respuestas === null) { $respuestas = []; }
                                                        //dump($opciones, $respuestas);//exit;
                                                    @endphp
                                                    @if ( is_array($opciones) )
                                                        <div class="{{($preg->BCP_tipoRespuesta == 'Casilla verificación')? 'group-check' : 'group-radio'}}" >
                                                            @foreach ( $opciones as $opcion  )
                                                                @if ($preg->BCP_tipoRespuesta == 'Casilla verificación')
                                                                    <div class="col-auto d-flex">
                                                                        <input {{ in_array($opcion, $respuestas)? 'checked':'' }} type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                                                    </div>
                                                                @elseif ( $preg->BCP_tipoRespuesta == 'Afirmación' || $preg->BCP_tipoRespuesta == 'Lista desplegable' )
                                                                    <div class="col-auto d-flex">
                                                                        <input {{ ($preg->RES_respuesta == $opcion)? 'checked':'' }} type='radio' name="RES_respuesta" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if ($preg->BCP_tipoRespuesta == 'Numeral')
                                                        <div class="row p-2"><input class="ms-2 col resp" type='number' size='10' min="0" name="RES_respuesta" value="{{$preg->RES_respuesta}}"> <span class="col-1 col-1 marca"></span></div>
                                                    @endif
                                                    @if ($preg->BCP_tipoRespuesta == 'Respuesta corta')
                                                        <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$preg->RES_respuesta}}"> <span class="col-1 marca"></span> </div>
                                                    @endif
                                                    @if ($preg->BCP_tipoRespuesta == 'Respuesta larga')
                                                        <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$preg->RES_respuesta}}"><span class="col-1 marca"></span> </div>
                                                    @endif
                                                    {{-- </div> --}}
                                                    @if ( $preg->BCP_complemento )
                                                        <div class="row complemento px-3 py-1"> {{ $preg->BCP_complemento }} <input type="text" name='RES_complemento' value="{{$preg->RES_complemento}}"></div>
                                                    @endif
                                                    @if ( $preg->BCP_adjunto != null || $preg->BCP_adjunto != '' )

                                                        <span>{{$preg->BCP_adjunto}}</span>
                                                        <div class="row complemento px-3 py-1">
                                                            <input type="file" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" class="archivo-{{$preg->RBF_id}}" capture name='RES_adjunto[]' multiple>
                                                            <input type="hidden" name="ARC_descripcion" value="{{$preg->BCP_pregunta}}">

                                                            {{-- Si existen archivos se hace una iteracion --}}
                                                            <div class="col">
                                                                @include('includes.archivos', ['archivos' => $archivos, 'id' =>  $preg->RES_id ])
                                                            </div>
                                                        </div>
                                                        <span class="btn btn-success btn-sm text-light d-none spiner-{{$preg->RBF_id}}" disabled>
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            Cargando archivo...
                                                        </span>
                                                        <span class="d-none text-success archivo-correcto" style="height: 20px;">
                                                            <i class="bi bi-check-circle"></i> Archivo almacenado correctamente!
                                                        </span>
                                                    @endif
                                                    <input type="hidden" name="RES_tipoRespuesta" value="{{$preg->BCP_tipoRespuesta}}">
                                                    <input type="hidden" name="RES_complementoRespuesta" value="{{$preg->BCP_complemento}}">
                                                    <input type="hidden" name="FK_RBF_id" value="{{$preg->RBF_id}}">
                                                </form>
                                                {{-- <input type="hidden" name="FK_FRM_id" value="{{ $FRM_id }}"> --}}
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </li>
                @endforeach
            </ol>
            <div class="row m-2 d-flex">
                <span class="btn btn-primary text-light text-shadow box-shadow" id="btn_confirmacion">Confirmar datos</span>
                <small class="alert alert-danger d-none" id="msg_vacios">¡Existen campos vacíos!</small>
            </div>
        </div>

        <div class="row border m-sm-2 p-2 d-flex">
            <legend class="text-primary fs-4 text-center" > Oservaciones identificadas</legend>
            {{-- ACORDDION PARA OBSERVACIONES/RECOMENDACIONES --}}
            <div class="accordion" id="accordion_observaciones">
                <div class="accordion-item bg-success">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button bg-success text-light text-shadow collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNuevo" aria-expanded="false" aria-controls="collapseNuevo">
                    Nueva recomendación
                    </button>
                </h2>
                <div id="collapseNuevo" class="aaccordion-collapse ms-2 collapse" aria-labelledby="headingOne" data-bs-parent="#accordion_observaciones">
                    <div class="accordion-body bg-light">
                        <form id="form_recomendaciones_1" method="POST" enctype="multipart/form-data" action="javascript:void(0)">@csrf
                            {{-- <input type="hidden" name="EST_id" value="{{$est_id}}"> --}}
                            <input type="hidden" name="FRM_id" value="{{$FRM_id}}">
                            <div class="form-floating border-bottom row" id="recomendacion_1">
                                <textarea style="height: 80px" name="REC_recomendacion" class="form-control" placeholder=""></textarea>
                                <label>Recomendación:</label>
                                <small class="error text-danger" id="REC_recomendacion_err" ></small>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-sm">
                                    <label class="form-label">Fecha:</label>
                                    <input type="date" class="form-control" name="REC_fechaRecomendacion" value="{{date('d/m/Y')}}">
                                    <small class="error text-danger" id="REC_fechaRecomendacion_err" ></small>

                                </div>

                                <div class="mb-3 col-sm">
                                    <label class="form-label">Tipo de recomendación:</label>
                                    <select class="form-select" name="REC_tipo">
                                        <option selected value=''>Seleccione...</option>
                                        <option value="Estructural">Estructural</option>
                                        <option value="Procedimental">Procedimental</option>
                                        <option value="Específica">Específica</option>
                                    </select>
                                    <small class="error text-danger" id="REC_tipo_err" ></small>
                                </div>

                            </div>
                            <div class="row my-1 " id="archivos">  </div>
                            {{-- <input type="hidden" name="FK_FRM_id" value="{{ $item['FK_FRM_id'] }}"> --}}
                            <div id="botones_1">
                                <span class="my-2 btn btn-danger nuevo-adjunto text-light text-shadow" id="nuevo_archivo_1"><i class="bi bi-file-earmark-plus adicionar-archivo"></i> Adicionar imagen o documento
                                </span>
                                <span class="my-2 btn btn-primary d-none cargando text-light text-shadow" id="cargando_1" disabled="">
                                    <span class="spinner-border spinner-border-sm "></span> Guardando... </span>   <span class="btn btn-success nueva-recomendacion text-light text-shadow" id="guardar_recomendacion_1"><i class="bi bi-save2"></i> Guardar Recomendación
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                </div>

                @if (count($a)>0)
                    @foreach ($a as $k=>$item)
                    <div class="accordion-item bg-primary mt-2">
                        <h2 class="accordion-header " id="heading_{{ $item['REC_id'] }}">
                            <button class="accordion-button collapsed bg-primary text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $item['REC_id'] }}" aria-expanded="false" aria-controls="collapse_{{ $item['REC_id'] }}">
                                <strong>{{ count($a)-$k}}.</strong>&nbsp; {{ substr($item['REC_recomendacion'], 0, 25) }}... </span>
                            </button>
                        </h2>
                        <div id="collapse_{{ $item['REC_id'] }}" class="accordion-collapse collapse  ms-2" aria-labelledby="heading_{{ $item['REC_id'] }}" data-bs-parent="#accordion_observaciones">
                            <div class="accordion-body bg-light">
                                <dl>
                                    <dt>Recomendación:</dt>
                                    <dd class="ps-4"> {{ $item['REC_recomendacion'] }}</dd>

                                    <dt>Fecha de la recomendación:</dt>
                                    <dd class="ps-4 "> {{ $item['REC_fechaRecomendacion'] }} </dd>

                                    <dt>Tipo de recomendación:</dt>
                                    <dd class="ps-4 "> {{ $item['REC_tipo'] }}</dd>

                                    <dt>Cumplimiento de la recomendación:</dt>
                                    <dd class="ps-4 pt-3">
                                        {{-- {{ ($item['REC_cumplimiento'] == 0)? 'No' : ' ' }} --}}
                                        @if ( ($item['REC_cumplimiento'] == 0) )
                                            <span class="badge rounded-pill bg-danger text-light fs-6"><i class="bi bi-x-circle "></i> Recomendación No Cumplida</span>
                                        @elseif ( ($item['REC_cumplimiento'] == 2) )
                                            <span class="badge rounded-pill bg-warning text-light fs-6"><i class="bi bi-arrow-bar-up p-2"></i> Recomendación Parcialmete Cumplida</span>
                                        @elseif ( ($item['REC_cumplimiento'] == 1) )
                                            <span class="badge rounded-pill bg-success text-light fs-6 text-shadow"><i class="bi bi-check-circle "></i> Recomendación Cumplida</span>

                                        @endif
                                        <br>
                                        @if ( $item['REC_cumplimiento'] == 0 || $item['REC_cumplimiento'] == 2 )
                                            <p class="p-2 mt-2 fs-6 btn bg-primary text-light text-shadow" data-bs-target="#modal_cumplimiento" data-bs-toggle="modal" title="Marcar cumplimiento" onclick="agregarCumplimiento('{{$item['REC_recomendacion']}}', '{{$item['REC_fechaRecomendacion']}}', '{{$item['REC_id']}}')">
                                                <i class="bi bi-pencil-square button btn-success p-2 rounded acciones" > </i> Marcar cumplimieto de ésta recomendación
                                            </p>
                                        @endif

                                    </dd>

                                    <dt>Archivos adjuntos:</dt>
                                    <dd class="ps-4 align-middle">
                                        @if ($item['ARC_id'] != null)
                                            @include('includes.archivos', ['archivos' => $archivosRec, 'id' =>  $item['REC_id'] ])
                                        @endif
                                    </dd>
                                    <hr>
                                    <dt>Fecha de cumplimiento a la Recomendación</dt>
                                    <dd class="ps-4">{{ $item['REC_fechaCumplimiento'] }}</dd>
                                    <dt>Detalles del cumplimiento</dt>
                                    <dd class="ps-4">{{ $item['REC_detallesCumplimiento'] }}</dd>

                                    <dt>Adjuntos</dt>
                                    <dd class="ps-4">
                                        @if ($item['ARC_id'] != null && $item['REC_detallesCumplimiento'] != null )
                                            @include('includes.archivos', [ 'archivos' => $archivosRecAcato, 'id' =>  $item['REC_id'] ])
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif


                {{-- <div class="accordion-item bg-primary mt-2">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed bg-primary text-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Accordion Item #3
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse  ms-2" aria-labelledby="headingThree" data-bs-parent="#accordion_observaciones">
                    <div class="accordion-body bg-light">
                    Lorem ipsum dolor sit.
                    </div>
                </div>
                </div> --}}

            </div>
        </div>
    @else
        <div class="text-center head">

            <p class=" m-0 p-0" id="establecimiento" style="font-size: 20px">Establecimiento: {{ $rec->EST_nombre }}</p>
            {{-- <p class="text-primary m-0 p-0" id="titulo" style="font-size: 30px" >Responder/llenar cuestionario: {{ $elemento->FRM_version }}</p> --}}
        </div>
        <div class="alert alert-warning p-3">
            <a class="btn btn-danger bt-lg text-decoration-none" href="/cuestionario/{{$FRM_id}}">Debe organizar preguntas para éste cuestionario </a>
        </div>
    @endif




    <!-- Modal para agregar cumpliento a la recomendacion -->
    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="modal_cumplimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Datos de cumplimiento a la recomendación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="recomendaciones_form" enctype="multipart/form-data" action="javascript:void(0)" >@csrf
                    <div class="modal-body body-cumplimiento">
                        <dl>
                            <dt>Recomendación:</dt>
                            <dd class="ps-2 val-recomendacion"></dd>
                            <dt>Fecha de la recomendación:</dt>
                            <dd class="ps-2 val-fecha-recomendacion"></dd>
                        </dl>
                    <hr>
                        <input type="hidden" name="REC_id" class="rec-id" value="{{ (isset($item['REC_id']))? $item['REC_id']:'' }}">

                        <label class="form-label">Cumplimiento: </label>
                        <select class="form-select" name="REC_cumplimiento">
                            <option value='' selected>Seleccione...</option>
                            <option value="2">Recomendación Parcialmente Cumplida</option>
                            <option value="1">Recomendación Cumplida</option>
                            {{-- <option value="0">Recomendación No Cumplida</option> --}}
                        </select>
                        {{-- mensaje de error: --}}
                        <small class="text-danger error" id="REC_cumplimiento_err"></small>

                        <label class="form-label mt-4">Fecha de cumplimiento: </label>
                        <input type="date"  id="fecha" class="form-control" name="REC_fechaCumplimiento" value="{{ date("Y-m-d"); }}">

                        {{-- <input type="date" id="start" name="trip-start" value="2018-07-22" min="2018-01-01" max="2018-12-31"> --}}

                        {{-- mensaje de error: --}}
                        <small class="text-danger error" id="REC_fechaCumplimiento_err"></small>
                        <br/>
                        <label class="form-label mt-3">Detalles del cumplimiento: </label>
                        <textarea name="REC_detallesCumplimiento" id="detalles" class="form-control" rows="3"></textarea>
                        {{-- mensaje de error: --}}
                        <small class="text-danger error" id="REC_detallesCumplimiento_err"></small>

                        <div class="archivos mt-3 "></div>
                        <span class="btn btn-success nuevo-archivo p-2 my-2" >Adjuntar archivos</span>
                    </div>
                </form>
                <div class="modal-footer">
                    <span type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</span>
                    <span type="button" class="btn btn-primary" id="guardar_cumplimiento">Guardar</span>
                </div>

                {{-- <div class="archivos mt-3 "></div> --}}

            </div>
        </div>
    </div>



</div> {{-- /container --}}

<script>
    /*Boton para confirmar los datos del formulario*/
    $("#btn_confirmacion").click( function(e){
        let marcas = [];
        $('.frm-respuesta').removeClass('bg-warning bg-gradient rounded');

        /*Validacion para radiobuttons*/
        $("#q div.group-radio").each(function(e){
            if( !$(this).find("input[name='RES_respuesta']:radio").is(':checked')) {
                marcas.push($(this).parent().attr('id'));
            }
        });
        /*Validación para checkbox*/
        $("#q div.group-check").each(function(e){
            $(this).each(function(e){
                $(this).find('input').each(function(){
                    if ($(this).prop('checked')) {
                        return false;
                    } else {
                        marcas.push($(this).closest('form').attr('id'));
                    }
                });
            })
        });
        /*Validacion para input text, number*/
        $("#q").find('input.resp').each(function(e){
            if( $(this).val() == '' ){
                marcas.push($(this).closest('form').attr('id'));
            }
        })
        /*Ordena los elementos del array*/
        // marcas.sort(function(a, b){return a - b});
        // console.log(marcas);
        // marca el alerta para respuestas vacias

        //console.log(marcas);
        $.each(marcas, function(key, value){
            $('#'+value).addClass('bg-warning bg-gradient rounded');
        });
        if(marcas.length > 0){
            // $("html, body").animate( { scrollTop: "10" }, 3000);
            $('html,body').animate({
                scrollTop: ($('#'+$("#q").find('form.frm-respuesta.bg-warning').attr('id')).offset().top)-150
            }, 'slow');
        }

        // $("#q").find('form.frm-respuesta.bg-warning').each(function(key,value){
        //     console.log($(this).attr('id'));
        // });

        console.log($("#q").find('form.frm-respuesta.bg-warning').attr('id'));

    });

   /* Guarda cada respuesta del formulario cuando se el mouse se mueve a la siguiente pregunta*/
    $(".frm-respuesta").focusout(function(e){
        e.preventDefault();
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        let formData = new FormData($('#frm_'+id)[0]);
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

