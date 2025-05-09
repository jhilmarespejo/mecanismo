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
                                        <span>{{ $establecimiento->EINF_poblacion_atendida ?? 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">CANTIDAD POBLACIÓN ACTUAL:</span>
                                        <span>{{ $establecimiento->EINF_cantidad_actual_internos ?? 'No registrado' }}</span>
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
                                        @if($responsable->EPER_grado_profesion)
                                            <small class="text-muted d-block">{{ $responsable->EPER_grado_profesion }}</small>
                                        @endif
                                        @if($responsable->EPER_telefono || $responsable->EPER_email)
                                            <small class="d-block">
                                                @if($responsable->EPER_telefono)
                                                    <i class="bi bi-telephone"></i> {{ $responsable->EPER_telefono }}
                                                @endif
                                                @if($responsable->EPER_email)
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
                                        <span>{{ $establecimiento->EINF_superficie_terreno ? $establecimiento->EINF_superficie_terreno . ' m²' : 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">SUPERFICIE CONSTRUIDA:</span>
                                        <span>{{ $establecimiento->EINF_superficie_construida ? $establecimiento->EINF_superficie_construida . ' m²' : 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">DERECHO PROPIETARIO:</span>
                                        <span>{{ $establecimiento->EINF_derecho_propietario ?? 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">AÑO DE FUNCIONAMIENTO:</span>
                                        <span>{{ $establecimiento->EST_anyo_funcionamiento ?? 'No registrado' }}</span>
                                    </div>
                                    
                                    <div class="info-row">
                                        <span class="info-label">GESTIÓN:</span>
                                        <span>{{ $establecimiento->EINF_gestion ?? date('Y') }}</span>
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
                                                                onclick="prepararModal('fachada')">
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
                                    <a href="{{ route('establecimientos.infoMostrar', $establecimiento->EST_id) }}" 
                                       class="btn btn-primary">
                                        <i class="bi bi-pencil-square"></i> Modificar ésta información
                                    </a>
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
                            @include('visita.visita-nuevo', ['EST_id' => $establecimiento->EST_id, 'EST_nombre' => $establecimiento->EST_nombre])
                        </div>
                    @endif
                    
                    @if(count($visitas) > 0)
                        @foreach($visitas as $key => $visita)
                            @if($visita->VIS_id)
                                @php 
                                    $VIS_id = $visita->VIS_id;
                                    // Bloque para definir los colores por tipo de visita
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
                                            {{ \Carbon\Carbon::parse($visita->VIS_fechas)->format('d-m-Y') }}
                                        </span>
                                    </div>
                                    @endmobile
                                    @desktop
                                    <div class="col align-self-center text-end">
                                        <span class="text-end alert {{$color}}">
                                            {{ \Carbon\Carbon::parse($visita->VIS_fechas)->format('d-m-Y') }}
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
                                                    {{$visita->VIS_tipo}}: {{ $visita->VIS_titulo}}
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
                <h5 class="modal-title" id="modalSubirDocumentoLabel">Subir documento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('visita.guardarDocumento') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="EST_id" value="{{ $establecimiento->EST_id }}">
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
                @if($documentos['reglamento']->ARC_formatoArchivo == 'image')
                    <img src="{{ asset($documentos['reglamento']->ARC_ruta) }}" class="img-fluid" alt="Reglamento del centro">
                @elseif($documentos['reglamento']->ARC_extension == 'pdf')
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
                <a href="{{ asset($documentos['reglamento']->ARC_ruta) }}" download class="btn btn-primary">
                    <i class="bi bi-download"></i> Descargar
                </a>
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
                @if($documentos['fachada']->ARC_formatoArchivo == 'image')
                    <img src="{{ asset($documentos['fachada']->ARC_ruta) }}" class="img-fluid" alt="Fachada del establecimiento">
                @elseif($documentos['fachada']->ARC_extension == 'pdf')
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
                <a href="{{ asset($documentos['fachada']->ARC_ruta) }}" download class="btn btn-primary">
                    <i class="bi bi-download"></i> Descargar
                </a>
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
                @if($documentos['licencia']->ARC_formatoArchivo == 'image')
                    <img src="{{ asset($documentos['licencia']->ARC_ruta) }}" class="img-fluid" alt="Licencia de funcionamiento">
                @elseif($documentos['licencia']->ARC_extension == 'pdf')
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
                <a href="{{ asset($documentos['licencia']->ARC_ruta) }}" download class="btn btn-primary">
                    <i class="bi bi-download"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    function prepararModal(tipo) {
        document.getElementById('tipo_documento').value = tipo;
        
        // Actualizar el título del modal según el tipo de documento
        let titulo = 'Subir documento';
        let label = 'Seleccione el archivo';
        
        if (tipo === 'reglamento') {
            titulo = 'Subir reglamento del centro';
            label = 'Seleccione el archivo del reglamento';
        } else if (tipo === 'licencia') {
            titulo = 'Subir licencia de funcionamiento';
            label = 'Seleccione el archivo de la licencia';
        } else if (tipo === 'fachada') {
            titulo = 'Subir fotografía de la fachada';
            label = 'Seleccione la imagen de la fachada';
        }
        
        document.getElementById('modalSubirDocumentoLabel').textContent = titulo;
        document.getElementById('documento_label').textContent = label;
    }
</script>

@endsection