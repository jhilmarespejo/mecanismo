@foreach ($archivos as $k=>$archivo)


    {{-- @php
        if ( isset($archivo['FK_ARC_id']) ){
            $archivoId = $archivo['FK_ARC_id'];

        } elseif ( isset($archivo['FK_REC_id']) ){
            $archivoId = $archivo['FK_REC_id'];

        } elseif ( isset($archivo['FK_RES_id']) ){
            $archivoId = $archivo['FK_RES_id'];

        } elseif ( isset($archivo['FK_SREC_id']) ){
            $archivoId = $archivo['FK_SREC_id'];
        }
        // dump($archivo);exit;
    @endphp --}}


    {{-- @if ( $id == $archivoId ) --}}

        <div data-bs-toggle="modal" data-bs-target="#modal" class="getFileModal btn row">
            @if ( $archivo['ARC_formatoArchivo'] == 'image' )
                <img style="height: 50px" src="/{{ $archivo['ARC_ruta'] }}" class="img-thumbnail" alt="{{ $archivo['ARC_descripcion'] }}" title="{{ $archivo['ARC_descripcion'] }}">
                <p class="d-none descripcion">{{ $archivo['ARC_descripcion'] }}</p>
            @endif
            @if( $archivo['ARC_extension'] == 'pdf' )
                <i class="text-danger fs-1 bi bi-file-earmark-pdf-fill"></i>
                <img style="height: 50px" src="/{{ $archivo['ARC_ruta'] }}" class="d-none" alt="{{ $archivo['ARC_descripcion'] }}">
                <p class="d-none descripcion">{{ $archivo['ARC_descripcion'] }}</p>
            @endif
            @if( $archivo['ARC_formatoArchivo'] == 'application' && $archivo['ARC_extension'] != 'pdf' )
                <i class="text-danger fs-1 bi bi-file-earmark"></i>
                <img style="height: 50px" src="/{{ $archivo['ARC_ruta'] }}" class="d-none" alt="{{ $archivo['ARC_descripcion'] }}">
                <p class="d-none descripcion">{{ $archivo['ARC_descripcion'] }}</p>
            @endif
            @if ( $archivo['ARC_formatoArchivo'] == 'audio' )
                <i class="bi bi-cassette text-danger fs-1">&#9835;</i>
                <img style="height: 50px" src="/{{ $archivo['ARC_ruta'] }}" class="d-none" alt="{{ $archivo['ARC_descripcion'] }}">
            @endif
            @if ( $archivo['ARC_formatoArchivo'] == 'video' )
                <i class="text-danger fs-1 bi bi-camera-reels-fill"></i>
                <img style="height: 50px" src="/{{ $archivo['ARC_ruta'] }}" class="d-none" alt="{{ $archivo['ARC_descripcion'] }}">
            @endif
        </div>
    {{-- @endif --}}
@endforeach



