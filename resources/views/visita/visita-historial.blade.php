@extends('layouts.app')
@section('title', 'Ficha del Establecimiento')

@section('content')
<style>
    .d-xss-none {
        display: none;
    }
    .bg-red{
        background-color: #fd4d36 !important;
    }
    @media screen and (max-width: 320px) {
        .d-xss-none {
            display: unset;
        }
    }
    .ficha-body {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0 0 10px 10px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .info-row {
        margin-bottom: 10px;
        border-bottom: 1px dotted #dee2e6;
        padding-bottom: 10px;
    }
    .info-label {
        font-weight: bold;
        color: #4b6cb7;
    }
    .documento-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .documento-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .documento-icon {
        font-size: 2rem;
        color: #4b6cb7;
    }
    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0c63e4;
    }
    .timeline-item {
        position: relative;
        padding-left: 20px;
        margin-bottom: 15px;
    }
    .timeline-item:before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #4b6cb7;
    }
    .timeline-item:after {
        content: "";
        position: absolute;
        left: -4px;
        top: 20px;
        height: 10px;
        width: 10px;
        border-radius: 50%;
        background-color: #4b6cb7;
    }

    /* Estilos para el modal de edición */
    .section-header {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin: 20px 0 15px 0;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-size: 1.1em;
    }

    .section-header:first-of-type {
        margin-top: 0;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 6px;
        font-size: 0.9em;
    }

    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 8px 12px;
        font-size: 0.9em;
    }

    .form-control:focus {
        border-color: #4b6cb7;
        box-shadow: 0 0 0 0.2rem rgba(75, 108, 183, 0.25);
    }

    .readonly-info {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        padding: 10px 12px;
        border-radius: 6px;
        color: #495057;
        font-weight: 500;
        font-size: 0.9em;
    }

    .required {
        color: #dc3545;
    }

    /* Estilo para errores de validación */
    .is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.8em;
        color: #dc3545;
    }
</style>

<div class="container mt-3 p-0 bg-white">
    <!-- Estructura de acordeones -->
    <div class="accordion" id="accordionEstablecimiento">
        <!-- Primer acordeón: Ficha del Establecimiento -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFicha">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapseFicha" aria-expanded="true" aria-controls="collapseFicha">
                    <h3 class="mb-0">FICHA DEL ESTABLECIMIENTO</h3>
                </button>
            </h2>
            {{-- @dump($establecimiento) --}}
            <div id="collapseFicha" class="accordion-collapse collapse show" 
                 aria-labelledby="headingFicha" data-bs-parent="#accordionEstablecimiento">
                <div class="accordion-body p-0">
                    <div class="ficha-body">
                        @if(isset($establecimiento))
                            
                            <div class="row">
                                <!-- Columna de información principal -->
                                <div class="col-md-7">
                                    <div class="info-row">
                                        <span class="info-label">NOMBRE DEL CENTRO:</span>
                                        <h3 class="d-inline">{{ $establecimiento->EST_nombre ?? 'No registrado' }}</h3>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">INSTITUCIÓN:</span>
                                        <span>{{ $establecimiento->TES_tipo ?? 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">POBLACIÓN A LA QUE ATIENDE:</span>
                                        <span>{{ isset($establecimiento->EINF_poblacion_atendida) ? $establecimiento->EINF_poblacion_atendida : 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">CANTIDAD POBLACIÓN ACTUAL:</span>
                                        <span>{{ isset($establecimiento->EINF_cantidad_actual_internos) ? $establecimiento->EINF_cantidad_actual_internos : 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">DIRECCIÓN DEL CENTRO:</span>
                                        <address>
                                            Departamento: {{ $establecimiento->EST_departamento ?? 'No registrado' }}<br>
                                            Municipio: {{ $establecimiento->EST_municipio ?? 'No registrado' }}<br>
                                            Dirección: {{ $establecimiento->EST_direccion ?? 'No registrado' }}
                                        </address>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">TELÉFONOS DE CONTACTO:</span>
                                        <span>{{ $establecimiento->EST_telefono_contacto ?? 'No registrado' }}</span>
                                    </div>
                                    
                                    @if(isset($responsable))
                                    <div class="info-row">
                                        <span class="info-label">NOMBRE DEL RESPONSABLE DEL CENTRO:</span>
                                        <span>{{ $responsable->EPER_nombre_responsable ?? 'No registrado' }}</span>
                                        @if(isset($responsable->EPER_grado_profesion) && $responsable->EPER_grado_profesion)
                                            <small class="text-muted d-block">{{ $responsable->EPER_grado_profesion }}</small>
                                        @endif
                                        @if((isset($responsable->EPER_telefono) && $responsable->EPER_telefono) || (isset($responsable->EPER_email) && $responsable->EPER_email))
                                            <small class="d-block">
                                                @if(isset($responsable->EPER_telefono) && $responsable->EPER_telefono)
                                                    <i class="bi bi-telephone"></i> {{ $responsable->EPER_telefono }}
                                                @endif
                                                @if(isset($responsable->EPER_email) && $responsable->EPER_email)
                                                    <i class="bi bi-envelope ms-2"></i> {{ $responsable->EPER_email }}
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    <div class="info-row">
                                        <span class="info-label">CAPACIDAD DE ALOJAMIENTO:</span>
                                        <span>{{ $establecimiento->EST_capacidad_creacion ?? 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">SUPERFICIE DEL TERRENO:</span>
                                        <span>{{ (isset($establecimiento->EINF_superficie_terreno) && $establecimiento->EINF_superficie_terreno) ? $establecimiento->EINF_superficie_terreno . ' m²' : 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">SUPERFICIE CONSTRUIDA:</span>
                                        <span>{{ (isset($establecimiento->EINF_superficie_construida) && $establecimiento->EINF_superficie_construida) ? $establecimiento->EINF_superficie_construida . ' m²' : 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">DERECHO PROPIETARIO:</span>
                                        <span>{{ isset($establecimiento->EINF_derecho_propietario) ? $establecimiento->EINF_derecho_propietario : 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">AÑO DE FUNCIONAMIENTO:</span>
                                        <span>{{ $establecimiento->EST_anyo_funcionamiento ?? 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">GESTIÓN:</span>
                                        <span>{{ isset($establecimiento->EINF_gestion) ? $establecimiento->EINF_gestion : date('Y') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Columna de documentos -->
                                <div class="col-md-5">
                                    <h4 class="text-center mb-3">Documentos del establecimiento</h4>
                                    
                                    <!-- Reglamento del centro -->
                                    <div class="documento-card">
                                        <div class="row">
                                            <div class="col-3 text-center">
                                                <i class="bi bi-file-earmark-text documento-icon"></i>
                                            </div>
                                            <div class="col-9">
                                                <h5>Reglamento del centro</h5>
                                                @if(isset($documentos['reglamento']))
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#modalVerReglamento">
                                                        <i class="bi bi-eye"></i> Ver documento
                                                    </button>
                                                    <a href="{{ asset($documentos['reglamento']->ARC_ruta) }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-download"></i> Descargar
                                                    </a>
                                                @else
                                                    <p class="text-muted">No disponible</p>
                                                    @if(Auth::user()->rol == 'Administrador')
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#modalSubirDocumento" 
                                                                onclick="prepararModal('reglamento')">  
                                                            <i class="bi bi-upload"></i> Subir documento
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Fotografía de la fachada -->
                                    <div class="documento-card">
                                        <div class="row">
                                            <div class="col-3 text-center">
                                                <i class="bi bi-image documento-icon"></i>
                                            </div>
                                            <div class="col-9">
                                                <h5>Fotografía de la fachada</h5>
                                                @if(isset($documentos['fachada']))
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#modalVerFachada">
                                                        <i class="bi bi-eye"></i> Ampliar imagen
                                                    </button>
                                                    <a href="{{ asset($documentos['fachada']->ARC_ruta) }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-download"></i> Descargar
                                                    </a>
                                                @else
                                                    <p class="text-muted">No disponible</p>
                                                    @if(Auth::user()->rol == 'Administrador')
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#modalSubirDocumento" 
                                                                onclick="prepararModal('fachada')" > 
                                                            <i class="bi bi-upload"></i> Subir imagen
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        @if(isset($documentos['fachada']) && $documentos['fachada'] && file_exists(public_path($documentos['fachada']->ARC_ruta)))
                                            <img src="{{ asset($documentos['fachada']->ARC_ruta) }}" class="img-fluid p-1" alt="Fachada del establecimiento">
                                        @endif
                                    </div>
                                    
                                    <!-- Licencia de funcionamiento -->
                                    <div class="documento-card">
                                        <div class="row">
                                            <div class="col-3 text-center">
                                                <i class="bi bi-file-earmark-check documento-icon"></i>
                                            </div>
                                            <div class="col-9">
                                                <h5>Licencia de funcionamiento</h5>
                                                @if(isset($documentos['licencia']))
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            data-bs-toggle="modal" data-bs-target="#modalVerLicencia">
                                                        <i class="bi bi-eye"></i> Ver documento
                                                    </button>
                                                    <a href="{{ asset($documentos['licencia']->ARC_ruta) }}" download class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-download"></i> Descargar
                                                    </a>
                                                @else
                                                    <p class="text-muted">No disponible</p>
                                                    @if(Auth::user()->rol == 'Administrador')
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#modalSubirDocumento" 
                                                                onclick="prepararModal('licencia')"> 
                                                            <i class="bi bi-upload"></i> Subir documento
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           <div class="d-flex justify-content-center mb-3">
                                @if(Auth::user()->rol == 'Administrador')
                                    <button type="button" class="btn btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditarFichaEstablecimiento"
                                        onclick="cargarDatosModal()">
                                    <i class="bi bi-pencil-square"></i> Modificar ésta información
                                </button>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning ">
                                No se encontraron datos del establecimiento.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Segundo acordeón: Historial de Visitas -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingHistorial">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapseHistorial" aria-expanded="false" aria-controls="collapseHistorial">
                    <h3 class="mb-0">HISTORIAL DE VISITAS</h3>
                </button>
            </h2>
            <div id="collapseHistorial" class="accordion-collapse collapse" 
                 aria-labelledby="headingHistorial" data-bs-parent="#accordionEstablecimiento">
                <div class="accordion-body">
                    {{-- Se incluye una vista para agregar una nueva visita --}}
                    @if(Auth::user()->rol == 'Administrador' && isset($establecimiento))
                        <div class="text-center mb-4">
                            @include('visita.visita-nuevo', ['EST_id' => $establecimiento->EST_id ?? '', 'EST_nombre' => $establecimiento->EST_nombre ?? ''])
                        </div>
                    @endif
                    
                    @if(isset($visitas) && count($visitas) > 0)
                        @foreach($visitas as $key => $visita)
                            @if(isset($visita->VIS_id) && $visita->VIS_id)
                                @php 
                                    $VIS_id = $visita->VIS_id;
                                    // Bloque para definir los colores por tipo de visita
                                    $color = 'text-white bg-secondary'; // Color por defecto
                                    if(isset($visita->VIS_tipo)) {
                                        if($visita->VIS_tipo == 'Visita en profundidad'){
                                            $color = 'text-white bg-success';
                                        } elseif($visita->VIS_tipo == 'Visita Temática') {
                                            $color = 'text-white bg-danger';
                                        } elseif($visita->VIS_tipo == 'Visita de seguimiento'){
                                            $color = 'text-white bg-primary';
                                        } elseif($visita->VIS_tipo == 'Visita reactiva'){
                                            $color = 'text-white bg-info';
                                        } elseif($visita->VIS_tipo == 'Visita Ad hoc'){
                                            $color = 'text-white bg-warning';
                                        }
                                    }
                                @endphp
                                <!-- START timeline item -->
                                <div class="row no-gutters mb-4">
                                    @mobile
                                    <div class="col align-self-center text-end">
                                        <span class="text-shadow text-center alert {{$color}}">
                                            Visita: <span class="fs-4">{{ count($visitas)-$key }}</span>
                                        </span>
                                    </div>
                                    <div class="col-5 align-self-center text-end">
                                        <span class="text-shadow text-center alert {{$color}}">
                                            {{ isset($visita->VIS_fechas) ? \Carbon\Carbon::parse($visita->VIS_fechas)->format('d-m-Y') : 'Sin fecha' }}
                                        </span>
                                    </div>
                                    @endmobile
                                    @desktop
                                    <div class="col align-self-center text-end">
                                        <span class="text-end alert {{$color}}">
                                            {{ isset($visita->VIS_fechas) ? \Carbon\Carbon::parse($visita->VIS_fechas)->format('d-m-Y') : 'Sin fecha' }}
                                        </span>
                                    </div>
                                    <!-- timeline item center dot -->
                                    <div class="col-sm-2 text-center flex-column d-sm-flex bar" >
                                        <div class="row h-50">
                                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                                            <div class="col">&nbsp;</div>
                                        </div>
                                        <h2 class="m-2">
                                            <span class="badge rounded-circle border border-4 border-primary text-primary text-shadow" >{{ count($visitas)-$key }}</span>
                                        </h2>
                                        <div class="row h-50">
                                            <div class="col border-end border-4 border-primary">&nbsp;</div>
                                            <div class="col">&nbsp;</div>
                                        </div>
                                    </div>
                                    @enddesktop
                                    <div class="col-sm-7 py-2 box-shadow rounded {{$color}}">
                                        <div class="card ">
                                            <div class="card-body {{$color}}">
                                                <h4 class="card-title text-center text-shadow">
                                                    <b class="d-xss-none">{{ count($visitas)-$key }}. </b>
                                                    {{ $visita->VIS_tipo ?? 'Visita' }}: {{ $visita->VIS_titulo ?? 'Sin título' }}
                                                </h4>
                                                <p class="card-text">
                                                    <ul class="list-group">
                                                        <li class="list-group-item border-0">
                                                            <a class="text-decoration-none" href="/formulario/buscaFormularios/{{$VIS_id}}"><i class="bi bi-database"></i> Formularios</a>
                                                        </li>
                                                        <li class="list-group-item border-0">
                                                            <a class="text-decoration-none" href="/visita/actaVisita/{{$VIS_id}}">
                                                                <i class="bi bi-file-earmark-medical-fill"></i> Acta de visita
                                                            </a>
                                                        </li>
                                                        <li class="list-group-item border-0">
                                                            <a class="text-decoration-none" href="/recomendaciones/{{$VIS_id}}">
                                                                <i class="bi bi-file-earmark-text-fill"></i> Recomendaciones
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END timeline item -->
                                @php $VIS_id = 'x'; @endphp
                            @endif
                        @endforeach
                    @else
                        <div class="alert alert-warning mx-5 mt-2 text-center" role="alert" data-bs-toggle="modal" data-bs-target="#nuevoFormulario">
                            No existen visitas programadas para éste establecimiento
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir documentos (solo administradores) -->
@if(Auth::user()->rol == 'Administrador' && isset($establecimiento))
<div class="modal fade" id="modalSubirDocumento" tabindex="-1" aria-labelledby="modalSubirDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSubirDocumentoLabel">Subir documento </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('visita.guardarDocumento') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="EST_id" value="{{ $establecimiento->EST_id ?? '' }}">
                    <input type="hidden" name="tipo_documento" id="tipo_documento">
                    
                    <div class="mb-3">
                        <label for="documento" class="form-label" id="documento_label">Seleccione el archivo</label>
                        <input type="file" class="form-control" id="documento" name="documento" required>
                        <div class="form-text">Formatos permitidos: PDF, JPG, JPEG, PNG. Tamaño máximo: 20MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modales para visualizar documentos (todos los usuarios) -->
@if(isset($documentos['reglamento']))
<div class="modal fade" id="modalVerReglamento" tabindex="-1" aria-labelledby="modalVerReglamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerReglamentoLabel">Reglamento del Centro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                @if(isset($documentos['reglamento']->ARC_formatoArchivo) && $documentos['reglamento']->ARC_formatoArchivo == 'image')
                    <img src="{{ asset($documentos['reglamento']->ARC_ruta) }}" class="img-fluid" alt="Reglamento del centro">
                @elseif(isset($documentos['reglamento']->ARC_extension) && $documentos['reglamento']->ARC_extension == 'pdf')
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ asset($documentos['reglamento']->ARC_ruta) }}" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Este documento no se puede previsualizar. Por favor, descárguelo para verlo.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                @if(isset($documentos['reglamento']->ARC_ruta))
                    <a href="{{ asset($documentos['reglamento']->ARC_ruta) }}" download class="btn btn-primary">
                        <i class="bi bi-download"></i> Descargar
                    </a>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($documentos['fachada']))
<div class="modal fade" id="modalVerFachada" tabindex="-1" aria-labelledby="modalVerFachadaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerFachadaLabel">Fotografía de la Fachada</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                @if(isset($documentos['fachada']->ARC_formatoArchivo) && $documentos['fachada']->ARC_formatoArchivo == 'image')
                    <img src="{{ asset($documentos['fachada']->ARC_ruta) }}" class="img-fluid" alt="Fachada del establecimiento">
                @elseif(isset($documentos['fachada']->ARC_extension) && $documentos['fachada']->ARC_extension == 'pdf')
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ asset($documentos['fachada']->ARC_ruta) }}" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Este documento no se puede previsualizar. Por favor, descárguelo para verlo.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                @if(isset($documentos['fachada']->ARC_ruta))
                    <a href="{{ asset($documentos['fachada']->ARC_ruta) }}" download class="btn btn-primary">
                        <i class="bi bi-download"></i> Descargar
                    </a>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(isset($documentos['licencia']))
<div class="modal fade" id="modalVerLicencia" tabindex="-1" aria-labelledby="modalVerLicenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerLicenciaLabel">Licencia de Funcionamiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                @if(isset($documentos['licencia']->ARC_formatoArchivo) && $documentos['licencia']->ARC_formatoArchivo == 'image')
                    <img src="{{ asset($documentos['licencia']->ARC_ruta) }}" class="img-fluid" alt="Licencia de funcionamiento">
                @elseif(isset($documentos['licencia']->ARC_extension) && $documentos['licencia']->ARC_extension == 'pdf')
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ asset($documentos['licencia']->ARC_ruta) }}" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Este documento no se puede previsualizar. Por favor, descárguelo para verlo.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                @if(isset($documentos['licencia']->ARC_ruta))
                    <a href="{{ asset($documentos['licencia']->ARC_ruta) }}" download class="btn btn-primary">
                        <i class="bi bi-download"></i> Descargar
                    </a>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal para editar la ficha del establecimiento --}}
@if(Auth::user()->rol == 'Administrador' && isset($establecimiento))
<div class="modal fade" id="modalEditarFichaEstablecimiento" tabindex="-1" aria-labelledby="modalEditarEstablecimientoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            @include('visita.editar-ficha-establecimiento')
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarEstablecimiento()">
                    <i class="bi bi-check-lg me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
// Función para guardar el establecimiento
function guardarEstablecimiento() {
    const form = document.getElementById('formEditarEstablecimiento');
    if (!form) {
        console.error('Formulario no encontrado');
        return;
    }
    
    const formData = new FormData(form);
    
    // Agregar el token CSRF manualmente
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('Token CSRF no encontrado');
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Guardando cambios...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });
    
    // Enviar formulario via AJAX
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'La información se actualizó correctamente',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cerrar modal y recargar página
                    const modalElement = document.getElementById('modalEditarFichaEstablecimiento');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                        modal.hide();
                    }
                    location.reload();
                }
            });
        } else {
            throw new Error(data.message || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Verificar si hay errores de validación
        if (error.errors) {
            let errorMessage = 'Por favor corrija los siguientes errores:\n';
            Object.keys(error.errors).forEach(field => {
                const fieldErrors = error.errors[field];
                if (Array.isArray(fieldErrors)) {
                    fieldErrors.forEach(errorText => {
                        errorMessage += `• ${errorText}\n`;
                    });
                }
                
                // Marcar campo como inválido
                const inputElement = document.getElementById(field);
                const errorElement = document.getElementById(`error-${field}`);
                
                if (inputElement) {
                    inputElement.classList.add('is-invalid');
                }
                
                if (errorElement && Array.isArray(fieldErrors)) {
                    errorElement.textContent = fieldErrors[0];
                    errorElement.style.display = 'block';
                }
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Errores de Validación',
                text: errorMessage,
                confirmButtonColor: '#dc3545'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Ocurrió un error inesperado',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}

function prepararModal(tipoDocumento) {
    const tipoDocumentoElement = document.getElementById('tipo_documento');
    const modalTitle = document.getElementById('modalSubirDocumentoLabel');
    const documentoLabel = document.getElementById('documento_label');
    const documentoInput = document.getElementById('documento');
    
    if (!tipoDocumentoElement || !modalTitle || !documentoLabel || !documentoInput) {
        console.error('Elementos del modal no encontrados');
        return;
    }
    
    // Establecer el tipo de documento en el campo oculto
    tipoDocumentoElement.value = tipoDocumento;
    
    // Cambiar el título del modal según el tipo
    switch(tipoDocumento) {
        case 'reglamento':
            modalTitle.textContent = 'Subir Reglamento del Centro';
            documentoLabel.textContent = 'Seleccione el archivo del reglamento (PDF recomendado)';
            break;
        case 'licencia':
            modalTitle.textContent = 'Subir Licencia de Funcionamiento';
            documentoLabel.textContent = 'Seleccione el archivo de la licencia (PDF o imagen)';
            break;
        case 'fachada':
            modalTitle.textContent = 'Subir Fotografía de la Fachada';
            documentoLabel.textContent = 'Seleccione la imagen de la fachada (JPG, PNG)';
            break;
    }
    
    // Limpiar el input file
    documentoInput.value = '';
}

// Validación del formulario antes de enviar
document.addEventListener('DOMContentLoaded', function() {
    const formSubirDocumento = document.querySelector('#modalSubirDocumento form');
    
    if (formSubirDocumento) {
        formSubirDocumento.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const documento = document.getElementById('documento');
            const tipoDocumento = document.getElementById('tipo_documento');
            
            if (!documento || !tipoDocumento) {
                console.error('Elementos del formulario no encontrados');
                return;
            }
            
            if (!documento.files.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Archivo requerido',
                    text: 'Por favor seleccione un archivo para subir',
                    confirmButtonColor: '#4b6cb7'
                });
                return;
            }
            
            // Validar tamaño del archivo (20MB máximo)
            const maxSize = 20 * 1024 * 1024; // 20MB en bytes
            if (documento.files[0].size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'El archivo no debe superar los 20MB',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            // Validar tipo de archivo
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(documento.files[0].type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tipo de archivo no permitido',
                    text: 'Solo se permiten archivos PDF, JPG, JPEG y PNG',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            // Mostrar loading
            Swal.fire({
                title: 'Subiendo documento...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            
            // Enviar el formulario
            this.submit();
        });
    }

    // Limpiar errores al escribir en los campos del modal de edición
    const modalEditarElement = document.getElementById('modalEditarFichaEstablecimiento');
    if (modalEditarElement) {
        modalEditarElement.addEventListener('shown.bs.modal', function() {
            const inputs = document.querySelectorAll('#formEditarEstablecimiento .form-control');
            
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const errorElement = document.getElementById(`error-${this.id}`);
                    if (errorElement) {
                        errorElement.style.display = 'none';
                    }
                });
                
                // Efectos visuales
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    }
    
    // Mostrar mensajes de sesión con SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#28a745'
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#dc3545'
        });
    @endif
});

function cargarDatosModal() {
    // Limpiar errores previos cuando se abre el modal
    const errorElements = document.querySelectorAll('.invalid-feedback');
    const inputElements = document.querySelectorAll('.form-control');
    
    errorElements.forEach(el => {
        if (el) el.style.display = 'none';
    });
    
    inputElements.forEach(el => {
        if (el) el.classList.remove('is-invalid');
    });
}
</script>

@endsection