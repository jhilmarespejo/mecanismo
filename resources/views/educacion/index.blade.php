@extends('layouts.app')
@section('title', 'Módulo Educativo')
@section('content')

<div class="container mt-3 p-4 bg-white">
    <h1 class="mb-2 text-center text-primary">Módulo Educativo</h1>
    @include('layouts.breadcrumbs', $breadcrumbs)
    
    <!-- Select box para Filtrar por el año -->
    <div class="row m-4 p-3" style="background-color: #cfe2ff;">
        <form action="{{ route('educacion.index') }}" method="GET" class="mb-3">
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

    <!-- Estadísticas -->
    <div class="my-5">
        <h2>Estadísticas</h2>
        <div class="row">
            <div class="col border border-1 m-1" id="beneficiariosPorCiudadContainer" style="height: 400px;"></div>
            <div class="col border border-1 m-1" id="beneficiariosPorTipoContainer" style="height: 400px;"></div>
        </div>
        <div class="row">
            <div class="col border border-1 m-1" id="temasPorCiudadContainer" style="height: 400px;"></div>
            <div class="col border border-1 m-1" id="temasAbordadosContainer" style="height: 400px;"></div>
        </div>
    </div>
</div>

<div class="container mt-3 p-4 bg-white">
    <h1 class="text-primary fs-2 text-center">Registro de actividades educativas</h1>
    <a href="{{ route('educacion.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle me-2"></i>Crear nuevo registro
    </a>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="educativo">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Tema</th>
                    <th>Beneficiarios</th>
                    <th>Cantidad</th>
                    <th>Ciudad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Gestión</th>
                    <th width="200px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($educacions as $educacion)
                <tr>
                    <td>{{ $educacion->EDU_id }}</td>
                    <td>{{ Str::limit($educacion->EDU_tema, 50) }}</td>
                    <td>{{ Str::limit($educacion->EDU_beneficiarios, 30) }}</td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $educacion->EDU_cantidad_beneficiarios }}</span>
                    </td>
                    <td>{{ $educacion->EDU_ciudad }}</td>
                    <td>{{ $educacion->EDU_fecha_inicio ? \Carbon\Carbon::parse($educacion->EDU_fecha_inicio)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $educacion->EDU_fecha_fin ? \Carbon\Carbon::parse($educacion->EDU_fecha_fin)->format('d/m/Y') : 'N/A' }}</td>
                    <td class="text-center">{{ $educacion->EDU_gestion }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('educacion.show', $educacion->EDU_id) }}" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('educacion.edit', $educacion->EDU_id) }}" 
                               class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-eliminar" 
                                    data-id="{{ $educacion->EDU_id }}" 
                                    data-tema="{{ Str::limit($educacion->EDU_tema, 30) }}" 
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="formEliminar" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.dataTables_wrapper .top {
    display: flex;
    justify-content: space-between;
}
.dataTables_info {
    padding-top: 0.3em !important;
}
table.modal-body tbody tr td {
    padding-top: 0px !important;
    padding-left: 10px !important;
}
.btn-group .btn {
    margin: 0 1px;
}
</style>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#educativo').DataTable({
        "columnDefs": [
            {
                "targets": [0], 
                "visible": false, 
            }
        ],
        "order": [[0, "desc"]],
        "dom": '<"top"ilf>rt<"bottom"p><"clear">',
        "language": {
            "info": "<b>_TOTAL_</b> resultados",
            "lengthMenu": "Mostrar _MENU_ elementos",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "emptyTable": "No hay datos disponibles",
            "zeroRecords": "No se encontraron registros coincidentes"
        }
    });

    // Manejar eliminación con SweetAlert
    $('.btn-eliminar').on('click', function() {
        const id = $(this).data('id');
        const tema = $(this).data('tema');
        
        Swal.fire({
            title: '¿Eliminar actividad?',
            html: `¿Está seguro de eliminar la actividad:<br><strong>"${tema}"</strong>?<br><br><small class="text-danger">Esta acción no se puede deshacer.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('#formEliminar');
                form.attr('action', `/educacion/${id}`);
                form.submit();
            }
        });
    });

    // Mostrar mensajes de sesión
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
    @endif
});

// Gráficos (manteniendo la funcionalidad existente)
// Cantidad de Beneficiarios por Ciudad
document.addEventListener('DOMContentLoaded', function () {
    var data = @json($beneficiariosPorCiudad);
    var ciudades = data.map(function (item) { return item.EDU_ciudad || item.edu_ciudad; });
    var beneficiarios = data.map(function (item) { return parseInt(item.total_beneficiarios); });

    Highcharts.chart('beneficiariosPorCiudadContainer', {
        chart: { type: 'column' },
        title: { text: 'Cantidad de Beneficiarios por Ciudad' },
        xAxis: { categories: ciudades, title: { text: 'Ciudad' } },
        yAxis: { min: 0, title: { text: 'Cantidad de Beneficiarios' } },
        series: [{ name: 'Beneficiarios', data: beneficiarios }],
        plotOptions: { column: { dataLabels: { enabled: true } } }
    });
});

// Cantidad de Beneficiarios por Tipo
document.addEventListener('DOMContentLoaded', function () {
    var data = @json($beneficiariosPorTipo);
    var tipos = data.map(function (item) { return item.EDU_beneficiarios || item.edu_beneficiarios; });
    var beneficiarios = data.map(function (item) { return parseInt(item.total_beneficiarios); });

    Highcharts.chart('beneficiariosPorTipoContainer', {
        chart: { type: 'bar' },
        title: { text: 'Cantidad de Beneficiarios por Tipo' },
        xAxis: { categories: tipos, title: { text: 'Tipo de Beneficiarios' } },
        yAxis: { min: 0, title: { text: 'Cantidad de Beneficiarios' } },
        series: [{ name: 'Beneficiarios', data: beneficiarios }],
        plotOptions: { bar: { dataLabels: { enabled: true } } }
    });
});

// Cantidad de Temas por Ciudad
document.addEventListener('DOMContentLoaded', function () {
    var data = @json($temasPorCiudad);
    var ciudades = data.map(function (item) { return item.EDU_ciudad || item.edu_ciudad; });
    var temas = data.map(function (item) { return parseInt(item.total_temas); });

    Highcharts.chart('temasPorCiudadContainer', {
        chart: { type: 'column' },
        title: { text: 'Cantidad de Temas por Ciudad' },
        xAxis: { categories: ciudades, title: { text: 'Ciudad' } },
        yAxis: { min: 0, title: { text: 'Cantidad de Temas' } },
        series: [{ name: 'Temas', data: temas }],
        plotOptions: { column: { dataLabels: { enabled: true } } }
    });
});

// Temas Abordados y Cantidad de Beneficiarios
document.addEventListener('DOMContentLoaded', function () {
    var data = @json($temasBeneficiarios);
    var temas = data.map(function (item) { return item.EDU_tema || item.edu_tema; });
    var beneficiarios = data.map(function (item) { return parseInt(item.cantidad_beneficiarios); });

    Highcharts.chart('temasAbordadosContainer', {
        chart: { type: 'bar' },
        title: { text: 'Temas Abordados y Cantidad de Beneficiarios' },
        xAxis: { categories: temas, title: { text: 'Tema' } },
        yAxis: { min: 0, title: { text: 'Cantidad de Beneficiarios' } },
        series: [{ name: 'Beneficiarios', data: beneficiarios }],
        plotOptions: { bar: { dataLabels: { enabled: true, format: '{point.y:.0f}' } } }
    });
});
</script>
@endsection