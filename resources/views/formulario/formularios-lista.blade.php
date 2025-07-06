@extends('layouts.app')
@section('title', 'Formularios')

@section('content')
@php
    use Carbon\Carbon;
    // Recuperar las variables de sesión
    $TES_tipo = session('TES_tipo');
    $EST_nombre = session('EST_nombre');
@endphp

<style>
    .formulario-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .formulario-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .formulario-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        text-align: center;
        margin-bottom: 15px;
    }

    .aplicacion-item {
        transition: all 0.2s ease;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        margin-bottom: 10px;
        background: #fff;
    }

    .aplicacion-item:hover {
        border-color: #007bff;
        background: #f8f9fa;
    }

    .aplicacion-completada {
        border-color: #28a745;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    }

    .aplicacion-pendiente {
        border-color: #ffc107;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    }

    /* .stats-badge {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        font-weight: 600;
    } */

    .action-btn {
        transition: all 0.2s ease;
        border-radius: 8px;
        padding: 8px 12px;
        border: none;
        font-weight: 500;
    }

    .action-btn:hover {
        transform: scale(1.05);
    }

    .btn-responder {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-eliminar {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        color: white;
    }

    .progress-ring {
        width: 60px;
        height: 60px;
        transform: rotate(-90deg);
    }

    .progress-ring-circle {
        stroke: #e9ecef;
        stroke-width: 4;
        fill: transparent;
        r: 26;
        cx: 30;
        cy: 30;
    }

    .progress-ring-progress {
        stroke: #28a745;
        stroke-width: 4;
        fill: transparent;
        r: 26;
        cx: 30;
        cy: 30;
        stroke-dasharray: 163.36;
        stroke-dashoffset: 163.36;
        transition: stroke-dashoffset 0.5s ease;
    }

    .header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .floating-action {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    @media (max-width: 768px) {
        .aplicacion-item {
            margin-bottom: 15px;
        }
        
        .floating-action {
            bottom: 10px;
            right: 10px;
        }
    }
</style>

{{-- SUB MENU --}}
<div class="btn-toolbar mb-4" role="toolbar">
    <div class="btn-group" role="group">
        <a href="javascript:history.back()" class="btn btn-outline-primary">
            <i class="bi bi-arrow-return-left me-2"></i>Página anterior
        </a>
    </div>
</div>

{{-- ENCABEZADO PRINCIPAL --}}
<div class="header-gradient">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h3 class="mb-2">
                <i class="bi bi-clipboard-data me-2"></i>{{ $VIS_tipo }}
            </h3>
            <h4 class="mb-0">{{ $TES_tipo }} - {{ $EST_nombre }}</h4>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-light text-dark fs-6">
                <i class="bi bi-calendar-event me-1"></i>
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </div>
</div>

{{-- CONTENIDO PRINCIPAL --}}
<div class="row">
    @if(empty($grupo_formularios))
        {{-- ESTADO VACÍO --}}
        <div class="col-12">
            <div class="card border-warning formulario-card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-clipboard-x text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-warning mb-3">No hay formularios asignados</h4>
                    <p class="text-muted mb-4">
                        Aún no se han asignado formularios para esta visita.
                    </p>
                    
                    @if(Auth::user()->rol == 'Administrador')
                        <a href="/formulario/eleccion/{{ $VIS_id }}/{{ $VIS_tipo }}" 
                           class="btn btn-lg action-btn btn-responder">
                            <i class="bi bi-plus-circle me-2"></i>Asignar Formulario
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- LISTA DE FORMULARIOS --}}
        @php $iconIndex = 0; @endphp
        @foreach($grupo_formularios as $tituloFormulario => $aplicaciones)
            <div class="col-12 mb-4">
                <div class="card formulario-card">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0 text-primary">
                                    <i class="bi bi-file-earmark-text me-2"></i>{{ $tituloFormulario }}
                                </h5>
                            </div>
                            <div class="col-auto">
                                @if(Auth::user()->rol == 'Administrador')
                                    <a href="/cuestionario/resultados/{{ $aplicaciones[0]['FRM_id'] }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-bar-chart-line me-1"></i>Resultados
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            {{-- COLUMNA IZQUIERDA: INFORMACIÓN DEL FORMULARIO --}}
                            <div class="col-lg-4 col-12 mb-3">
                                <div class="formulario-icon">
                                    <div class="mb-3">
                                        <img src="/img/{{ $iconIndex % 5 }}.png" 
                                             class="img-fluid" 
                                             style="max-height: 80px; filter: brightness(0) invert(1);" 
                                             alt="Icono formulario">
                                    </div>
                                    <h6 class="mb-2">{{ $tituloFormulario }}</h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="fw-bold fs-4">{{ $aplicaciones[0]['preguntas'] ?? 0 }}</div>
                                            <small>Preguntas</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-bold fs-4">{{ count($aplicaciones) }}</div>
                                            <small>Aplicaciones</small>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- BOTÓN NUEVO FORMULARIO --}}
                                <div class="text-center">
                                    @php
                                        $puedeAplicar = $aplicaciones[0]['FRM_tipo'] == 'N' || 
                                                       ($aplicaciones[0]['FRM_tipo'] == '1' && count($aplicaciones) == 0);
                                    @endphp
                                    
                                    @if($puedeAplicar)
                                        <a href="/cuestionario/duplicarCuestionario/{{ $aplicaciones[0]['FRM_id'] }}/{{ $VIS_id }}" 
                                           class="btn btn-lg action-btn btn-responder w-100">
                                            <i class="bi bi-plus-circle me-2"></i>Aplicar Nuevo
                                        </a>
                                    @else
                                        <div class="alert alert-info small mb-0">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Este formulario solo se puede aplicar una vez
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- COLUMNA DERECHA: LISTA DE APLICACIONES DE FORMULARIOS --}}
                            <div class="col-lg-8 col-12">
                                {{-- MENSAJE DE ALERTA PARA FORMULARIOS ÚNICOS --}}
                                @if($aplicaciones[0]["FRM_tipo"] == '1' && session('warning'))
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        {{ session('warning') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                
                                {{-- LISTA DE APLICACIONES --}}
                                <div class="aplicaciones-container">
                                    @foreach($aplicaciones as $aplicacion)
                                        @php
                                            $porcentajeCompletitud = $aplicacion['preguntas'] > 0 
                                                ? round(($aplicacion['respuestas'] / $aplicacion['preguntas']) * 100) 
                                                : 0;
                                            
                                            $claseEstado = match(true) {
                                                $porcentajeCompletitud >= 100 => 'aplicacion-completada',
                                                $porcentajeCompletitud >= 50 => 'aplicacion-pendiente',
                                                default => ''
                                            };
                                        @endphp
                                        <div class="aplicacion-item {{ $claseEstado }} p-3">
                                            <div class="row align-items-center">
                                                {{-- ÍCONO Y PROGRESO --}}
                                                <div class="col-auto">
                                                    <div class="position-relative">
                                                        <svg class="progress-ring">
                                                            <circle class="progress-ring-circle"></circle>
                                                            <circle class="progress-ring-progress" 
                                                                    style="stroke-dashoffset: {{ 163.36 - (163.36 * $porcentajeCompletitud / 100) }};">
                                                            </circle>
                                                        </svg>
                                                        <div class="position-absolute top-50 start-50 translate-middle">
                                                            <i class="bi bi-file-earmark-ruled fs-4 text-primary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- INFORMACIÓN DE LA APLICACIÓN --}}
                                                <div class="col">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <h6 class="mb-1">
                                                                {{ mb_strimwidth($aplicacion["FRM_titulo"], 0, 40, '...', 'UTF-8') }}
                                                            </h6>
                                                            <p class="text-muted small mb-2">
                                                                <i class="bi bi-calendar me-1"></i>
                                                                Creado: {{ Carbon::parse($aplicacion["createdAt"])->translatedFormat('d M. Y H:i') }}
                                                                <span class="ms-2">
                                                                    <i class="bi bi-hash me-1"></i>{{ $aplicacion["AGF_id"] }}
                                                                </span>
                                                            </p>
                                                            <div class="d-flex gap-2 flex-wrap">
                                                                <span class="badge bg-info text-shadow">
                                                                    <i class="bi bi-question-circle me-1"></i>
                                                                    {{ $aplicacion["preguntas"] }} preguntas
                                                                </span>
                                                                <span class="badge bg-primary text-shadow">
                                                                    <i class="bi bi-check-circle me-1"></i>
                                                                    {{ $aplicacion["respuestas"] }} respondidas
                                                                </span>
                                                                <span class="badge text-shadow {{ $porcentajeCompletitud >= 100 ? 'bg-success' : ($porcentajeCompletitud >= 50 ? 'bg-warning' : 'bg-secondary') }}">
                                                                    {{ $porcentajeCompletitud }}% completo
                                                                </span>
                                                            </div>
                                                        </div>
                                                        
                                                        {{-- ACCIONES --}}
                                                        <div class="col-md-4 text-md-end">
                                                            <div class="btn-group-vertical w-100" role="group">
                                                                <a href="/cuestionario/responder/{{ $VIS_id }}/{{ $aplicacion["FRM_id"] }}/{{ $aplicacion["AGF_id"] }}" 
                                                                   class="btn action-btn btn-responder">
                                                                    <i class="bi bi-pencil-square me-2"></i>
                                                                    {{ $porcentajeCompletitud >= 100 ? 'Revisar' : 'Responder' }}
                                                                </a>
                                                                
                                                                @if($aplicacion["estado"] != 1 && Auth::user()->rol == 'Administrador')
                                                                    <form action="{{ route('cuestionario.eliminar') }}" 
                                                                          method="POST" 
                                                                          class="frm-eliminar-cuestionario mt-2"
                                                                          onsubmit="return confirmarEliminacion(event)">
                                                                        @csrf
                                                                        <input type="hidden" name="AGF_id" value="{{ $aplicacion["AGF_id"] }}">
                                                                        <button type="submit" class="btn action-btn btn-eliminar w-100">
                                                                            <i class="bi bi-trash me-2"></i>Eliminar
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
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
            </div>
            @php $iconIndex++; @endphp
        @endforeach
    @endif
</div>

{{-- BOTÓN FLOTANTE PARA AGREGAR FORMULARIOS (Solo Administradores) --}}
@if(Auth::user()->rol == 'Administrador')
    <div class="floating-action-discrete">
        <a href="/formulario/eleccion/{{ $VIS_id }}/{{ $VIS_tipo }}" 
           class="btn btn-lg btn-success box-shadow w-50 text-shadow"
           data-bs-toggle="tooltip" 
           data-bs-placement="left" 
           title="Adicionar formulario a esta visita">
            <i class="bi bi-plus-circle me-2"></i>
            <span class="btn-text">Agregar Formulario</span>
        </a>
    </div>
@endif

{{-- MODAL DE ESTADÍSTICAS (Opcional) --}}
<div class="modal fade" id="estadisticasModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-graph-up me-2"></i>Estadísticas del Formulario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="estadisticas-content">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Función para confirmar eliminación
function confirmarEliminacion(event) {
    event.preventDefault();
    
    Swal.fire({
        title: '¿Eliminar cuestionario?',
        text: 'Esta acción no se puede deshacer. Se perderán todas las respuestas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.submit();
        }
    });
    
    return false;
}

// Auto-dismiss de alertas de warning
setTimeout(function() {
    let alertElement = document.querySelector('.alert-warning');
    if (alertElement) {
        let fadeEffect = setInterval(function() {
            if (!alertElement.style.opacity) {
                alertElement.style.opacity = 1;
            }
            if (alertElement.style.opacity > 0) {
                alertElement.style.opacity -= 0.1;
            } else {
                clearInterval(fadeEffect);
                alertElement.style.display = 'none';
            }
        }, 50);
    }
}, 5000);

// Función para cargar estadísticas (opcional)
function cargarEstadisticas(formularioId) {
    $('#estadisticasModal').modal('show');
    
    fetch(`/api/formularios/${formularioId}/estadisticas`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('estadisticas-content').innerHTML = `
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h3 class="text-primary">${data.total_aplicaciones || 0}</h3>
                        <p class="text-muted">Total Aplicaciones</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3 class="text-success">${data.completados || 0}</h3>
                        <p class="text-muted">Completados</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3 class="text-warning">${data.en_progreso || 0}</h3>
                        <p class="text-muted">En Progreso</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3 class="text-info">${Math.round(data.porcentaje_promedio || 0)}%</h3>
                        <p class="text-muted">Promedio Completitud</p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('estadisticas-content').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al cargar las estadísticas
                </div>
            `;
        });
}

// Efecto de hover mejorado para aplicaciones
document.querySelectorAll('.aplicacion-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(5px)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'translateX(0)';
    });
});

// Contador animado para números
function animateNumbers() {
    document.querySelectorAll('.fw-bold.fs-4').forEach(element => {
        const finalNumber = parseInt(element.textContent);
        let currentNumber = 0;
        const increment = Math.ceil(finalNumber / 20);
        
        const timer = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                currentNumber = finalNumber;
                clearInterval(timer);
            }
            element.textContent = currentNumber;
        }, 50);
    });
}

// Ejecutar animación cuando la página esté lista
document.addEventListener('DOMContentLoaded', animateNumbers);
</script>
@endsection