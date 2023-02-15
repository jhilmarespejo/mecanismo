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

    @mobile
    <div class="container-fluid row border-top border-bottom p-3">
        <div class="col">
            <a class="text-decoration-none" href="/establecimientos/historial/{{$est_id}}" >
                <i class="bi bi-arrow-90deg-left"></i> Historial
            </a>
        </div>
        <div class="col">
            <a class="text-decoration-none" href="/formulario/adjuntos/{{$est_id}}" >
                <i class="bi bi-folder-symlink"></i> Archivos adjuntos
            </a>
        </div>
    </div>
    @endmobile
    @desktop
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/establecimientos/historial/{{$est_id}}" >
                        <i class="bi bi-arrow-90deg-left"></i> Historial
                    </a>
                </li>
                {{-- <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/imprimir/{{$rec->FK_FRM_id}}" >
                        <i class="bi bi-printer"></i> Vista para imprimir formulario</span>
                    </a>
                </li>
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/cuestionario/responder/{{$rec->FK_FRM_id}}" >
                        <i class="bi bi-ui-checks-grid"></i> Responder cuestionario
                    </a>
                </li> --}}
                <li class="nav-item p-1 px-3">
                    <a class="text-decoration-none" href="/formulario/adjuntos/{{$est_id}}" >
                        <i class="bi bi-folder-symlink"></i> Archivos adjuntos
                    </a>
                </li>
                </ul>
            </div>
        </div>
    </nav>
    @enddesktop
        {{-- SUBMENU  --}}

    <h2 class="text-center py-2 text-primary">Historial de Observaciones</h2>
    <h3 class="text-center py-2 text-primary">{{ $establecimiento->EST_nombre; }}</h3>

    @include('includes.recomendaciones', ['nueva_recomendacion' => true])
@endsection
