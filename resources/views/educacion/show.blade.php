@extends('layouts.app')
@section('title', 'Detalles de Actividad Educativa')

@section('content')
<div class="container mt-3 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">Detalles de la Actividad Educativa</h1>
        <div>
            <a href="{{ route('educacion.edit', $educacion->EDU_id) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil-square me-2"></i>Editar
            </a>
            <a href="{{ route('educacion.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @include('layouts.breadcrumbs', $breadcrumbs)

    <div class="row">
        <!-- Información Principal -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong class="text-muted">ID:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-secondary fs-6">{{ $educacion->EDU_id }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong class="text-muted">Tema:</strong>
                        </div>
                        <div class="col-sm-9">
                            <p class="mb-0">{{ $educacion->EDU_tema }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong class="text-muted">Beneficiarios:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-info fs-6">{{ $educacion->EDU_beneficiarios }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong class="text-muted">Cantidad:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-success fs-5">{{ $educacion->EDU_cantidad_beneficiarios }} personas</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong class="text-muted">Ciudad:</strong>
                        </div>
                        <div class="col-sm-9">
                            <i class="bi bi-geo-alt text-primary me-2"></i>{{ $educacion->EDU_ciudad }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong class="text-muted">Gestión:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="badge bg-warning text-dark fs-6">{{ $educacion->EDU_gestion }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong class="text-muted">Medio de Verificación:</strong>
                        </div>
                        <div class="col-sm-9">
                            <p class="mb-0 text-justify">{{ $educacion->EDU_medio_verificacion }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fechas y Duración -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Cronograma</h5>
                </div>
                <div class="card-body">
                    @if($educacion->EDU_fecha_inicio && $educacion->EDU_fecha_fin)
                        <div class="text-center mb-3">
                            <div class="mb-2">
                                <strong class="text-muted">Fecha de Inicio</strong>
                                <div class="fs-5 text-primary">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    {{ \Carbon\Carbon::parse($educacion->EDU_fecha_inicio)->format('d/m/Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($educacion->EDU_fecha_inicio)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                </small>
                            </div>

                            <div class="my-3">
                                <i class="bi bi-arrow-down text-muted fs-4"></i>
                            </div>

                            <div class="mb-2">
                                <strong class="text-muted">Fecha de Fin</strong>
                                <div class="fs-5 text-primary">
                                    <i class="bi bi-calendar-x me-2"></i>
                                    {{ \Carbon\Carbon::parse($educacion->EDU_fecha_fin)->format('d/m/Y') }}
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($educacion->EDU_fecha_fin)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                </small>
                            </div>
                        </div>

                        @php
                            $fechaInicio = \Carbon\Carbon::parse($educacion->EDU_fecha_inicio);
                            $fechaFin = \Carbon\Carbon::parse($educacion->EDU_fecha_fin);
                            $duracion = $fechaFin->diffInDays($fechaInicio) + 1;
                        @endphp

                        <div class="alert alert-info text-center">
                            <strong>Duración:</strong> {{ $duracion }} 
                            {{ $duracion == 1 ? 'día' : 'días' }}
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Fechas no registradas
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
    </div>

    <!-- Archivos de Respaldo -->
    @if($educacion->EDU_imagen_medio_verificacion)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Archivos de Respaldo</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $archivos = json_decode($educacion->EDU_imagen_medio_verificacion, true);
                            if (!is_array($archivos)) {
                                $archivos = [$educacion->EDU_imagen_medio_verificacion];
                            }
                        @endphp

                        <div class="row">
                            @foreach($archivos as $index => $archivo)
                                @php
                                    $rutaCompleta = storage_path('app/public/' . $archivo);
                                    $rutaPublica = asset('storage/' . $archivo);
                                    $nombreArchivo = basename($archivo);
                                    $extension = pathinfo($archivo, PATHINFO_EXTENSION);
                                    $tipoArchivo = 'documento';
                                    
                                    if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                        $tipoArchivo = 'imagen';
                                    } elseif (in_array(strtolower($extension), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'])) {
                                        $tipoArchivo = 'video';
                                    } elseif (in_array(strtolower($extension), ['mp3', 'wav', 'ogg', 'aac', 'flac'])) {
                                        $tipoArchivo = 'audio';
                                    } elseif ($extension == 'pdf') {
                                        $tipoArchivo = 'pdf';
                                    }
                                @endphp

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            @if($tipoArchivo == 'imagen' && file_exists($rutaCompleta))
                                                <img src="{{ $rutaPublica }}" class="img-fluid mb-2" 
                                                     style="max-height: 150px; cursor: pointer;" 
                                                     onclick="verImagen('{{ $rutaPublica }}', '{{ $nombreArchivo }}')"
                                                     alt="Imagen de respaldo">
                                            @elseif($tipoArchivo == 'pdf')
                                                <i class="bi bi-file-pdf text-danger" style="font-size: 3rem;"></i>
                                            @elseif($tipoArchivo == 'video')
                                                <i class="bi bi-play-circle text-primary" style="font-size: 3rem;"></i>
                                            @elseif($tipoArchivo == 'audio')
                                                <i class="bi bi-music-note text-info" style="font-size: 3rem;"></i>
                                            @else
                                                <i class="bi bi-file-earmark text-secondary" style="font-size: 3rem;"></i>
                                            @endif
                                            
                                            <h6 class="card-title mt-2">{{ Str::limit($nombreArchivo, 20) }}</h6>
                                            <small class="text-muted">{{ strtoupper($extension) }}</small>
                                            
                                            <div class="mt-2">
                                                @if($tipoArchivo == 'imagen')
                                                    <button class="btn btn-sm btn-primary me-1" 
                                                            onclick="verImagen('{{ $rutaPublica }}', '{{ $nombreArchivo }}')">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </button>
                                                @elseif($tipoArchivo == 'pdf')
                                                    <button class="btn btn-sm btn-primary me-1" 
                                                            onclick="verPDF('{{ $rutaPublica }}', '{{ $nombreArchivo }}')">
                                                        <i class="bi bi-eye"></i> Ver
                                                    </button>
                                                @endif
                                                
                                                <a href="{{ $rutaPublica }}" download class="btn btn-sm btn-success">
                                                    <i class="bi bi-download"></i> Descargar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal para ver imágenes -->
<div class="modal fade" id="modalImagen" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitle">Ver Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagenSrc" src="" class="img-fluid" alt="Imagen">
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver PDFs -->
<div class="modal fade" id="modalPDF" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPDFTitle">Ver PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="modalPDFSrc" src="" width="100%" height="600px"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function verImagen(src, nombre) {
    document.getElementById('modalImagenSrc').src = src;
    document.getElementById('modalImagenTitle').textContent = nombre;
    new bootstrap.Modal(document.getElementById('modalImagen')).show();
}

function verPDF(src, nombre) {
    document.getElementById('modalPDFSrc').src = src;
    document.getElementById('modalPDFTitle').textContent = nombre;
    new bootstrap.Modal(document.getElementById('modalPDF')).show();
}
</script>

<style>
.text-justify {
    text-align: justify;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.badge {
    font-size: 0.9em;
}
</style>
@endsection