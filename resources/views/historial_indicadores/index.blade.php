@extends('layouts.app')
@section('title', 'Indicadores')

@section('content')

<div class="container">
    <h1>Historial de Indicadores</h1>
    <a href="historial-indicadores/create" class="btn btn-primary mb-3">Nuevo Registro</a>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Respuesta</th>
                <th scope="col">Fecha de Respuesta</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historialIndicadores as $historial)
            <tr>
                <td>{{ $historial->HIN_id }}</td>
                <td>{{ $historial->HIN_respuesta }}</td>
                <td>{{ $historial->HIN_fecha_respuesta }}</td>
                <td>
                    <a href="{{ route('historial-indicadores.show', $historial->HIN_id) }}" class="btn btn-info">Ver</a>
                    <a href="{{ route('historial-indicadores.edit', $historial->HIN_id) }}" class="btn btn-warning">Editar</a>
                    <!-- Agregar opción de eliminar si es necesario -->
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('js')
    <script>

    </script>

@endsection
