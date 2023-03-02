@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')
@php
$aux = null;
$a = [];
$archivosRec = [];
$archivosRecAcato = [];

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
    $a=0;
} // END FOREACH

// dump($auxCategoriasArray);

    // foreach ($auxCategoriasArray as $key => $values) {
    //     echo 'Categoria: '.$key.'<br/>';
    //     foreach ($values as $keySC=>$subcategorias ){
    //         if ( is_string($keySC) ){
    //             echo '- SUB Categoria: '.$keySC.'<br/>';
    //             foreach ($subcategorias as $k=>$pregunta){
    //                 echo '--'.$pregunta->BCP_pregunta.'<br>';
    //             }
    //         } else {
    //             echo '--'.$subcategorias->BCP_pregunta.'<br>';
    //         }
    //     }
    // }
@endphp

@if ( count($elementos) > 0 )
        {{-- minimenu --}}
        @mobile
        <div class="container-fluid row border-top border-bottom p-3">
            <div class="col ">
                {{-- <a class="text-decoration-none fs-4" href="/establecimientos/historial/{{$elemento->EST_id}}" >
                <i class="bi bi-arrow-90deg-left"></i> </a> --}}
                <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i> </a>
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
                        {{-- <a class="text-decoration-none" href="/establecimientos/historial/{{$elemento->EST_id}}" >
                            <i class="bi bi-arrow-90deg-left"></i> Historial </a> --}}
                            <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i> Volver atrás</a>
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
        <div class="text-center head mb-4">
            <p class="m-0 p-0 fs-3" id="establecimiento">{{ $elemento->EST_nombre }}</p>
            <p class="text-primary m-0 p-0 fs-3" id="titulo"> {{ $elemento->FRM_titulo }}</p>
            <p class="text-primary m-0 p-0 fs-5" id="titulo">Responder/llenar cuestionario: {{ $elemento->FRM_version }}</p>
        </div>

        @foreach ($auxCategoriasArray as $key => $values)
        <div class="accordion" id="respuestas">
            <div class="accordion-item">
                <h2 class="accordion-header" id="elemento_{{ $a }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#elemento-{{$a}}" aria-expanded="false" aria-controls="elemento-{{$a}}">
                        {{ $key; }}
                    </button>
                </h2>
                <div id="elemento-{{$a}}" class="accordion-collapse collapse" aria-labelledby="elemento_{{ $a }}" data-bs-parent="#respuestas">
                    <div class="accordion-body">
                        <ul class="list-group list-group-flush">
                        @foreach ($values as $keySC=>$subcategorias )
                            @if ( is_string($keySC) )
                                <strong>{{ 'SUB Categoria: '.$keySC }}</strong>
                                    @foreach ($subcategorias as $key=>$pregunta)
                                    <li class="list-group-item hover">{{ $pregunta->BCP_pregunta }}:  <strong>
                                        @php
                                            $resp_array = json_decode($pregunta->RES_respuesta, true);
                                        @endphp
                                        @if ( is_array( $resp_array ) )
                                            <ul class="ps-4">
                                                @foreach ( $resp_array as $k=>$respuetaArray )
                                                    <li>{{ $respuetaArray }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="ps-4">{{$pregunta->RES_respuesta}}</p>
                                        @endif
                                    </strong>
                                    </li>

                                    @endforeach
                            @else
                                <li class="list-group-item hover">{{ $subcategorias->BCP_pregunta }}:
                                    <strong>
                                        @php
                                            $respArray = json_decode($subcategorias->RES_respuesta, true);
                                        @endphp
                                        @if ( is_array( $respArray ) )
                                            <ul class="ps-4">
                                                @foreach ( $respArray as $k=>$respuetaArray )
                                                    <li>{{ $respuetaArray }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="ps-4">{{$subcategorias->RES_respuesta}}</p>
                                        @endif
                                    </strong>
                                </li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
        @php $a++; @endphp

    @endforeach
@endif




<script>

    $(document).ready(function() {
        $('#accordion_observaciones').toggle("slow");
        $('#div_adjuntos').toggle("slow");
    });

</script>

@endsection

