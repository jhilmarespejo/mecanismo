<div id="frm_cuestionario">
    <ol id="q" >
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
                                    @endif
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
                @endforeach
            </ol>
                {{-- </li> --}}
                @endforeach
            {{-- </ol> --}}
            <div class="row m-2 d-flex">
                <span class="btn btn-primary text-light text-shadow box-shadow" id="btn_confirmacion">Confirmar datos</span>
                <small class="alert alert-danger d-none" id="msg_vacios">¡Existen campos vacíos!</small>
            </div>
        </div>
