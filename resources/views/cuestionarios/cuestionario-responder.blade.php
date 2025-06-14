@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')

<style>
    .hover:hover {
        background-color: #eaeaea;
    }
    
    @media screen and (max-width: 380px) {
        ol, ul { padding-left: 10px; }
    }
    
    .header-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .device-info {
        position: fixed;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8em;
        z-index: 1000;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        flex-direction: column;
    }
</style>

<!-- Overlay de carga inicial -->
<div id="loading-overlay" class="loading-overlay d-none">
    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
    <h5>Cargando cuestionario...</h5>
    <p class="text-muted">Preparando las preguntas para su dispositivo</p>
</div>

<!-- Indicador de dispositivo (solo visible en desarrollo) -->
@if(config('app.debug'))
<div class="device-info d-none d-md-block">
    <i class="bi bi-display me-1"></i>Desktop
</div>
<div class="device-info d-md-none">
    <i class="bi bi-phone me-1"></i>Mobile
</div>
@endif

<div class="container-fluid p-sm-3 p-0 mx-0" id="cuestionario">
    @if(count($elementos) > 0)
        {{-- Navegación superior --}}
        @mobile
        <div class="container-fluid row border-top border-bottom p-3 bg-light">
            <div class="col-auto">
                <a href="javascript:history.back()" role="button" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
            <div class="col text-center">
                <strong class="text-primary">Modo Móvil</strong>
            </div>
            <div class="col-auto">
                <a class="btn btn-outline-primary btn-sm" 
                   href="/cuestionario/imprimir/{{ $VIS_id }}/{{ $FRM_id }}/{{ $AGF_id }}" 
                   target="_blank">
                    <i class="bi bi-printer"></i>
                </a>
            </div>
        </div>
        @endmobile

        @desktop
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <div class="navbar-nav flex-row gap-3">
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Página anterior
                    </a>
                    <a class="btn btn-outline-primary" 
                       href="/cuestionario/imprimir/{{ $VIS_id }}/{{ $FRM_id }}/{{ $AGF_id }}" 
                       target="_blank">
                        <i class="bi bi-printer me-2"></i>Imprimir
                    </a>
                    <div class="ms-auto">
                        <span class="badge bg-info">
                            <i class="bi bi-display me-1"></i>Modo Desktop
                        </span>
                    </div>
                </div>
            </div>
        </nav>
        @enddesktop

        {{-- Encabezado principal --}}
        <div class="header-info text-center">
            <h2 class="mb-2">
                <i class="bi bi-building me-2"></i>{{ $EST_nombre }}
            </h2>
            <h3 class="mb-2">{{ $FRM_titulo }}</h3>
            <p class="mb-0">
                <i class="bi bi-clipboard-check me-2"></i>
                @mobile
                    Cuestionario interactivo - Navegación por preguntas
                @endmobile
                @desktop
                    Cuestionario completo - Navegación por scroll
                @enddesktop
            </p>
            @if(isset($AGF_copia) && $AGF_copia > 1)
                <small class="badge bg-warning text-dark mt-2">
                    <i class="bi bi-files me-1"></i>Copia {{ $AGF_copia }}
                </small>
            @endif
        </div>

        {{-- Estadísticas del cuestionario --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body p-3">
                        <div class="row text-center">
                            @php
                                $total_elementos = count($elementos);
                                $preguntas_normales = collect($elementos)->whereNotIn('BCP_tipoRespuesta', ['Sección', 'Subsección', 'Seccion', 'Subseccion', 'Etiqueta'])->count();
                                $secciones = collect($elementos)->whereIn('BCP_tipoRespuesta', ['Sección', 'Seccion'])->count();
                                $subsecciones = collect($elementos)->whereIn('BCP_tipoRespuesta', ['Subsección', 'Subseccion'])->count();
                            @endphp
                            
                            <div class="col">
                                <div class="h4 text-primary mb-0">{{ $preguntas_normales }}</div>
                                <small class="text-muted">Preguntas</small>
                            </div>
                            @if($secciones > 0)
                            <div class="col">
                                <div class="h4 text-info mb-0">{{ $secciones }}</div>
                                <small class="text-muted">Secciones</small>
                            </div>
                            @endif
                            @if($subsecciones > 0)
                            <div class="col">
                                <div class="h4 text-warning mb-0">{{ $subsecciones }}</div>
                                <small class="text-muted">Subsecciones</small>
                            </div>
                            @endif
                            <div class="col">
                                <div class="h4 text-success mb-0" id="contador-respondidas">0</div>
                                <small class="text-muted">Respondidas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cuestionario principal --}}
        <div class="row border rounded m-sm-2 p-2">
            <div class="col-12">
                @desktop
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <legend class="text-primary fs-3 mb-0">
                        <i class="bi bi-clipboard-data me-2"></i>Cuestionario Completo
                    </legend>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked 
                               id="toggle-cuestionario" 
                               onchange="toggleCuestionario()">
                        <label class="form-check-label" for="toggle-cuestionario">
                            Mostrar/Ocultar
                        </label>
                    </div>
                </div>
                @include('includes.cuestionario_desktop')
                @enddesktop
                
                @mobile
                <legend class="text-primary fs-4 text-center mb-3">
                    <i class="bi bi-phone me-2"></i>Cuestionario Interactivo
                </legend>
                @include('includes.cuestionario_mobile')
                @endmobile
            </div>
        </div>

        {{-- Información adicional (solo desktop) --}}
        @desktop
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <i class="bi bi-info-circle me-2"></i>Instrucciones
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Las respuestas se guardan automáticamente</li>
                            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Puede dejar preguntas sin responder si no aplican</li>
                            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Use el scroll para navegar entre preguntas</li>
                            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>El progreso se muestra en la barra lateral</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-lightbulb me-2"></i>Consejos
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-star text-warning me-2"></i>Lea cuidadosamente cada pregunta</li>
                            <li class="mb-2"><i class="bi bi-star text-warning me-2"></i>Use las aclaraciones como guía</li>
                            <li class="mb-2"><i class="bi bi-star text-warning me-2"></i>Adjunte archivos cuando sea necesario</li>
                            <li class="mb-2"><i class="bi bi-star text-warning me-2"></i>Confirme al finalizar todo el cuestionario</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @enddesktop

    @else
        {{-- Estado sin preguntas --}}
        <div class="text-center py-5">
            <div class="card border-warning">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Cuestionario no disponible</h3>
                    
                    @if(Auth::user()->rol == 'Administrador')
                        <p class="text-muted">Este cuestionario no tiene preguntas configuradas.</p>
                        <div class="mt-3">
                            <a class="btn btn-primary me-2" href="javascript:history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                            <a class="btn btn-outline-primary" href="#">
                                <i class="bi bi-gear me-2"></i>Configurar preguntas
                            </a>
                        </div>
                    @else
                        <p class="text-muted">El cuestionario aún no está disponible. Contacte al administrador.</p>
                        <a class="btn btn-primary" href="javascript:history.back()">
                            <i class="bi bi-arrow-left me-2"></i>Volver
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
$(document).ready(function() {
    // Mostrar overlay de carga brevemente
    $('#loading-overlay').removeClass('d-none');
    setTimeout(() => {
        $('#loading-overlay').addClass('d-none');
    }, 1000);
    
    // Prevenir envío de formularios con Enter
    $("form").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
    });
    
    // Actualizar contador de respuestas (solo desktop)
    @desktop
    setInterval(function() {
        const respondidas = $('.pregunta-container.respondida').length;
        $('#contador-respondidas').text(respondidas);
    }, 2000);
    @enddesktop
    
    // Manejo de errores global
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('Error AJAX:', {
            url: settings.url,
            status: xhr.status,
            error: thrownError
        });
        
        if (xhr.status === 419) {
            Swal.fire({
                title: 'Sesión expirada',
                text: 'Su sesión ha expirado. Necesita volver a iniciar sesión.',
                icon: 'warning',
                confirmButtonText: 'Recargar página'
            }).then(() => {
                location.reload();
            });
        }
    });
});

function toggleCuestionario() {
    $('#frm_cuestionario').slideToggle('slow');
}

function confirmarCuestionario(FRM_id) {
    Swal.fire({
        title: '¿Confirmar cuestionario?',
        text: 'Una vez confirmado, el cuestionario se marcará como completado.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            enviarConfirmacion(FRM_id);
        }
    });
}

function enviarConfirmacion(FRM_id) {
    $.ajax({
        async: true,
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        url: '/cuestionario/confirmaCuestionario',
        type: 'POST',
        data: {estado: 'completado', FRM_id: FRM_id},
        beforeSend: function() {
            Swal.fire({
                title: 'Confirmando...',
                text: 'Procesando su cuestionario',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(data) {
            Swal.fire({
                title: '¡Cuestionario completado!',
                text: data.message || 'Su cuestionario ha sido guardado exitosamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.back();
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error al confirmar:', {xhr, status, error});
            
            Swal.fire({
                title: 'Error al confirmar',
                text: 'Hubo un problema al procesar su cuestionario. Por favor, inténtelo nuevamente.',
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

// Funciones de utilidad para debugging
@if(config('app.debug'))
function debugInfo() {
    console.log('=== DEBUG INFO ===');
    console.log('Elementos totales:', {{ count($elementos) }});
    console.log('Preguntas normales:', {{ collect($elementos)->whereNotIn('BCP_tipoRespuesta', ['Sección', 'Subsección', 'Seccion', 'Subseccion', 'Etiqueta'])->count() }});
    console.log('Device mode:', window.innerWidth >= 768 ? 'Desktop' : 'Mobile');
    console.log('AGF_id:', '{{ $AGF_id ?? 'N/A' }}');
    console.log('FRM_id:', '{{ $FRM_id ?? 'N/A' }}');
    console.log('VIS_id:', '{{ $VIS_id ?? 'N/A' }}');
}

// Hacer función disponible globalmente para debugging
window.debugInfo = debugInfo;
@endif
</script>

@endsection