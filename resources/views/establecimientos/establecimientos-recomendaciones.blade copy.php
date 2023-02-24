@extends('layouts.app')
@section('title', 'Cuestionario')


@section('content')
{{-- <link rel="stylesheet" href="/css/b5vtabs.min.css" /> --}}
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
        // dump($a, $archivosRec, $archivosRecAcato);
        // dump( $rec->FK_FRM_id );
        // exit
    @endphp

    @if ( !isset($from_cuestionario)   )
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="bi bi-gear"></i></a>
            <button class="navbar-toggler bg-secondary " type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-three-dots"></i>
            </button>
            <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/establecimientos/historial/{{$est_id}}" >
                        <i class="bi bi-arrow-90deg-left"></i> Historial
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/imprimir/{{$rec->FK_FRM_id}}" >
                        <i class="bi bi-printer"></i> Vista para imprimir formulario</span>
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/responder/{{$rec->FK_FRM_id}}" >
                        <i class="bi bi-ui-checks-grid"></i> Responder cuestionario
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/formulario/adjuntos/{{$rec->FK_FRM_id}}" >
                        <i class="bi bi-folder-symlink"></i> Archivos adjuntos
                    </a>
                </li>
                </ul>
            </div>
            </div>
        </nav>
        <h2 class="text-center py-2 text-primary">Historial de Observaciones</h2>
        <h3 class="text-center py-2 text-primary">{{ $establecimiento->EST_nombre; }}</h3>
    @endif

    <div class="d-flex align-items-start">
        <div class="col-sm-3 nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">

            {{-- Elemento para nueva recomendacion --}}
            <span class="nav-link p-2 border-top border-start bg-success text-light text-shadow" id="v-pills-0-tab" data-bs-toggle="pill" data-bs-target="#v-pills-0" type="button" role="tab" aria-controls="v-pills-0" aria-selected="true"> <strong>Nueva recomendación  </strong></span>

            {{-- Pestañas de cada recomendacion --}}
            @if (count($a)>0)
                @foreach ($a as $k=>$item)
                    <span class="nav-link p-2 border-top border-start {{ ($k == 0)? 'active':'' }}" id="v-pills-{{ $item['REC_id'] }}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{ $item['REC_id'] }}" type="button" role="tab" aria-controls="v-pills-{{ $item['REC_id'] }}" aria-selected="true"> <strong>{{ count($a)-$k}}. </strong> {{ substr($item['REC_recomendacion'], 0, 30) }}... </span>
                @endforeach
            @endif

        </div>

        <div class="col-sm-9 tab-content container-fluid border bg-light" id="v-pills-tabContent">
            @if (count($a)>0)
                @foreach ($a as $k=>$item)
                    <div class="tab-pane fade p-sm-4 p-1 {{ ($k == 0)? 'show active':'' }}" id="v-pills-{{ $item['REC_id'] }}" role="tabpanel" aria-labelledby="v-pills-{{ $item['REC_id'] }}-tab">
                        <dl>
                            <dt>Recomendación:</dt>
                            <dd class="ps-4"> {{ $item['REC_recomendacion'] }}</dd>

                            <dt>Fecha de la recomendación:</dt>
                            <dd class="ps-4 "> {{ $item['REC_fechaRecomendacion'] }} </dd>

                            <dt>Tipo de recomendación:</dt>
                            <dd class="ps-4 "> {{ $item['REC_tipo'] }}</dd>

                            <dt>Cumplimiento de la recomendación:</dt>
                            <dd class="ps-4 pt-3">
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
                @endforeach
            @endif

            {{-- formulario para agregar nueva recomendacion --}}
            <div class="tab-pane fade p-sm-4 p-1 {{ ($a == null)? 'show active':'' }}" id="v-pills-0" role="tabpanel" aria-labelledby="v-pills-0-tab">
                <form id="form_recomendaciones_1" method="POST" enctype="multipart/form-data" action="javascript:void(0)">@csrf
                    {{-- <input type="hidden" name="EST_id" value="{{$est_id}}"> --}}
                    <input type="hidden" name="FRM_id" value="{{$frm_id}}">
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

{{-- ------------------------------------------------------ --}}

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
                <input type="hidden" name="FRM_id" value="{{$frm_id}}">

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


</div>


{{-- ------------------------------------------------------ --}}


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


@endsection


