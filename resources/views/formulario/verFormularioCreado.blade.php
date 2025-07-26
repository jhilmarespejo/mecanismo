@extends('layouts.app')

@section('title', 'Formulario Detalles')

@section('content')
<div class="container my-5">
    <!-- Mensajes de éxito o error -->
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
    
    <!-- BREADCUMB -->
    @include('layouts.breadcrumbs', $breadcrumbs)

    <!-- Encabezado del formulario -->
    <div class="card shadow mb-4">
        <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="bi bi-journal-text"></i> {{ $formulario->FRM_titulo }}</h3>
            <!-- Botón para imprimir -->
            <a href="{{ route('formulario.imprimirFormulario', $formulario->FRM_id) }}" target="_blank" class="btn text-shadow btn-info text-white text-shadow">
                <i class="bi bi-printer"></i> Imprimir en PDF
            </a>
            <a href="{{ route('formulario.editar', $formulario->FRM_id) }}" class="btn text-shadow btn-info text-white text-shadow">
                <i class="bi bi-pencil-square"></i> Editar formulario
            </a>
        </div>
    </div>
    
    <!-- Preguntas -->
    <h4 class="text-primary mt-4"><i class="bi bi-list-task"></i> Preguntas</h4>
    
    <div class="mb-2">
        @forelse($preguntas as $index => $pregunta)
            @php
                $esSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'sección');
                $esSubSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'subsección');
            @endphp

            <!-- Sección -->
            @if($esSeccion)
                <h5 class="mt-4 text-uppercase fw-bold text-primary border-bottom pb-2">
                    <i class="bi bi-folder"></i> {{ $pregunta->BCP_pregunta }}
                </h5>
            <!-- Subsección -->
            @elseif($esSubSeccion)
                <h6 class="ms-3 mt-3 fw-semibold text-secondary">
                    <i class="bi bi-folder2"></i> {{ $pregunta->BCP_pregunta }}
                </h6>
            <!-- Pregunta -->
            @else
            <div class="ms-5 mb-1 p-1 border rounded shadow-sm bg-light row">
                <label class="fw-bold d-flex flex-wrap align-items-center"> <!-- Añadido flex-wrap para que el contenido no se desborde -->
            
                        @if( $pregunta->BCP_tipoRespuesta == 'Casilla verificación')
                            <div class="col-12">
                                <label class="fw-bold"> 
                                    {{-- <i class="bi bi-check-square  text-success me-2 "></i> --}}
                                    {{ $pregunta->BCP_pregunta }}</label>
                                {{-- {{ $pregunta->BCP_pregunta }} --}}
                            </div>
                        @endif
                        @if($pregunta->BCP_tipoRespuesta == 'Lista desplegable' )
                            <div class="col-12">
                                <label class="fw-bold">{{--<i class="bi bi-check-circle text-success me-2 "></i>--}} {{ $pregunta->BCP_pregunta }}</label>
                            </div>
                        @endif
                        @if($pregunta->BCP_tipoRespuesta == 'Respuesta corta')
                            <div class="col-12 mt-2">
                                <label class="fw-bold">{{--<i class="bi bi-pencil-square me-2 text-warning"></i>--}} {{ $pregunta->BCP_pregunta }}</label> <!-- Mostrar la pregunta -->
                                <div class="col-12 col-md-7 mt-2">
                                    <input type="text" class="form-control" placeholder="Ingrese una respuesta...">
                                </div>
                            </div>
                        @endif

                        @if($pregunta->BCP_tipoRespuesta == 'Numeral')
                            <div class="col-12 mt-2">
                                <label class="fw-bold">{{--<i class="bi bi-123 me-2 text-info"></i>--}} 
                                     {{ $pregunta->BCP_pregunta }}</label> <!-- Mostrar la pregunta -->
                                <div class="col-12 col-md-7 mt-2">
                                    <input type="number" class="form-control" placeholder="Ingrese una respuesta numérica...">
                                </div>
                            </div>
                        @endif
                    
                    </label>
                    
                    
                    <!-- Opciones si las tiene -->
                    @php $opciones = json_decode($pregunta->BCP_opciones, true); @endphp
                    
                    @if($opciones && in_array($pregunta->BCP_tipoRespuesta, ['Casilla verificación', 'Lista desplegable']))
                        @php
                            $totalOpciones = count($opciones); // Total de opciones
                            $columnas = 3; // Número de columnas
                            $filas = ceil($totalOpciones / $columnas); // Calcular número de filas
                            $inputType = $pregunta->BCP_tipoRespuesta == 'Casilla verificación' ? 'checkbox' : 'radio'; // Determina el tipo de entrada
                            $name = $pregunta->BCP_id; // Nombre base del input
                            $name .= $inputType == 'checkbox' ? '[]' : ''; // Añade [] para checkboxes
                        @endphp
                        
                        <div class="row">
                            <!-- 
                                Dividimos las opciones en columnas usando array_chunk().
                                Esto permite distribuir las opciones de manera equitativa en las columnas especificadas.
                            -->
                            @php
                                // array_chunk() divide el array $opciones en sub-arrays (columnas)
                                // ceil(count($opciones) / $columnas) calcula cuántas opciones van en cada columna
                                $opcionesPorColumna = array_chunk($opciones, ceil(count($opciones) / $columnas));
                            @endphp

                            <!-- Iteramos cada columna de opciones -->
                            @foreach($opcionesPorColumna as $columnaOpciones)
                                <!-- 
                                    Cada columna ocupa:
                                    - 12/12 espacio en móviles (col-12)
                                    - 6/12 en tablets (col-sm-6)
                                    - 4/12 en desktop (col-md-4)
                                -->
                                <div class="col-12 col-sm-6 col-md-4 mb-2">
                                    
                                    <!-- Iteramos cada opción dentro de la columna actual -->
                                    @foreach($columnaOpciones as $key => $opcion)
                                        <!-- Contenedor para cada opción (checkbox/radio + label) -->
                                        <div class="form-check mt-1">
                                            <!-- 
                                                Input dinámico:
                                                - Tipo depende de $inputType (checkbox para múltiple selección, radio para selección única)
                                                - Name lleva [] si es checkbox para recibir array en backend
                                                - ID y FOR usan BCP_id + key para ser únicos
                                                - Value es el texto de la opción
                                            -->
                                            <input 
                                                class="form-check-input" 
                                                type="{{ $inputType }}" 
                                                name="{{ $name }}" 
                                                id="opcion_{{ $pregunta->BCP_id }}_{{ $key }}" 
                                                value="{{ $opcion }}">
                                            
                                            <!-- Label asociado al input mediante ID -->
                                            <label class="form-check-label" for="opcion_{{ $pregunta->BCP_id }}_{{ $key }}">
                                                {{ $opcion }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    
                    <!-- Mostrar complemento -->
                    @if($pregunta->BCP_complemento)
                        <div class="mt-2 text-muted">
                            <small><i class="bi bi-info-circle"></i> {{ $pregunta->BCP_complemento }}: </small>
                            <input type="text" class="form-control mt-1" placeholder="..."> <!-- Añadido margen para responsividad -->
                        </div>
                    @endif
                
                </div>
            @endif
        @empty
            <p class="text-muted">No hay preguntas registradas para este formulario.</p>
        @endforelse
    </div>
</div>
@endsection
