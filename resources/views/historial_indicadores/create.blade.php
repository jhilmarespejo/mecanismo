@extends('layouts.app')
@section('title', 'Crear histori')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">Crear Historial de Indicador</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('historial-indicadores.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="HIN_respuesta" class="form-label">Respuesta</label>
                            <textarea class="form-control" id="HIN_respuesta" name="HIN_respuesta" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="HIN_fecha_respuesta" class="form-label">Fecha de Respuesta</label>
                            <input type="date" class="form-control" id="HIN_fecha_respuesta" name="HIN_fecha_respuesta" required>
                        </div>

                        <div class="mb-3">
                            <label for="HIN_fuente_verificacion" class="form-label">Fuente de Verificación</label>
                            <input type="text" class="form-control" id="HIN_fuente_verificacion" name="HIN_fuente_verificacion" required>
                        </div>

                        <div class="mb-3">
                            <label for="FK_IND_id" class="form-label">Pregunta</label>
                            <select name="FK_IND_id" class="form-select" required>
                                <option value="">Selecciona una pregunta...</option>
                                @foreach($preguntas as $id => $pregunta)
                                    <option value="{{ $id }}">{{ $pregunta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script>

    </script>

@endsection
