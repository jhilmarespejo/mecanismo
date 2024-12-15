<div class="accordion" id="accordion_observaciones">
    {{-- Controles para nueva recomendacion --}}
    <div class="accordion-item bg-primary">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button bg-primary text-light text-shadow collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNuevo" aria-expanded="false" aria-controls="collapseNuevo">
            Nueva recomendación
            </button>
        </h2>
        <div id="collapseNuevo" class="aaccordion-collapse ms-2 collapse" aria-labelledby="headingOne" data-bs-parent="#accordion_observaciones">
            <div class="accordion-body bg-light">
                <form id="form_recomendaciones_1" method="POST" enctype="multipart/form-data" action="javascript:void(0)">@csrf
                    <input type="hidden" name="VIS_id" value="{{$VIS_id}}">

                    <div class="form-floating border-bottom row" id="recomendacion_1">
                        <textarea style="height: 80px" name="REC_recomendacion" class="form-control" placeholder=""></textarea>
                        <label>Recomendación:</label>
                        <small class="error text-danger" id="REC_recomendacion_err" ></small>
                    </div>
                    <div class="form-floating border-bottom row mt-1" id="">
                        {{-- <textarea style="height: 80px" name="REC_recomendacion" class="form-control" placeholder=""></textarea> --}}
                        <input type="text" class="form-control" name="REC_autoridad_competente">
                        <label>Autoridad competente:</label>
                        <small class="error text-danger" id="REC_autoridad_competente_err" ></small>
                    </div>

                    <div class="row my-1 " id="archivos">  </div>
                    <div id="botones_1">
                        <span class="my-2 btn btn-danger nuevo-adjunto text-light text-shadow" id="nuevo_archivo_1"><i class="bi bi-file-earmark-plus adicionar-archivo"></i> Adicionar imagen o documento
                        </span>
                        <span class="my-2 btn btn-primary d-none cargando text-light text-shadow" id="cargando_1" disabled="">
                            <span class="spinner-border spinner-border-sm ">
                            </span> Guardando... </span>
                            <span class="btn btn-success nueva-recomendacion text-light text-shadow" id="guardar_recomendacion_1"><i class="bi bi-save2"></i> Guardar Recomendación
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SE MUESTRAN LAS recomendaciones  --}}
    @if (count($recomendaciones)>0)
        @foreach ( $recomendaciones as $k=>$reco )
        {{-- @dump(($reco['archivos'])) --}}
            <div class="accordion-reco bg-info mt-2">
                <h2 class="accordion-header " id="heading_{{ $reco['REC_id'] }}">
                    <button class="accordion-button collapsed bg-info text-light text-shadow" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $reco['REC_id'] }}" aria-expanded="false" aria-controls="collapse_{{ $reco['REC_id'] }}">
                        <strong>{{ count($recomendaciones)-$k}}.</strong>&nbsp; {{ substr($reco['REC_recomendacion'], 0, 25) }}... </span>
                    </button>
                </h2>
                <div id="collapse_{{ $reco['REC_id'] }}" class="accordion-collapse collapse  ms-2" aria-labelledby="heading_{{ $reco['REC_id'] }}" data-bs-parent="#accordion_observaciones">
                    <div class="accordion-body bg-light">
                       <p><i class="bi bi-chat-left-text-fill text-primary fs-5"></i> {{ $reco['REC_recomendacion'] }} </p>
                       <p><i class="bi bi-calendar3 text-primary fs-5"></i> Fecha de la recomendación: <span class="fw-bold text-primary">{{ $reco['REC_fechaRecomendacion'] }}</span></p>
                       <p>
                        @if ($reco['REC_cumplimiento'] == 0)
                            <i class="bi bi-x-circle text-danger text-primary fs-5"></i> <span class="fw-bold text-danger ">Recomendacion no cumplida </span>
                        @elseif ($reco['REC_cumplimiento'] == 1)
                            <i class="bi bi-check-circle text-success text-primary fs-5"></i> <span class="fw-bold text-success ">Recomendacion cumplida </span>
                        @elseif ($reco['REC_cumplimiento'] == 2)
                            <i class="bi bi-upload text-warning text-primary fs-5"></i> <span class="fw-bold text-warning ">Recomendacion parcialmente cumplida </span>
                        @endif
                        </p>
                        <p><i class="bi bi-person-check-fill text-primary fs-5"></i> Autoridad competente: <span class="fw-bold text-primary"> {{ $reco['REC_autoridad_competente'] }}</span></p>
                        <p><i class="bi bi-file-richtext text-primary fs-5"></i> Archivos adjuntos:
                            @if (count($reco['archivos']) > 0 )
                                @include('includes.archivos', ['archivos' => $reco['archivos'] ])
                            @else
                                <div class="alert alert-warning" role="alert">
                                    <i class="bi bi-info-circle"></i> Sin archivos adjuntos
                                </div>
                            @endif
                        </p>

                        <hr>
                        <!-- Avances para esta recomendación -->
                        <h6>PROGRESOS:</h6>
                        @foreach ($progresos as $p=>$progreso)
                            @if ($reco['REC_id'] == $p)
                                {{-- @dump($progreso) --}}
                                {{-- @dump($avance['SREC_descripcion']) --}}
                                <div class="accordion" id="accordionAvance_{{$reco['REC_id']}}">
                                @foreach ($progreso as $avance)
                                    <div class="accordion-item">
                                      <h2 class="accordion-header" id="headingAvance{{$avance['SREC_id']}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAvance{{$avance['SREC_id']}}" aria-expanded="true" aria-controls="collapseAvance{{$avance['SREC_id']}}">
                                          <i class="bi bi-calendar3 text-blue"></i><span class="fw-bold ms-4">13-04-2023</span>
                                        </button>
                                      </h2>
                                      <div id="collapseAvance{{$avance['SREC_id']}}" class="accordion-collapse collapse" aria-labelledby="headingAvance{{$avance['SREC_id']}}" data-bs-parent="#accordionAvance_{{$reco['REC_id']}}">
                                        <div class="accordion-body">
                                          <p><i class="bi bi-chat-left-text-fill text-primary fs-5"></i> {{$avance['SREC_descripcion']}}</p>
                                          <p><i class="bi bi-paperclip text-primary fs-5"></i> Archivos adjuntos:

                                            @if ( count($avance['archivos']) > 0 )
                                                @include('includes.archivos', ['archivos' => $avance['archivos'] ])
                                            @else
                                                <div class="alert alert-warning" role="alert">
                                                    <i class="bi bi-info-circle"></i> Sin archivos adjuntos
                                                </div>
                                            @endif
                                          </p>
                                        </div>
                                      </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach

                        {{-- Boton para agregar nuevos progresos o avances en la recomendacion --}}
                        <hr>
                            @if ( $reco['REC_cumplimiento'] == 0 || $reco['REC_cumplimiento'] == 2 )
                                <p class="p-2 mt-2 fs-6 btn bg-primary text-light text-shadow box-shadow" data-bs-target="#modal_cumplimiento" data-bs-toggle="modal" title="Marcar cumplimiento" onclick="agregarCumplimiento('{{$reco['REC_recomendacion']}}', '{{$reco['REC_fechaRecomendacion']}}', '{{$reco['REC_id']}}')">
                                    <i class="bi bi-fast-forward-circle fs-5 acciones" > </i> Registrar avances en ésta recomendación
                                </p>
                            @endif
                        <hr>

                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-danger mx-5 mt-2 text-center" role="alert">
            Aún no se asignaron recomendaciones a este establecimiento
        </div>
    @endif


</div>


    <!-- Modal para agregar cumpliento a la recomendacion -->
<div class="modal fade" data-bs-backdrop="static"  id="modal_cumplimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" >Registro de avances o progresos para el cumplimiento de ésta recomendación </h5>
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
                        <option value="0">Recomendación No Cumplida</option>
                    </select>
                    {{-- mensaje de error: --}}
                    <small class="text-danger error" id="REC_cumplimiento_err"></small>

                    <label class="form-label mt-4">Fecha de cumplimiento: </label>
                    <input type="date"  id="fecha" class="form-control" name="SREC_fecha_seguimiento" value="{{ date("Y-m-d"); }}">

                    {{-- <input type="date" id="start" name="trip-start" value="2018-07-22" min="2018-01-01" max="2018-12-31"> --}}

                    {{-- mensaje de error: --}}
                    <small class="text-danger error" id="SREC_fecha_seguimiento_err"></small>
                    <br/>
                    <label class="form-label mt-3">Detalles del cumplimiento: </label>
                    <textarea name="SREC_descripcion" id="detalles" class="form-control" rows="3"></textarea>
                    {{-- mensaje de error: --}}
                    <small class="text-danger error" id="SREC_descripcion_err"></small>

                    <div class="archivos mt-3 "></div>
                    <span class="nuevo-archivo my-2 btn btn-danger text-light text-shadow"><i class="bi bi-file-earmark-plus"></i> Adicionar imagen o documento
                    </span>
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


<script>
     /*Guarda los avances o progresos de una recomendación*/
     $(document).on('click', '#guardar_cumplimiento', function(e){
        // $("#guardar_cumplimiento").on('click', function(e){
            e.preventDefault();
            var formData = new FormData($('#recomendaciones_form')[0]);
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
                        var vacio;
                        $("#recomendaciones_form :input").each(function () {
                            var id = $(this).attr('id');
                            $('#'+id+'_err').empty();
                            if ( $('#'+id ).val() == '' ) {
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
                    // // console.log(data.errors);
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

     /*agrega inputs para nuevos archivos*/
    let j=0;
    $(document).on('click', '.nuevo-archivo', function(){
        //let id = $(this).attr('id').replace(/[^0-9]/g,'');

            $(".archivos").append(`
        <fieldset class=" border px-2 mx-2 p-1 adjunto" id="adjunto_`+j+`">
            <legend class="hover fs-6 float-none w-auto p-2 rounded" >Archivos
                <span class="text-danger fs-4 rounded remover-adjunto" id="remover_adjunto_`+j+`">
                <i class="bi bi-trash "></i> </span>
            </legend>
            <p class="p-0 m-0">
                <input type="file" class="form-control input-archivo" id="archivo_`+j+`" name="REC_archivo[]" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture />
                <small class="text-danger col" id="archivo_`+j+`_err"></small>
            </p>
            <p class="p-0 m-0">
                <span class="">Descripción:</span>
                <input type="text" class="form-control" id="descripcion_`+j+`" name="ARC_descripcion[]">
                <small class="text-danger col" id="descripcion_`+j+`_err"></small>
            </p>


        </fieldset>`);
        ++j;
    });

    /*Elimina el contenedor de archivos adjuntos*/
    $(document).on('click', '.remover-adjunto', function(){
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        $("#adjunto_"+id).remove();
    });

        /*Agrega nuevo registro de cumplimiento*/
    function agregarCumplimiento( recomendacion, fecha, id ){
        $(".archivos").empty();
        $('textarea[name="REC_detallesCumplimiento"]').val('');
        $(".val-recomendacion, .val-fecha-recomendacion, .rec-id").empty();
        $(".val-recomendacion").append(recomendacion);
        $(".val-fecha-recomendacion").append(fecha);
        $(".rec-id").val(id);
    }
</script>
