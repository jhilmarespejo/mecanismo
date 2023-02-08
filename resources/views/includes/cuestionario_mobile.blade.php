{{-- @dump($elementos->toArray()) --}}
<div id="carousel_preguntas" class="carousel slide" data-bs-interval="false">
    <div class="carousel-inner">
        @foreach ($elementos as $k=>$elemento)
            <div class="carousel-item {{ ($k==0)? 'active': '' }}" id="card_{{$k+1}}">
                <div class="card border mb-3" >
                    <div class="card-header" >
                        <dl>
                            @php
                                if( $elemento->categoriaID == null ){
                                    $categoria = $elemento->subcategoria;
                                    $subcategoria = null;
                                } else {
                                    $categoria = $elemento->categoria;
                                    $subcategoria = $elemento->subcategoria;
                                }
                            @endphp
                            <dd> <b>Categoría: </b>{{ $categoria }}</dd>
                            @if( $subcategoria != null)
                            <dd> <b>Subcategoría: </b>{{$subcategoria}}</dd>
                            @endif

                        </dl>
                    </div>
                    <div class="card-body">
                        <p class="card-title fs-4"><b><small>{{$k+1}}/{{count($elementos)}}</small></b>. {{$elemento->BCP_pregunta}} </p>

                    <form method="POST" enctype="multipart/form-data" id="frm_{{$elemento->RBF_id}}" class="frm-respuesta"> @csrf
                        @php
                        $opcionesSC = json_decode( $elemento->BCP_opciones, true);
                        $respuestasSC = json_decode( $elemento->RES_respuesta, true);
                        if ($respuestasSC === null) { $respuestasSC = []; }
                        // dump($preg->RES_respuesta, $opciones)
                        @endphp
                        @if ( is_array($opcionesSC) )
                        <div class="{{($elemento->BCP_tipoRespuesta == 'Casilla verificación')? 'group-check' : 'group-radio'}}" >
                            @foreach ($opcionesSC as $opcion)
                            @if ($elemento->BCP_tipoRespuesta == 'Casilla verificación')
                            <div class="col-auto d-flex">
                                <input {{ in_array($opcion, $respuestasSC)? 'checked':'' }} type='checkbox' name="RES_respuesta[]" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                            </div>
                            @elseif ( $elemento->BCP_tipoRespuesta == 'Afirmación' || $elemento->BCP_tipoRespuesta == 'Lista desplegable' )
                            <div class="col-auto d-flex">
                                <input {{ ($elemento->RES_respuesta == $opcion)? 'checked':'' }} type='radio' name="RES_respuesta" value="{{ $opcion }}"> &nbsp;{{ $opcion }}
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endif

                        @if ($elemento->BCP_tipoRespuesta == 'Numeral')
                        <div class="row p-2"><input class="ms-2 col resp" type='number' size='10' min="0" name="RES_respuesta" value="{{$elemento->RES_respuesta}}"><span class="col-1 marca"></span> </div>
                        @endif
                        @if ($elemento->BCP_tipoRespuesta == 'Respuesta corta')
                        <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$elemento->RES_respuesta}}"> <span class="col-1 marca"></span> </div>
                        @endif
                        @if ($elemento->BCP_tipoRespuesta == 'Respuesta larga')
                        <div  class='row p-2'><input class="col resp" type='text' name="RES_respuesta" value="{{$elemento->RES_respuesta}}"><span class="col-1 marca"></span> </div>
                        @endif
                        {{-- </div> --}}
                        @if ( $elemento->BCP_complemento)
                        <div class="row complemento px-3 py-1"> {{ $elemento->BCP_complemento }} <input type="text" name='RES_complemento' value="{{$elemento->RES_complemento}}"></div>
                        @endif
                        @if ( $elemento->BCP_adjunto != null || $elemento->BCP_adjunto != '' )

                        <span>{{$elemento->BCP_adjunto}}</span>
                        <div class="row complemento px-3 py-1">
                            <input type="file" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" class="archivo-{{$elemento->RBF_id}}" capture name='RES_adjunto[]' multiple>
                            <input type="hidden" name="ARC_descripcion" value="{{$elemento->BCP_elemento}}">

                            {{-- Si existen archivos se hace una iteracion --}}
                            <div class="col">
                                @include('includes.archivos', ['archivos' => $archivos, 'id' =>  $elemento->RES_id ])
                            </div>
                        </div>
                        <span class="btn btn-success btn-sm text-light d-none spiner-{{$elemento->RBF_id}}" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Cargando archivo...
                        </span>
                        <span class="d-none text-success archivo-correcto" style="height: 20px;">
                            <i class="bi bi-check-circle"></i> Archivo almacenado correctamente!
                        </span>
                        @endif
                        <input type="hidden" name="RES_tipoRespuesta" value="{{$elemento->BCP_tipoRespuesta}}">
                        <input type="hidden" name="RES_complementoRespuesta" value="{{$elemento->BCP_complemento}}">
                        <input type="hidden" name="FK_RBF_id" value="{{$elemento->RBF_id}}">
                    </form>
                    </div>
                    </div>
                    {{-- <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated p-2" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%"></div>
                    </div> --}}
            </div>
        @endforeach
    </div>
    <div class="container mt-2">
        <span class="btn btn-primary text-light" id="anterior" data-bs-target="#carousel_preguntas" data-bs-slide="prev">
            Anterior
        </span>
        <span class="btn btn-primary text-light" id="siguiente" data-bs-target="#carousel_preguntas" data-bs-slide="next">
        Siguiente
        </span>
    </div>
</div>

<script>
    $(document).ready(function() {
        if( $("#card_1").hasClass("active") ){
            $("#anterior"). attr("disabled", true)
        }else{
            console.log('inactivo');
        }
    });
</script>
