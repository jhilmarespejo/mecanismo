@php
    // foreach ($elementos as $k=>$elemento){
    //     dump(($k+1).'-'.$elemento["BCP_pregunta"]);
    // }
    // exit;
@endphp
<div id="carousel_preguntas" class="carousel slide" data-bs-interval="false">
    <div class="carousel-inner">
        @php $aux = 0; @endphp
        @foreach ($elementos as $k=>$elemento)
            <div class="carousel-item {{ ($k==0)? 'active': '' }}" id="card_{{$k+1}}">
                <input type="hidden" class="salto" value="{{ $elemento['RBF_salto_FK_BCP_id'] }}">
                <div class="card border mb-3 RBF_id_{{$elemento['RBF_id']}} " id="BCP_id_{{$elemento['BCP_id']}}">
                    <div class="card-header" >
                        <dl>
                            @php
                                if( $elemento['categoriaID'] == null ){
                                    $categoria = $elemento['subcategoria'];
                                    $subcategoria = null;
                                } else {
                                    $categoria = $elemento['categoria'];
                                    $subcategoria = $elemento['subcategoria'];
                                }
                            @endphp
                            <dd class="categoria_{{$elemento['FK_CAT_id']}}"> <b>Categoría: </b>{{ $categoria }}</dd>
                            @if( $subcategoria != null)
                            <dd class="subcategoria_{{$elemento['categoriaID']}}" > <b>Subcategoría: </b>{{$subcategoria}}</dd>
                            @endif
                        </dl>
                    </div>
                    <div class="card-body">
                        {{-- MUESTRA LAS PREGUNTAS --}}
                        @if ($elemento['BCP_tipoRespuesta'] == 'Etiqueta')
                            <p class="card-title fs-4 alert alert-danger" role="alert">
                                <b><small>{{$k+1}}</small></b>. {{$elemento['BCP_pregunta']}}
                            </p>
                        @else
                            <p class="card-title fs-4">
                                {{-- <b> {{$elemento['BCP_id']}}--<small>{{$k+1}}</small></b>. {{$elemento['BCP_pregunta']}} --}}
                                <b> <small>{{$k+1}}</small></b>. {{$elemento['BCP_pregunta']}}
                            </p>
                        @endif
                        {{-- IMPUTS PARA REGISTRAR LAS PREGUNTAS --}}
                        <form method="POST" enctype="multipart/form-data" id="frm_{{$elemento['RBF_id']}}" class="frm-respuesta"> @csrf
                            @php
                                $opcionesSC = json_decode( $elemento['BCP_opciones'], true);
                                $respuestasSC = json_decode( $elemento['RES_respuesta'], true);
                                if ($respuestasSC === null) { $respuestasSC = []; }
                                // dump($preg['RES_respuesta'], $opciones)
                            @endphp
                            @if ( is_array($opcionesSC) )
                                <div class="{{($elemento['BCP_tipoRespuesta'] == 'Casilla verificación')? 'group-check' : 'group-radio'}}" >
                                    @foreach ($opcionesSC as $opcion)
                                        @if ($elemento['BCP_tipoRespuesta'] == 'Casilla verificación')
                                            <div class="col-auto d-flex">
                                                <input {{ in_array($opcion, $respuestasSC)? 'checked':'' }} type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                            </div>
                                        @elseif ( $elemento['BCP_tipoRespuesta'] == 'Afirmación' || $elemento['BCP_tipoRespuesta'] == 'Lista desplegable' )
                                            <div class="col-auto d-flex">
                                                <input {{ ($elemento['RES_respuesta'] == $opcion)? 'checked':'' }} type='radio' name="RES_respuesta" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if ($elemento['BCP_tipoRespuesta'] == 'Etiqueta')
                                <div class="visually-hidden row p-2"><input class="ms-2 col-6 resp" size="4" type='number' size='10' min="0" value="0">
                                    <span class="col-1 marca"></span>
                                </div>
                            @endif
                            @if ($elemento['BCP_tipoRespuesta'] == 'Numeral')
                                <div class="row p-2"><input class="ms-2 col-6 resp" size="4" type='number' size='10' min="0" name="RES_respuesta" value="{{$elemento['RES_respuesta']}}">
                                    <span class="col-1 marca"></span>
                                </div>
                            @endif
                            @if ($elemento['BCP_tipoRespuesta'] == 'Respuesta corta')
                                <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$elemento['RES_respuesta']}}">
                                    <span class="col-1 marca"></span>
                                </div>
                            @endif
                            @if ($elemento['BCP_tipoRespuesta'] == 'Respuesta larga')
                                <div  class='row p-2'>
                                    {{-- <input class="col resp" type='text' name="RES_respuesta" value="{{$elemento['RES_respuesta']}}"> --}}
                                    <textarea name="RES_respuesta" class="col resp" rows="3" cols="30">
                                        {{$elemento['RES_respuesta']}}
                                    </textarea>
                                    {{-- <span class="col-1 marca"></span> --}}
                                </div>
                            @endif
                            {{-- </div> --}}
                            @if ( $elemento['BCP_complemento'])
                                <div class="row complemento px-3 py-1"> {{ $elemento['BCP_complemento'] }} <input type="text" name='RES_complemento' value="{{$elemento['RES_complemento']}}"></div>

                            @endif
                            @if ( $elemento['BCP_adjunto'] != null || $elemento['BCP_adjunto'] != '' )

                                <span>{{$elemento['BCP_adjunto']}}</span>
                                <div class="row complemento px-3 py-1">
                                    <input type="file" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" class="archivo-{{$elemento['RBF_id']}}" capture name='RES_adjunto[]' multiple>
                                    <input type="hidden" name="ARC_descripcion" value="{{$elemento['BCP_elemento']}}">

                                    {{-- Si existen archivos se hace una iteracion --}}
                                    <div class="col">
                                        @include('includes.archivos', ['archivos' => $archivos, 'id' =>  $elemento['RES_id'] ])
                                    </div>
                                </div>
                                <span class="btn btn-success btn-sm text-light d-none spiner-{{$elemento['RBF_id']}}" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Cargando archivo...
                                </span>
                                <span class="d-none text-success archivo-correcto" style="height: 20px;">
                                    <i class="bi bi-check-circle"></i> Archivo almacenado correctamente!
                                </span>
                            @endif
                            <input type="hidden" name="RES_tipoRespuesta" value="{{$elemento['BCP_tipoRespuesta']}}">
                            <input type="hidden" name="RES_complementoRespuesta" value="{{$elemento['BCP_complemento']}}">
                            <input type="hidden" name="FK_RBF_id" value="{{$elemento['RBF_id']}}">
                            <input type="hidden" name="FK_AGF_id" value="{{$elemento['AGF_id']}}">
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        {{-- ultimo elemento del carrusel --}}
        <div class="carousel-item" id="card_{{ count($elementos)+1 }}">
            <div class="card text-white bg-success mb-3" style="max-height: 18rem;">
                <div class="card-header fs-4">Fin del cuestionario</div>
                <div class="card-body">
                    <h5 class="card-title">Por favor revise los siguientes datos</h5>
                    <ol>
                    <li>Aseguresé de que la información es correcta y confiable</li>
                    <li>Verifique que todas las preguntas hayan sido respondidas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- <div class="col-2" > </div> --}}
        <span class="badge rounded-pill bg-success col-2 " id="conteo"></span>
        <div class="col">
            <div class="progress">
                </small><div class="progress-bar progress-bar-striped progress-bar-animated" id="pb_preguntas" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
            </div>
        </div>
    </div>


    <div class="container mt-2">
        <span class="btn btn-primary text-light" id="btn_anterior" >
            Anterior
        </span>
        <span class="btn btn-primary text-light" id="btn_siguiente" >
            Siguiente
        </span>
        <span class="btn btn-success text-light d-none" id="btn_fin" data-bs-slide="next">
            Confirmar y finalizar
        </span>
    </div>
    <hr>

<script>
    $(document).ready(function() {
        // desabilita boton anterior en la primera pantalla
        if( $("#card_1").hasClass("active") ){
            $("#btn_anterior").css("pointer-events", "none");
        }

        $("#btn_anterior, #btn_siguiente").click( function(e){
            div_activo( $(this) );
        });

        // Determina el div activo y ejecuta el avance o retroceso de del slide
        function div_activo( item ){
            let activo = item.parent().parent().find('.active').attr('id');
            var anterior = parseInt((item.parent().parent().find('.active').attr('id')).replace(/[^0-9]/g,''));

            console.log( activo, anterior );

            if( item.attr('id') == "btn_anterior" ){
                var actual = anterior-1;
                if(anterior > actual){
                    var anterior = anterior-2;
                }
                avance(item.attr('id'));
                barra(actual, anterior);
            }
            if( item.attr('id') == "btn_siguiente") {
                var actual = anterior+1;
                validaciones(item.attr('id'), activo, actual, anterior);
            }

            // desabilita boton anterior en la primera pantalla
            if( actual == 1 ){
                $("#btn_anterior").css("pointer-events", "none");
            } else {
                $("#btn_anterior").css("pointer-events", "auto");
            }
        }

        function avance(item){
            if( item == "btn_anterior" ){
                $("#carousel_preguntas").carousel("prev");
            }
            if( item == "btn_siguiente"){
                $("#carousel_preguntas").carousel("next");
            }
        }

        function barra(actual=null, anterior=null){
            // console.log('act>'+actual, 'ant>'+anterior);
            var total = {{ count($elementos) }}
            // console.log(total);

            let progreso = Math.floor((anterior/total)*100);
            $("#pb_preguntas").css("width",progreso+"%");
            $("#pb_preguntas").text(progreso+"%");
            $("#text_progreso").text(actual+'/'+total);

            // deshabilita boton siguiente en la ultima pantalla
            if( actual == total+1 ){
                $("#btn_siguiente").addClass('d-none');
                $("#btn_fin").removeClass('d-none')
            } else {
                $("#btn_siguiente").removeClass('d-none');
                $("#btn_fin").addClass('d-none')
            }
            $('#conteo').empty();
            $('#conteo').text(actual+'/'+(total+1));
        }

        function mensaje(item, actual=null, anterior=null){
            Swal.fire({
                title: 'No se respondió a ésta pregunta!',
                showDenyButton: true,
                denyButtonText: 'Continuar sin responder',
                confirmButtonText: 'Responder',
            }).then((result) => {
                if (result.isDenied) { //avanza
                    avance(item);
                    barra(actual, anterior);
                } else if (result.isConfirmed) { //no avanza
                    if( $("#card_1").hasClass("active") ){
                        $("#btn_anterior").css("pointer-events", "none");
                    }
                }
            });
        }
        function validaciones(item, activo, actual=null, anterior=null){
            $ok=false;
            /*Validacion para input tipo text, number etiqueta*/
            $("#"+activo).find('input.resp').each(function(e){
                if( $(this).val() == '' ){
                    mensaje(item, actual, anterior);
                } else {
                    $ok=true;
                    // avance(item);
                    // barra(actual, anterior);
                }
            });

            /*Validacion para input TEXAREA*/
            $("#"+activo).find('textarea.resp').each(function(e){
                if( $(this).text() == '' ){
                    mensaje(item, actual, anterior);
                } else {
                    $ok=true;
                    // avance(item);
                    // barra(actual, anterior);
                }
            });

            /*Validacion para radiobuttons*/
            $("#"+activo).find("div.group-radio").each(function(e){
                if( $(this).find("input[name='RES_respuesta']:radio").is(':checked')) {

                    /* evalua si la respuesta tiene alguna condicion de salto y realiza el salto correspondiente*/
                    var salto = $('#'+activo+' .salto').val();
                    if( salto ){
                        var salto = jQuery.parseJSON($('#'+activo+' .salto').val());
                        var resultado = $(this).find("input[name='RES_respuesta']:checked").val();
                        jQuery.each(salto, function(key, value) {
                            // console.log(String(key), String(resultado));
                            if( String(key) == String(resultado) ){
                                $('#BCP_id_'+value).parent().addClass('active');
                                // console.log( actual );
                                $('#card_'+(actual-1) ).removeClass('active');
                                barra(actual, anterior);

                                if( String(resultado) == 'Finalizar cuestionario' ){
                                    $('#card_'+value).addClass('active');
                                    console.log(resultado, 'valor '+value, actual, anterior );
                                    $('#card_'+(actual-1) ).removeClass('active');
                                    barra(value, value);
                                }
                            }
                            // id="card_{{ count($elementos)+1 }}"


                        });
                    } else {
                        $ok=true;
                        // avance(item);
                        // barra(actual, anterior);
                    }
                } else {
                    mensaje(item, actual, anterior);
                }
            });

            /*Validación para checkbox*/
            var checks = [];
            $("#"+activo).find("div.group-check").each(function(e){
                // $(this).each(function(e){
                    $(this).find('input').each(function(){
                        if ($(this).prop('checked')) {
                            checks.push(1)
                        } else {
                            checks.push(0)
                        }
                    });
                // })
                if( checks.includes(1) ){
                    $ok=true;
                    // avance(item);
                    // barra(actual, anterior);
                } else {
                    mensaje(item, actual, anterior);
                }
            });

            if( $ok == true ){
                avance(item);
                barra(actual, anterior);
            }
        }



        /*Evento para confirmar datos en la ultima pantalla*/
        $("#btn_fin").click( function(e){
            confirmaCuestionario( {{$elemento['FRM_id']}} );
        });
    });

</script>


{{-- cuando es el penultimo slide y se da responder se muestra el boton confirmar --}}
