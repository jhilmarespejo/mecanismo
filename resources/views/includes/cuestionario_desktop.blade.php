@php
    $elemento = $elementos;
@endphp
<div id="frm_cuestionario">
    {{-- <ol id="q" > --}}
        @php $c = 1; @endphp
        {{-- @foreach ($elementos_categorias as $key => $elemento) --}}
            {{-- <li class="">
                <strong id="categorias" class="mb-2">{{ strtoupper($key) }}</strong>
            </li> --}}
            {{--subcategoria --}}
            {{-- <ul class="list-unstyled">
                <li class="mt-1" id="con_subcategorias"> --}}
                    <ul class="list-unstyled" id="q">
                        @foreach($elemento as $k => $item)
                            {{-- <li class="subCategoria_{{$item['categoriaID']}} RBF_id_{{$item['RBF_id']}}" id="BCP_id_{{$item['BCP_id']}}"> --}}
                                <div class=" row border-bottom py-3 hover p-2 elementos">
                                    <div class="col-sm-5 col-preguntas-sc">

                                        {{-- PREGUNTAS --}}
                                        @if ($item['BCP_tipoRespuesta'] == 'Etiqueta')
                                            <div class="alert alert-danger" role="alert">
                                                {{ $item['BCP_pregunta'] }}
                                            </div>
                                        @else
                                            {{ $c. '. ' .$item['BCP_pregunta'] }}
                                        @endif
                                    </div>
                                    {{-- RESPUESTAS --}}
                                    <div class="col-sm-7 col-respuestas-sc">
                                        <form method="POST" enctype="multipart/form-data" id="frm_{{$item['RBF_id']}}" class="frm-respuesta"> @csrf
                                            @php
                                                $opcionesSC = json_decode( $item['BCP_opciones'], true);
                                                $respuestasSC = json_decode( $item['RES_respuesta'], true);
                                                if ($respuestasSC === null) { $respuestasSC = []; }
                                                // dump($preg->RES_respuesta, $opciones)
                                            @endphp
                                            @if ( is_array($opcionesSC) )
                                            <input type="hidden" class="salto" value="{{$item['RBF_salto_FK_BCP_id']}}">
                                            <div class="{{($item['BCP_tipoRespuesta'] == 'Casilla verificación')? 'group-check' : 'group-radio'}}" >
                                                @foreach ($opcionesSC as $opcion)
                                                    @if ($item['BCP_tipoRespuesta'] == 'Casilla verificación')
                                                        <div class="col-auto d-flex">
                                                            <input {{ in_array($opcion, $respuestasSC)? 'checked':'' }} type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                                        </div>
                                                        @elseif ( $item['BCP_tipoRespuesta'] == 'Afirmación' || $item['BCP_tipoRespuesta'] == 'Lista desplegable' )
                                                        <div class="col-auto d-flex">
                                                            <input {{ ($item['RES_respuesta'] == $opcion)? 'checked':'' }} type='radio' name="RES_respuesta" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            @endif
                                            @if ($item['BCP_tipoRespuesta'] == 'Numeral')
                                                <div class="row p-2"><input class="ms-2 col resp" type='number' size='10' min="0" name="RES_respuesta" value="{{$item['RES_respuesta']}}"><span class="col-1 marca"></span> </div>
                                            @endif
                                            @if ($item['BCP_tipoRespuesta'] == 'Respuesta corta')
                                                <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$item['RES_respuesta']}}"> <span class="col-1 marca"></span> </div>
                                            @endif
                                            @if ($item['BCP_tipoRespuesta'] == 'Respuesta larga')
                                                <div  class='row p-2'>
                                                    <textarea name="RES_respuesta" class="col resp" cols="30" rows="2">{{$item['RES_respuesta']}}</textarea>
                                                    <span class="col-1 marca"></span>
                                                </div>
                                            @endif
                                            {{-- </div> --}}
                                            @if ( $item['BCP_complemento'])
                                                <div class="row complemento px-3 py-1">
                                                    {{ $item['BCP_complemento'] }} <input type="text" name='RES_complemento' value="{{$item['RES_complemento']}}">
                                                </div>
                                            @endif
                                            @if ( $item['BCP_adjunto'] != null || $item['BCP_adjunto'] != '' )

                                                <span>{{$item['BCP_adjunto']}}</span>
                                                <div class="row complemento px-3 py-1">
                                                    <input type="file" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" class="archivo-{{$item['RBF_id']}}" capture name='RES_adjunto[]' multiple>
                                                    <input type="hidden" name="ARC_descripcion" value="{{$item['BCP_pregunta']}}">

                                                    {{-- Si existen archivos se hace una iteracion --}}
                                                    <div class="col">
                                                        @include('includes.archivos', ['archivos' => $archivos, 'id' =>  $item['RES_id'] ])
                                                    </div>
                                                </div>
                                                <span class="btn btn-success btn-sm text-light d-none spiner-{{$item['RBF_id']}}" disabled>
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Cargando archivo...
                                                </span>
                                                <span class="d-none text-success archivo-correcto" style="height: 20px;">
                                                    <i class="bi bi-check-circle"></i> Archivo almacenado correctamente!
                                                </span>
                                            @endif
                                            <input type="hidden" name="RES_tipoRespuesta" value="{{$item['BCP_tipoRespuesta']}}">
                                            <input type="hidden" name="RES_complementoRespuesta" value="{{$item['BCP_complemento']}}">
                                            <input type="hidden" name="FK_RBF_id" value="{{$item['RBF_id']}}">
                                            <input type="hidden" name="FK_AGF_id" value="{{$item['AGF_id']}}">
                                        </form>
                                    </div>
                                </div>
                            {{-- </li> --}}
                        @php $c++; @endphp
                        @endforeach
                    </ul>
                {{-- </li>
            </ul> --}}
        {{-- @endforeach --}}
    {{-- </ol> --}}
            <div class="row m-2 d-flex">
                <span class="btn btn-primary text-light text-shadow box-shadow" id="btn_confirmacion">Confirmar datos</span>
                <small class="alert alert-danger d-none" id="msg_vacios">¡Existen campos vacíos!</small>
            </div>
        </div>

<script>
    $("#q div.group-radio").change(function (e) {
        var salto =  jQuery.parseJSON($(this).siblings('.salto').val());
        var resultado = $(this).find("input[name='RES_respuesta']:checked").val();
        jQuery.each(salto, function(key, value) {
            if( String(key) == String(resultado) && String(resultado) != 'Finalizar cuestionario'){
                console.log(String(key), String(value));

                $('html,body').animate({
                    scrollTop: ($("#BCP_id_"+value).offset().top)-150
                }, 'slow');
            } if( String(resultado) == 'Finalizar cuestionario' ){
                console.log('fin');
                $('html,body').animate({
                    scrollTop: ($("#btn_confirmacion").offset().top)
                }, 'slow');
            }
        });

    //     // console.log( $(this).find("input[name='RES_respuesta']:checked").val() );
    });


    /*Boton para confirmar los datos del formulario*/
    $("#btn_confirmacion").click( function(e){
        validar();
        confirmaCuestionario( {{$FRM_id}} )
    });
    function validar(){
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
            var checks = [];
            // $(this).each(function(e){
                $(this).find('input').each(function(){
                    if ($(this).prop('checked')) {
                        checks.push(1)
                    } else {
                        checks.push(0)
                    }

                });
            // })
            if( !checks.includes(1) ){
                marcas.push($(this).closest('form').attr('id'));
            }
        });

        /*Validacion para input text, number*/
        $("#q").find('input.resp').each(function(e){
            if( $(this).val() == '' ){
                marcas.push($(this).closest('form').attr('id'));
            }
        })

        $.each(marcas, function(key, value){
            $('#'+value).addClass('bg-warning bg-gradient rounded');
        });
        if(marcas.length > 0){
            // $("html, body").animate( { scrollTop: "10" }, 3000);
            $('html,body').animate({
                scrollTop: ($('#'+$("#q").find('form.frm-respuesta.bg-warning').attr('id')).offset().top)-150
            }, 'slow');
        }
    }

    // guarda cada respuesta
    $(".frm-respuesta").focusout(function(e){
        e.preventDefault();
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        let formData = new FormData($('#frm_'+id)[0]);
        // console.log(formData);
        $.ajax({
            async: true,
            // headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: '/cuestionario/guardarRespuestasCuestionario',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            // dataType: 'json',
            beforeSend: function () {
                if( $('input.archivo-'+id).val() !== '' ){
                    $('.archivo-correcto').addClass('d-none');
                    $('input.archivo-'+id).addClass('d-none');
                    $('.spiner-'+id).removeClass('d-none');
                } else {

                }
            },
            success: function ( response ) {
            },
            complete : function( response ) {
                // console.log(response.responseJSON, response.status);
                if( response.responseJSON === 'correcto' ) {
                    console.log( response.responseJSON, response.status );
                    // $('#frm_'+id).children('div').find('input.resp').removeClass('border border-2 border-danger');
                    // $('#frm_'+id).children('div').find('span.marca').empty();
                    // $('#frm_'+id).children('div.complemento i').empty();
                } if( response.message === 'archivos_correcto' ) {
                    console.log('archivos_correcto');
                    // $('.spiner-'+id).addClass('d-none');
                    // $('.archivo-correcto').removeClass('d-none');
                }
            },
            error: function(response){  }
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

</script>

