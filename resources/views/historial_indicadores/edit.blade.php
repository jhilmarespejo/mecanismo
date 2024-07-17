@extends('layouts.app')
@section('title', 'Modificación historial')

@section('content')

<div class="container">
    <h1>Editar Historial de Indicador</h1>
    <!-- Formulario para editar un historial de indicador existente -->
    <form action="{{ route('historial_indicadores.update', $historialIndicador->HIN_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="HIN_respuesta" class="form-label">Respuesta</label>
            <input type="text" class="form-control" id="HIN_respuesta" name="HIN_respuesta" value="{{ $historialIndicador->HIN_respuesta }}">
        </div>
        <div class="mb-3">
            <label for="HIN_fecha_respuesta" class="form-label">Fecha de Respuesta</label>
            <input type="text" class="form-control" id="HIN_fecha_respuesta" name="HIN_fecha_respuesta" value="{{ $historialIndicador->HIN_fecha_respuesta }}">
        </div>
        <div class="mb-3">
            <label for="HIN_fuente_verificacion" class="form-label">Fuente de Verificación</label>
            <input type="text" class="form-control" id="HIN_fuente_verificacion" name="HIN_fuente_verificacion" value="{{ $historialIndicador->HIN_fuente_verificacion }}">
        </div>
        <!-- Agregar más campos del formulario según sea necesario -->
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>
@endsection

@section('js')
    <script>

    </script>

@endsection
