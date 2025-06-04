@extends('layouts.app')
@section('title', 'Visitas')

@section('content')
<style>
/* Corrección específica para las flechas de accordion */
.accordion-button {
    padding-right: 3.5rem !important; /* Asegurar espacio para la flecha */
    overflow: hidden;
    text-overflow: ellipsis;
}

.accordion-button::after {
    margin-left: auto !important;
    flex-shrink: 0 !important;
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

.accordion-button:not(.collapsed)::after {
    transform: translateY(-50%) rotate(180deg);
}

/* Asegurar que el contenido no empuje la flecha */
.accordion-header .container-fluid,
.accordion-header .row {
    max-width: calc(100% - 4rem); /* Dejar espacio para la flecha */
}
</style>

<div class="container">
    <!-- Select box para Filtrar por el año -->
    <div class="row m-4 p-3" style="background-color: #cfe2ff;">
        <form action="{{ route('visita.resumen') }}" method="GET" class="mb-3">
            <label for="anio_actual" class="col-sm-8 col-form-label col-form-label-lg">Filtrar por año:</label>
            <select name="anio_actual" id="anio_actual" class="form-select form-select-lg" onchange="this.form.submit()">
                <option value="">Seleccionar año</option>
                <option value="2024" {{ $anioActual == '2024' ? 'selected' : '' }}>2024</option>
                <option value="2025" {{ $anioActual == '2025' ? 'selected' : '' }}>2025</option>
                <option value="2026" {{ $anioActual == '2026' ? 'selected' : '' }}>2026</option>
                <option value="2027" {{ $anioActual == '2027' ? 'selected' : '' }}>2027</option>
                <option value="2028" {{ $anioActual == '2028' ? 'selected' : '' }}>2028</option>
            </select>
        </form>
    </div>

    @if ($totalVisitasProcessed["total_general"] == 0)
        <div class="alert alert-warning text-center fs-5" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>
            Aún no se registraron visitas para el año {{ $anioActual }}
        </div>
    @else
        <!-- Mostrar total general de visitas -->
        <div class="text-center mb-4">
            <div class="alert alert-success d-inline-block" role="alert">
                <img src="/img/TotalVisitas.png" class="img-fluid px-2" style="max-width: 60px;" alt="Total de visitas">
                <span class="fs-4">Total de visitas para el año {{ $anioActual }}: </span><strong class="fs-3">{{ $totalVisitasProcessed['total_general'] }}</strong>
            </div>
        </div>
        
        <div class="accordion mt-3" id="accordionTipoEstablecimientos">
            @php $tipoE = 0; @endphp
            @foreach ($totalVisitasProcessed['resultado'] as $tipoEstablecimiento => $tipos)
            <div class="accordion-item mt-2 box-shadow">
                <h2 class="accordion-header" id="heading_tipo_{{ $tipoE }}">
                    <button class="accordion-button bg-secondary text-white p-3" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapse_tipo_{{ $tipoE }}" 
                            aria-expanded="true" 
                            aria-controls="collapse_tipo_{{ $tipoE }}">
                        <div class="d-flex align-items-center justify-content-between w-100 me-3">
                            <div class="d-flex align-items-center">
                                <img src="/img/LD.png" class="img-fluid px-2" style="max-width: 60px;" alt="{{ $tipoEstablecimiento }}">
                                <span class="fw-bold fs-5 ms-2">{{ $tipoEstablecimiento }}</span>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-dark fs-6 p-2">
                                    <small class="text-muted">Total visitas:</small> 
                                    <strong>{{ $tipos['total_tipo_establecimiento'] }}</strong>
                                </span>
                            </div>
                        </div>
                    </button>
                </h2>

                <div id="collapse_tipo_{{ $tipoE }}" 
                     class="accordion-collapse collapse" 
                     aria-labelledby="heading_tipo_{{ $tipoE }}" 
                     data-bs-parent="#accordionTipoEstablecimientos">
                    <div class="accordion-body">
                        <div class="accordion" id="accordionEstablecimientos_{{ $tipoE }}">
                            @php $e = 0; @endphp
                            @foreach ($tipos['establecimientos'] as $nombreEstablecimiento => $establecimiento)
                            <div class="accordion-item mt-1">
                                <h2 class="accordion-header" id="heading_est_{{ $tipoE }}_{{ $e }}">
                                    <button class="accordion-button collapsed p-2" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse_est_{{ $tipoE }}_{{ $e }}" 
                                            aria-expanded="false" 
                                            aria-controls="collapse_est_{{ $tipoE }}_{{ $e }}">
                                        <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                            <div>
                                                <span class="fw-bold fs-6">{{ $nombreEstablecimiento }}</span>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary text-white p-2">
                                                    <small>Visitas:</small> 
                                                    <strong>{{ $establecimiento['total_establecimiento'] }}</strong>
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                
                                <div id="collapse_est_{{ $tipoE }}_{{ $e }}" 
                                     class="accordion-collapse collapse" 
                                     aria-labelledby="heading_est_{{ $tipoE }}_{{ $e }}" 
                                     data-bs-parent="#accordionEstablecimientos_{{ $tipoE }}">
                                    <div class="accordion-body bg-light">
                                        <h6 class="text-muted mb-3">
                                            <i class="bi bi-calendar3 me-2"></i>Visitas realizadas en {{ $anioActual }}:
                                        </h6>
                                        
                                        @if(!empty($establecimiento['visitas']))
                                            <div class="list-group">
                                                @foreach ($establecimiento['visitas'] as $visita)
                                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div class="col-8">
                                                        <a href="{{ route('visita.historial', $establecimiento['EST_id']) }}" 
                                                           target="_blank" 
                                                           class="text-decoration-none">
                                                            <i class="bi bi-eye me-2"></i>
                                                            <strong>{{ $visita['VIS_tipo'] }}</strong>
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar-event me-1"></i>{{ $visita['VIS_fechas'] }}
                                                        </small>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <span class="badge bg-success text-white">
                                                            {{ $visita['total_tipo_visitas'] }} 
                                                            {{ $visita['total_tipo_visitas'] == 1 ? 'visita' : 'visitas' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-info text-center">
                                                <i class="bi bi-info-circle me-2"></i>
                                                No hay visitas registradas para este establecimiento en {{ $anioActual }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @php $e++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @php $tipoE++; @endphp
            @endforeach
        </div>
    @endif
</div>

@endsection