<div id="frm_cuestionario">
    <ol id="q" >
        @php $c = 1; @endphp
        @foreach ($auxCategoriasArray as $keyCat=>$categorias)
        <li>
            <strong id="categorias">{{ $keyCat }}</strong>
            {{-- cuando la categoria TIENE subcategoria (SC) --}}
            <ol>
                @foreach ($categorias as $keySC=>$subcategorias )
                @if ( is_string($keySC) )
                <li class="mt-1" id="con_subcategorias">
                    {{-- Nombre de la Subcategoría --}}
                    <strong >{{ $keySC }}</strong>
                    <ul class="list-unstyled">
                        @foreach ($subcategorias as $key=>$pregunta)
                            <li>
                                <div class=" row border-bottom py-3 hover p-2 elementos">
                                    <div class="col-sm-5 col-preguntas-sc" >
                                        {{ $c. '. ' .$pregunta->BCP_pregunta }}
                                        @if ($pregunta->BCP_tipoRespuesta == 'Afirmación' || $pregunta->BCP_tipoRespuesta == 'Lista desplegable')
                                        @endif
                                    </div>

                                    <div class="col-sm-7 col-respuestas-sc">
                                        {{-- <div class="row "> --}}
                                            <form method="POST" enctype="multipart/form-data" id="frm_{{$pregunta->RBF_id}}" class="frm-respuesta"> @csrf
                                                @php
                                                $opcionesSC = json_decode( $pregunta->BCP_opciones, true);
                                                $respuestasSC = json_decode( $pregunta->RES_respuesta, true);
                                                if ($respuestasSC === null) { $respuestasSC = []; }
                                                // dump($preg->RES_respuesta, $opciones)
                                                @endphp
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
                        @php $c++; @endphp
                        @endforeach
                    </ul>
                </li>
                @endif
                @endforeach
            </ol>
                {{-- cuando la categoria NO tiene subcategoria --}}
            <ul class="list-unstyled">
                @foreach ($categorias as $keyP=>$preg )
                    @if ( !is_string($keyP) )
                        <li class="mt-1" id="sin_preguntas">
                            <div class="row border-bottom py-3 hover elementos">
                                <div class="col-sm-5 col-preguntas" >
                                    {{ $c. '. ' .$preg->BCP_pregunta }}
                                    {{-- @if ($preg->BCP_tipoRespuesta == 'Afirmación' || $preg->BCP_tipoRespuesta == 'Lista desplegable')
                                    @endif --}}
                                </div>
                                <div class="col-sm-7 col-respuestas">
                                    <form method="POST" enctype="multipart/form-data" id="frm_{{$preg->RBF_id}}" class="frm-respuesta">@csrf
                                        @php
                                            $opciones = json_decode( $preg->BCP_opciones, true);
                                            $respuestas = json_decode( $preg->RES_respuesta, true);
                                            if ($respuestas === null) { $respuestas = []; }
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
                                            <div class="row p-2"><input class="ms-2 col resp" type='number' size='10' min="0" name="RES_respuesta" value="{{$preg->RES_respuesta}}">
                                                <span class="col-1 col-1 marca"></span>
                                            </div>
                                        @endif
                                        @if ($preg->BCP_tipoRespuesta == 'Respuesta corta')
                                            <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$preg->RES_respuesta}}">
                                                <span class="col-1 marca"></span>
                                            </div>
                                        @endif
                                        @if ($preg->BCP_tipoRespuesta == 'Respuesta larga')
                                            <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$preg->RES_respuesta}}">
                                                <span class="col-1 marca"></span>
                                            </div>
                                        @endif
                                        {{-- </div> --}}
                                        @if ( $preg->BCP_complemento )
                                            <div class="row complemento px-3 py-1"> {{ $preg->BCP_complemento }}
                                                <input type="text" name='RES_complemento' value="{{$preg->RES_complemento}}">
                                            </div>
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
                                </div>
                            </div>
                        </li>
                    @endif
                    @php $c++; @endphp
                @endforeach
            </ul>
                {{-- </li> --}}
                @endforeach
            {{-- </ol> --}}
            <div class="row m-2 d-flex">
                <span class="btn btn-primary text-light text-shadow box-shadow" id="btn_confirmacion">Confirmar datos</span>
                <small class="alert alert-danger d-none" id="msg_vacios">¡Existen campos vacíos!</small>
            </div>
        </div>

<script>
    /*Boton para confirmar los datos del formulario*/
    $("#btn_confirmacion").click( function(e){
        validar();
        confirmaCuestionario( {{$elemento->FRM_id}} )
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

{{-- hotel viena --}}
