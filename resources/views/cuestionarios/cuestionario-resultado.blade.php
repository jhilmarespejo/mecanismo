@extends('layouts.app')
@section('title', 'Resultados del Cuestionario')

@section('content')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .chart-container {
        min-height: 400px;
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .category-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }
    .progress-custom {
        height: 8px;
        border-radius: 10px;
    }
    .table-response {
        font-size: 0.95rem;
    }
    .badge-response {
        font-size: 0.85em;
        padding: 0.5em 0.8em;
    }
</style>

<div class="container-fluid py-4">
    <!-- =========================== HEADER PRINCIPAL =========================== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px;">
                    <h1 class="display-6 mb-2">
                        <i class="bi bi-bar-chart-line me-3"></i>Resultados del Cuestionario
                    </h1>
                    <h2 class="h4 mb-2">{{ session('EST_nombre') }}</h2>
                    <h3 class="h5 mb-0 opacity-90">{{ $FRM_titulo }}</h3>
                    <div class="mt-3">
                        <span class="badge bg-white text-primary fs-6 px-3 py-2">
                            <i class="bi bi-clipboard-check me-2"></i>{{ $totalAplicaciones ?? $estadisticas['total_aplicaciones'] ?? 0 }} Aplicaciones
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================== NAVEGACIÓN =========================== -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="javascript:history.back()" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Atrás
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Resultados</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($resultados == null)
        <!-- =========================== ESTADO VACÍO =========================== -->
        <div class="row">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        <h3 class="mt-3 text-warning">No se aplicó el formulario</h3>
                        <p class="text-muted">No hay datos disponibles para mostrar resultados.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- =========================== ESTADÍSTICAS GENERALES CORREGIDAS =========================== -->
        <div class="row mb-4">
            @php
                // Usar estadísticas calculadas en el controlador
                $totalPreguntasReales = $estadisticas['total_preguntas_reales'] ?? 0;
                $totalAplicaciones = $estadisticas['total_aplicaciones'] ?? 0;
                $aplicacionesCompletas = $estadisticas['aplicaciones_completas'] ?? 0;
                $aplicacionesIncompletas = $estadisticas['aplicaciones_incompletas'] ?? 0;
                $porcentajeCompletitudGeneral = $estadisticas['porcentaje_completitud_general'] ?? 0;
                $porcentajeAplicacionesCompletas = $estadisticas['porcentaje_aplicaciones_completas'] ?? 0;
                $promedioRespuestasPorAplicacion = $estadisticas['promedio_respuestas_por_aplicacion'] ?? 0;
            @endphp

            <!-- Total de Preguntas Reales -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="bi bi-question-circle" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="card-title text-primary">{{ $totalPreguntasReales }}</h3>
                        <p class="card-text text-muted fs-5">Preguntas por formulario</p>
                        {{-- <small class="text-muted">Excluye secciones y títulos</small> --}}
                    </div>
                </div>
            </div>

            <!-- Total de Aplicaciones -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-info mb-2">
                            <i class="bi bi-clipboard-data" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="card-title text-info">{{ $totalAplicaciones }}</h3>
                        <p class="card-text text-muted fs-5">Formularios aplicados</p>
                    </div>
                </div>
            </div>

            <!-- Aplicaciones Completas -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-success mb-2">
                            <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="card-title text-success">{{ $aplicacionesCompletas }}</h3>
                        <p class="card-text text-muted fs-5">Formularios Completados</p>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-success" style="width: {{ $porcentajeAplicacionesCompletas }}%"></div>
                        </div>
                        <small class="text-muted">{{ $porcentajeAplicacionesCompletas }}% del total</small>
                    </div>
                </div>
            </div>

            <!-- Aplicaciones Incompletas -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card stat-card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="bi bi-exclamation-circle-fill" style="font-size: 2.5rem;"></i>
                        </div>
                        <h3 class="card-title text-warning">{{ $aplicacionesIncompletas }}</h3>
                        <p class="card-text text-muted fs-5">Formularios Incompletos</p>
                        <div class="progress progress-custom">
                            <div class="progress-bar bg-warning" style="width: {{ 100 - $porcentajeAplicacionesCompletas }}%"></div>
                        </div>
                        <small class="text-muted">{{ 100 - $porcentajeAplicacionesCompletas }}% del total</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================== MÉTRICAS ADICIONALES =========================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>Métricas de Rendimiento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <!-- Completitud General -->
                            <div class="col-md-4 mb-3">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">{{ $porcentajeCompletitudGeneral }}%</h4>
                                    <p class="text-muted mb-2">Completitud General</p>
                                    <div class="progress progress-custom mx-auto" style="width: 80%;">
                                        <div class="progress-bar bg-primary" style="width: {{ $porcentajeCompletitudGeneral }}%"></div>
                                    </div>
                                    <small class="text-muted">Respuestas totales vs esperadas</small>
                                </div>
                            </div>

                            <!-- Promedio de Respuestas -->
                            <div class="col-md-4 mb-3">
                                <div class="border-end">
                                    <h4 class="text-success mb-1">{{ $promedioRespuestasPorAplicacion }}</h4>
                                    <p class="text-muted mb-2">Promedio por Aplicación</p>
                                    <div class="progress progress-custom mx-auto" style="width: 80%;">
                                        <div class="progress-bar bg-success" style="width: {{ ($promedioRespuestasPorAplicacion / $totalPreguntasReales) * 100 }}%"></div>
                                    </div>
                                    <small class="text-muted">de {{ $totalPreguntasReales }} preguntas</small>
                                </div>
                            </div>
                            
                            <!-- Eficacia de Aplicación -->
                            <div class="col-md-4 mb-3">
                                <h4 class="text-info mb-1">{{ $porcentajeAplicacionesCompletas }}%</h4>
                                <p class="text-muted mb-2">Eficacia de Aplicación</p>
                                <div class="progress progress-custom mx-auto" style="width: 80%;">
                                    <div class="progress-bar bg-info" style="width: {{ $porcentajeAplicacionesCompletas }}%"></div>
                                </div>
                                <small class="text-muted">Formularios 100% completos</small>
                            </div>
                        </div>

                        <!-- Resumen Textual -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <p class="mb-0 text-center fs-5">
                                <strong>Resumen:</strong> 
                                De {{ $totalAplicaciones }} aplicaciones del formulario, 
                                <span class="text-success">{{ $aplicacionesCompletas }} están completas</span>
                                @if($aplicacionesIncompletas > 0)
                                    y <span class="text-warning">{{ $aplicacionesIncompletas }} están incompletas</span>
                                @endif
                                {{-- . El formulario tiene {{ $totalPreguntasReales }} preguntas que requieren respuesta. --}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================== NAVEGACIÓN POR CATEGORÍAS =========================== -->
        {{-- <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Navegación Rápida por Categorías
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($resultados as $nombreCategoria => $preguntas)
                                <a href="#categoria-{{ Str::slug($nombreCategoria) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-folder me-1"></i>{{ $nombreCategoria }}
                                    <span class="badge bg-primary ms-1">{{ count($preguntas) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- =========================== RESULTADOS POR CATEGORÍA =========================== -->
        {{-- @dump($resultados) --}}
        @php $contadorCategoria = 1; @endphp
        @foreach ($resultados as $nombreCategoria => $preguntasCategoria)
            <div class="row mb-5" id="categoria-{{ Str::slug($nombreCategoria) }}">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <!-- Header de Categoría -->
                        {{-- <div class="category-header">
                            <h4 class="mb-0">
                                <i class="bi bi-folder-open me-2"></i>
                                {{ $contadorCategoria }}. {{ $nombreCategoria }}
                            </h4>
                            <small class="opacity-90">{{ count($preguntasCategoria) }} preguntas en esta categoría</small>
                        </div> --}}

                        <div class="card-body p-0">
                            @php $contadorPregunta = 1; @endphp
                            @foreach ($preguntasCategoria as $pregunta)
                                <div class="border-bottom p-4">
                                    <!-- Título de Pregunta -->
                                    <div class="mb-3">
                                        <h5 class="text-primary mb-1">
                                            {{-- <span class="badge bg-primary me-2">{{ $contadorCategoria }}.{{ $contadorPregunta }}</span> --}}
                                            {{ $pregunta['BCP_pregunta'] }}
                                        </h5>
                                        {{-- <div class="d-flex gap-2 mb-2">
                                            <span class="badge badge-response bg-secondary">
                                                <i class="bi bi-tag me-1"></i>{{ $pregunta['BCP_tipoRespuesta'] }}
                                            </span>
                                            @if(isset($pregunta['respuestas']) && !empty($pregunta['respuestas']))
                                                <span class="badge badge-response bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Con respuestas
                                                </span>
                                            @else
                                                <span class="badge badge-response bg-warning">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Sin respuestas
                                                </span>
                                            @endif
                                        </div> --}}
                                    </div>

                                    <!-- Contenido según tipo de pregunta -->
                                    @if(in_array($pregunta['BCP_tipoRespuesta'], ['Casilla verificación', 'Lista desplegable', 'Afirmación']))
                                        <!-- PREGUNTAS CERRADAS - Tabla + Gráfico -->
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="table-responsive">
                                                    <table class="table table-response table-hover mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Opción</th>
                                                                <th class="text-end">Cantidad</th>
                                                                <th class="text-end">Porcentaje</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php 
                                                                $respuestaParcial = 0;
                                                                $datosGrafico = [];
                                                            @endphp
                                                            @if(isset($pregunta['respuestas']))
                                                                @foreach ($pregunta['respuestas'] as $opcion => $cantidad)
                                                                    @php 
                                                                        $respuestaParcial += $cantidad;
                                                                        $totalActual = $totalAplicaciones ?? $estadisticas['total_aplicaciones'] ?? 1;
                                                                        $porcentaje = $totalActual > 0 ? round(($cantidad / $totalActual) * 100, 1) : 0;
                                                                        $datosGrafico[] = ['name' => $opcion, 'y' => (float)$porcentaje];
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $opcion }}</td>
                                                                        <td class="text-end">
                                                                            <span class="badge bg-primary">{{ $cantidad }}</span>
                                                                        </td>
                                                                        <td class="text-end">
                                                                            <div class="d-flex align-items-center justify-content-end">
                                                                                <div class="progress me-2" style="width: 60px; height: 20px;">
                                                                                    <div class="progress-bar" style="width: {{ $porcentaje }}%"></div>
                                                                                </div>
                                                                                <span class="fw-bold">{{ $porcentaje }}%</span>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif

                                                            @if ($respuestaParcial < $totalActual)
                                                                @php 
                                                                    $sinRespuesta = $totalActual - $respuestaParcial;
                                                                    $porcentajeSinRespuesta = round(($sinRespuesta / $totalActual) * 100, 1);
                                                                    $datosGrafico[] = ['name' => 'Sin respuesta', 'y' => (float)$porcentajeSinRespuesta];
                                                                @endphp
                                                                <tr class="table-warning">
                                                                    <td><em>Sin respuesta</em></td>
                                                                    <td class="text-end">
                                                                        <span class="badge bg-warning text-dark">{{ $sinRespuesta }}</span>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <div class="d-flex align-items-center justify-content-end">
                                                                            <div class="progress me-2" style="width: 60px; height: 20px;">
                                                                                <div class="progress-bar bg-warning" style="width: {{ $porcentajeSinRespuesta }}%"></div>
                                                                            </div>
                                                                            <span class="fw-bold">{{ $porcentajeSinRespuesta }}%</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot class="table-info">
                                                            <tr>
                                                                <th>Total</th>
                                                                <th class="text-end">{{ $total }}</th>
                                                                <th class="text-end">100%</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="chart-container">
                                                    <div id="chart-{{ $contadorCategoria }}-{{ $contadorPregunta }}" style="width: 100%; height: 300px;"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Script para generar gráfico -->
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                Highcharts.chart('chart-{{ $contadorCategoria }}-{{ $contadorPregunta }}', {
                                                    chart: { type: 'pie' },
                                                    title: { text: null },
                                                    tooltip: {
                                                        pointFormat: '<b>{point.percentage:.1f}%</b><br/>Cantidad: {point.custom_count}'
                                                    },
                                                    plotOptions: {
                                                        pie: {
                                                            allowPointSelect: true,
                                                            cursor: 'pointer',
                                                            dataLabels: {
                                                                enabled: true,
                                                                format: '<b>{point.name}</b>: {point.percentage:.1f}%'
                                                            },
                                                            showInLegend: true
                                                        }
                                                    },
                                                    series: [{
                                                        name: 'Respuestas',
                                                        colorByPoint: true,
                                                        data: @json($datosGrafico).map(function(item, index) {
                                                            const colors = ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80'];
                                                            return {
                                                                name: item.name,
                                                                y: item.y,
                                                                color: colors[index % colors.length],
                                                                custom_count: Math.round((item.y * {{ $total }}) / 100)
                                                            };
                                                        })
                                                    }],
                                                    credits: { enabled: false }
                                                });
                                            });
                                        </script>

                                    @elseif(in_array($pregunta['BCP_tipoRespuesta'], ['Numeral', 'Respuesta corta', 'Respuesta larga']))
                                        <!-- PREGUNTAS ABIERTAS - Acordeón -->
                                        <div class="accordion" id="accordion-{{ $contadorCategoria }}-{{ $contadorPregunta }}">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed" type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#collapse-{{ $contadorCategoria }}-{{ $contadorPregunta }}"
                                                            aria-expanded="false">
                                                        <i class="bi bi-chat-dots me-2"></i>
                                                        Ver {{ isset($pregunta['respuestas']) ? count($pregunta['respuestas']) : 0 }} respuestas
                                                    </button>
                                                </h2>
                                                <div id="collapse-{{ $contadorCategoria }}-{{ $contadorPregunta }}" 
                                                     class="accordion-collapse collapse" 
                                                     data-bs-parent="#accordion-{{ $contadorCategoria }}-{{ $contadorPregunta }}">
                                                    <div class="accordion-body">
                                                        @if(isset($pregunta['respuestas']) && !empty($pregunta['respuestas']))
                                                            <div class="list-group list-group-flush">
                                                                @foreach ($pregunta['respuestas'] as $index => $respuestaAbierta)
                                                                    <div class="list-group-item border-0 px-0">
                                                                        @if(empty($respuestaAbierta['respuesta']))
                                                                            <em class="text-muted">Sin respuesta</em>
                                                                        @else
                                                                            <div class="d-flex justify-content-between align-items-start">
                                                                                <div class="flex-grow-1">
                                                                                    <small class="text-muted">Respuesta {{ $index + 1 }}</small>
                                                                                    <p class="mb-1">{{ $respuestaAbierta['respuesta'] }}</p>
                                                                                </div>
                                                                                <a href="/cuestionario/responder/{{ $VIS_id ?? '' }}/{{ $FRM_id }}/{{ $respuestaAbierta['FK_AGF_id'] ?? '' }}" 
                                                                                   target="_blank" 
                                                                                   class="btn btn-sm btn-outline-primary">
                                                                                    <i class="bi bi-eye"></i>
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="text-center text-muted py-3">
                                                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                                <p class="mt-2">No hay respuestas disponibles</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @php $contadorPregunta++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @php $contadorCategoria++; @endphp
        @endforeach

        <!-- =========================== BOTÓN VOLVER AL INICIO =========================== -->
        <div class="row">
            <div class="col-12 text-center">
                <a href="#" class="btn btn-primary btn-lg" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                    <i class="bi bi-arrow-up me-2"></i>Volver al inicio
                </a>
            </div>
        </div>
    @endif
</div>

<!-- =========================== SCRIPTS =========================== -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/draggable-points.js"></script>

<script>
// Configuración global de Highcharts
Highcharts.setOptions({
    colors: ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c', '#8085e9', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1'],
    chart: {
        backgroundColor: '#ffffff',
        style: {
            fontFamily: '"Segoe UI", Tahoma, Geneva, Verdana, sans-serif'
        }
    }
});

// Smooth scroll para navegación
document.querySelectorAll('a[href^="#categoria-"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>

@endsection