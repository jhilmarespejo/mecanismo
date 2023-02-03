

<div class="accordion" id="accordion_observaciones">

    {{-- Controles para nueva recomendacion --}}
    @if (Route::currentRouteName() != 'recomendaciones')
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
        @else
            <div class="alert alert-danger mx-5 mt-2 text-center" role="alert">
                Aún no se asignaron recomendaciones a este establecimiento
            </div>
        @endif


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
    @else

    @endif


</div>

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
