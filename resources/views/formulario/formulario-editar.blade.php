@extends('layouts.app')
@section('title', 'Editar Formulario')

@section('content')
<div class="container mt-3 p-4 bg-white">
    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
<!-- Contenedor principal -->
<div class="col-lg-12 mb-4">
    <div class="card shadow mb-4">
        <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="bi bi-journal-text"></i> {{ $formulario->FRM_titulo }}</h3>
            <!-- Botón para imprimir -->
            <a href="{{ route('formulario.imprimirFormulario', $formulario->FRM_id) }}" target="_blank" class="btn text-shadow btn-info text-white text-shadow">
                <i class="bi bi-printer"></i> Imprimir en PDF
            </a>
            <a href="{{ route('formulario.verFormularioCreado', $formulario->FRM_id) }}" class="btn text-shadow btn-info text-white text-shadow">
                <i class="bi bi-pencil-square"></i> Ver formulario
            </a>
        </div>
    </div>
    <div class="p-4 position-relative">
        <!-- Título y breadcrumbs -->
        @include('layouts.breadcrumbs', $breadcrumbs)
        <h3 class="text-primary fw-bold mb-4 text-center">Editar Formulario</h3>
        
        <form id="formularioEditar" method="POST" action="{{ route('formulario.actualizar', $formulario->FRM_id) }}">
            @csrf
            @method('PUT')
            
            <!-- Campo para el título del formulario -->
            <div class="mb-4 text-center">
                <label for="titulo_formulario" class="form-label fw-bold">Título del formulario</label>
                <input 
                    type="text" 
                    class="form-control form-control-lg shadow-sm text-center text-uppercase" 
                    id="titulo_formulario" 
                    name="FRM_titulo" 
                    placeholder="ESCRIBA EL TÍTULO DEL FORMULARIO AQUI..."  
                    value="{{ $formulario->FRM_titulo }}"
                />
            </div>
            
            <!-- Tipo de formulario -->
            <input type="hidden" name="FRM_tipo" id="FRM_tipo" value="{{ $formulario->FRM_tipo }}">
            
            <!-- Contenedor para las preguntas existentes y nuevas -->
            <div id="contenedor_pregunta_seleccionada" class="sortable mb-3">
                @php
                    $contador = 0;
                    $nivelActual = '';
                @endphp
                
                <!-- Preguntas existentes -->
                @foreach($preguntas as $index => $pregunta)
                    @if(strpos($pregunta->BCP_tipoRespuesta, 'Sección') !== false)
                        <!-- Sección -->
                        <div class="card mb-3" id="card_pregunta_existente_{{ $pregunta->BCP_id }}" data-rbf-id="{{ $pregunta->RBF_id }}" data-bcp-id="{{ $pregunta->BCP_id }}">
                            <div class="card-header bg-light border-primary">
                                <h5 class="mt-1 text-uppercase fw-bold text-primary d-flex align-items-center">
                                    <i class="bi bi-folder me-2 drag-handle" style="cursor: grab;"></i>
                                    <span>{{ $pregunta->BCP_pregunta }}</span>
                                    <button type="button" class="btn btn-outline-danger btn-sm ms-auto eliminar-pregunta-existente" 
                                            data-rbf-id="{{ $pregunta->RBF_id }}" data-bcp-id="{{ $pregunta->BCP_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </h5>
                            </div>
                            <input type="hidden" name="tipoRespuesta[]" value="Sección">
                        </div>
                        @php $nivelActual = explode(' ', $pregunta->BCP_pregunta)[0]; @endphp
                        
                    @elseif(strpos($pregunta->BCP_tipoRespuesta, 'Subsección') !== false)
                        <!-- Subsección -->
                        <div class="card mb-3 ms-4" id="card_pregunta_existente_{{ $pregunta->BCP_id }}" data-rbf-id="{{ $pregunta->RBF_id }}" data-bcp-id="{{ $pregunta->BCP_id }}">
                            <div class="card-header bg-light border-info">
                                <h5 class="mt-1 text-uppercase fw-bold text-info d-flex align-items-center">
                                    <i class="bi bi-folder2 me-2 drag-handle" style="cursor: grab;"></i>
                                    <span>{{ $pregunta->BCP_pregunta }}</span>
                                    <button type="button" class="btn btn-outline-danger btn-sm ms-auto eliminar-pregunta-existente" 
                                            data-rbf-id="{{ $pregunta->RBF_id }}" data-bcp-id="{{ $pregunta->BCP_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </h5>
                            </div>
                            <input type="hidden" name="tipoRespuesta[]" value="Subsección">
                        </div>
                        
                    @else
                        <!-- Pregunta normal -->
                        @php $contador++; @endphp
                        <div class="card mb-2 ms-4" id="card_pregunta_existente_{{ $pregunta->BCP_id }}" data-rbf-id="{{ $pregunta->RBF_id }}" data-bcp-id="{{ $pregunta->BCP_id }}">
                            <div class="card-header d-flex align-items-center">
                                <span class="numero-pregunta me-2 d-flex justify-content-center align-items-center rounded-circle border shadow-sm drag-handle"
                                    style="width: 36px; height: 36px; font-size: 16px; font-weight: bold; background-color: #f8f9fa; cursor: grab;">
                                    {{ $contador }}
                                </span>
                                <div class="input-group-text fs-5">
                                    @php
                                        $icono = '';
                                        if (strpos($pregunta->BCP_tipoRespuesta, 'Casilla') !== false) {
                                            $icono = '<i class="bi bi-list-check"></i>';
                                        } elseif (strpos($pregunta->BCP_tipoRespuesta, 'Lista') !== false) {
                                            $icono = '<i class="bi bi-ui-radios-grid"></i>';
                                        } elseif (strpos($pregunta->BCP_tipoRespuesta, 'Respuesta corta') !== false) {
                                            $icono = '<i class="bi bi-chat-left-text"></i>';
                                        } elseif (strpos($pregunta->BCP_tipoRespuesta, 'Numeral') !== false) {
                                            $icono = '<i class="bi bi-123"></i>';
                                        }
                                    @endphp
                                    {!! $icono !!}
                                </div>
                                <input 
                                    class="form-control pregunta col"
                                    type="text"
                                    value="{{ preg_replace('/^\d+\.\s*/', '', $pregunta->BCP_pregunta) }}"
                                    placeholder="Pregunta"
                                    readonly
                                />
                            </div>
                            <div class="card-body">
                                <!-- Mostrar el tipo de respuesta en forma adecuada según su tipo -->
                                <div class="border-bottom pb-2 mb-3">
                                    {{-- <strong>Tipo de pregunta</strong> <span class="badge bg-primary">{{ $pregunta->BCP_tipoRespuesta }}</span> --}}
                                    <span class="text-muted" style="font-size: 13px"><b>Tipo de respuesta: </b> {{ ($pregunta->BCP_tipoRespuesta == 'Respuesta corta' || $pregunta->BCP_tipoRespuesta == 'Respuesta larga')? 'Texto' : $pregunta->BCP_tipoRespuesta }}</span>
                                </div>
                                
                                @if(strpos($pregunta->BCP_tipoRespuesta, 'Casilla') !== false)
                                    <!-- Mostrar checkboxes -->
                                    @if(!empty($pregunta->BCP_opciones))
                                        @php
                                            $opciones = json_decode($pregunta->BCP_opciones, true);
                                        @endphp
                                        @if(is_array($opciones))
                                            <div class="opciones-container">
                                                <div class="row">
                                                    @foreach($opciones as $opcion)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" disabled>
                                                                <label class="form-check-label">{{ $opcion }}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                
                                @elseif(strpos($pregunta->BCP_tipoRespuesta, 'Lista') !== false)
                                    <!-- Mostrar radiobuttons -->
                                    @if(!empty($pregunta->BCP_opciones))
                                        @php
                                            $opciones = json_decode($pregunta->BCP_opciones, true);
                                        @endphp
                                        @if(is_array($opciones))
                                            <div class="opciones-container">
                                                <div class="row">
                                                    @foreach($opciones as $opcion)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" disabled>
                                                                <label class="form-check-label">{{ $opcion }}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                
                                @elseif(strpos($pregunta->BCP_tipoRespuesta, 'Respuesta corta') !== false)
                                    <!-- Campo de texto -->
                                    <div class="mb-3">
                                        <input type="text" class="form-control" placeholder="Ingrese una respuesta..." disabled>
                                    </div>
                                
                                @elseif(strpos($pregunta->BCP_tipoRespuesta, 'Numeral') !== false)
                                    <!-- Campo numérico -->
                                    <div class="mb-3">
                                        <input type="number" class="form-control" placeholder="Ingrese un valor numérico..." disabled>
                                    </div>
                                @endif
                                
                                <!-- Mostrar complemento si existe -->
                                @if(!empty($pregunta->BCP_complemento))
                                    <div class="mt-3 pt-2 border-top">
                                        <div class="form-text mb-2">
                                            <i class="bi bi-info-circle"></i> Complemento: {{ $pregunta->BCP_complemento }}
                                        </div>
                                        <div class="mb-3">
                                            <textarea class="form-control" rows="2" placeholder="Ingrese información complementaria..." disabled></textarea>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer text-end">
                                <button type="button" class="btn btn-warning btn-sm eliminar-pregunta-existente" 
                                        data-rbf-id="{{ $pregunta->RBF_id }}" data-bcp-id="{{ $pregunta->BCP_id }}">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            
            <hr>
            
            <!-- Botones para agregar elementos -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <!-- Botón para guardar formulario -->
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-save"></i> Guardar Cambios
                </button>
                
                <div class="d-flex gap-3">
                    <!-- Nueva Sección -->
                    <button type="button" class="btn btn-secondary btn-lg" id="nueva_seccion">
                        <i class="bi bi-folder-plus"></i> Nueva Sección
                    </button>
                    
                    <!-- Nueva Subsección -->
                    <button type="button" class="btn btn-secondary btn-lg" id="nueva_subseccion">
                        <i class="bi bi-folder-symlink"></i> Nueva Subsección
                    </button>
                    
                    <!-- Nueva Pregunta -->
                    <button type="button" class="btn btn-primary btn-lg" id="nueva_pregunta">
                        <i class="bi bi-plus-circle"></i> Nueva Pregunta
                    </button>
                </div>
            </div>
            
            <!-- Campos ocultos para el formulario -->
            <input type="hidden" name="listaPreguntasJSON" id="listaPreguntasJSON">
            <input type="hidden" name="preguntasEliminar" id="preguntasEliminar">
        </form>
    </div>
</div>

<style>
    /* Estilo para secciones y subsecciones */
    .card-header h5 {
        margin-bottom: 0;
    }
    
    /* Estilos para arrastrar y soltar */
    .drag-handle {
        cursor: grab;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .ui-state-highlight {
        height: 70px;
        background-color: #f0f8ff;
        border: 2px dashed #007bff;
        margin-bottom: 1rem;
    }
    
    /* Estilos para preguntas */
    .numero-pregunta {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    /* Estilos para validación */
    .form-control.is-invalid,
    .was-validated .form-control:invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
</style>
</div>
@endsection

@section('js')


<style>
.drag-handle {
    cursor: grab;
}
.drag-handle:active {
   cursor: grabbing;
}
.ui-state-highlight {
   height: 100px;
   background-color: #f0f8ff;
   border: 2px dashed #007bff;
   margin-bottom: 1rem;
}
.card.is-invalid {
   border-color: #dc3545 !important;
   box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
}
.input-group.is-invalid {
   border-color: #dc3545 !important;
   box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
}
</style>
<script src="{{ asset('js/formulario-editar.js') }}"></script>
@endsection