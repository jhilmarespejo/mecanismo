@extends('layouts.app')

@section('title', 'Formulario Detalles')

@section('content')
{{-- <div class="container my-5">
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

    <!-- Información del formulario -->
    <div class="card shadow mb-4">
        <div class="card-header text-white bg-primary">
            <h3 class="mb-0"><i class="bi bi-journal-text"></i> {{ $formulario->FRM_titulo }}</h3>
        </div>
    </div>

    <!-- Botón para imprimir el PDF -->
        <div class="d-flex justify-content-end mb-4">
            <button id="btnImprimir" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Imprimir PDF
            </button>
        </div>

    <!-- Preguntas como cuestionario -->
    <form method="POST" id="formularioCuestionario">
        @csrf
        <h4 class="text-primary mt-4"><i class="bi bi-list-task"></i> Preguntas</h4>

        <div class="mb-4">
            @forelse($preguntas as $index => $pregunta)
                @php
                    $esSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'sección');
                    $esSubSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'subsección');
                @endphp

                @if($esSeccion)
                    <!-- Sección -->
                    <h5 class="mt-4 text-uppercase fw-bold text-primary border-bottom pb-2">
                        <i class="bi bi-folder"></i> {{ $pregunta->BCP_pregunta }}
                    </h5>
                @elseif($esSubSeccion)
                    <!-- Subsección -->
                    <h6 class="ms-3 mt-3 fw-semibold text-secondary">
                        <i class="bi bi-folder2"></i> {{ $pregunta->BCP_pregunta }}
                    </h6>
                @else
                    <!-- Pregunta -->
                    <div class="ms-5 mb-3 p-3 border rounded shadow-sm bg-light">
                        <label class="fw-bold d-flex align-items-center">
                            @if($pregunta->BCP_tipoRespuesta == 'Lista desplegable')
                                <i class="bi bi-list-task me-2 text-primary"></i>
                            @elseif($pregunta->BCP_tipoRespuesta == 'Casilla verificación')
                                <i class="bi bi-check-square me-2 text-success"></i>
                            @elseif($pregunta->BCP_tipoRespuesta == 'Respuesta corta')
                                <i class="bi bi-pencil-square me-2 text-warning"></i>
                            @elseif($pregunta->BCP_tipoRespuesta == 'Numeral')
                                <i class="bi bi-123 me-2 text-info"></i>
                            @elseif($pregunta->BCP_tipoRespuesta == 'Archivo')
                                <i class="bi bi-paperclip me-2 text-danger"></i>
                            @endif
                            {{ $index + 1 }}. {{ $pregunta->BCP_pregunta }}
                        </label>

                        <!-- Renderizar tipo de respuesta -->
                        @if($pregunta->BCP_tipoRespuesta == 'Lista desplegable')
                            @php $opciones = json_decode($pregunta->BCP_opciones, true); @endphp
                            <select class="form-select mt-2" name="pregunta_{{ $pregunta->BCP_id }}">
                                <option selected disabled>Seleccione una opción...</option>
                                @foreach($opciones as $key => $opcion)
                                    <option value="{{ $opcion }}">{{ $opcion }}</option>
                                @endforeach
                            </select>
                        
                        @elseif($pregunta->BCP_tipoRespuesta == 'Casilla verificación')
                            @php $opciones = json_decode($pregunta->BCP_opciones, true); @endphp
                            @foreach($opciones as $key => $opcion)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="pregunta_{{ $pregunta->BCP_id }}" id="opcion_{{ $pregunta->BCP_id }}_{{ $key }}">
                                    <label class="form-check-label" for="opcion_{{ $pregunta->BCP_id }}_{{ $key }}">
                                        {{ $opcion }}
                                    </label>
                                </div>
                            @endforeach

                        @elseif($pregunta->BCP_tipoRespuesta == 'Respuesta corta')
                            <input type="text" class="form-control mt-2" placeholder="Escribe tu respuesta aquí...">

                        @elseif($pregunta->BCP_tipoRespuesta == 'Numeral')
                            <input type="number" class="form-control mt-2" placeholder="Ingresa un valor numérico">

                        @elseif($pregunta->BCP_tipoRespuesta == 'Archivo')
                            <input type="file" class="form-control mt-2">

                        @else
                            <p class="text-muted">Tipo de respuesta no definido.</p>
                        @endif
                    </div>
                @endif
            @empty
                <p class="text-muted">No hay preguntas registradas para este formulario.</p>
            @endforelse
        </div>
    </form>
</div> --}}
@endsection

@section('scripts')
<script>
//    document.getElementById('btnImprimir').addEventListener('click', function () {
//     var content = document.getElementById('formularioCuestionario').outerHTML; // Solo el formulario
//     var printWindow = window.open('', '', 'height=700,width=900');

//     // Incluir estilos de Bootstrap y Livewire en el PDF
//     printWindow.document.write('<html><head><title>Formulario</title>');
//     printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">');
//     printWindow.document.write('<style>body { font-family: Arial, sans-serif; }</style>');
//     printWindow.document.write('</head><body>');

//     // Eliminar los estilos Livewire dinámicos antes de imprimir
//     printWindow.document.write(content.replace(/<style[^>]*wire:id=[^>]*>.*?<\/style>/g, ''));

//     printWindow.document.write('</body></html>');
//     printWindow.document.close();
//     printWindow.print();
});
</script>
@endsection
