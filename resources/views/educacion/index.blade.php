@extends('layouts.app')
@section('title', 'Módulo Educativo')
@section('content')



<div class="container mt-3 p-4 bg-white">

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif




    @include('layouts.breadcrumbs', $breadcrumbs)
  <!-- Estadísticas -->
  <h1 class="mb-2 text-center text-primary">Módulo Educativo</h1>
<!-- Select box para Filtrar por el año -->
<div class="row m-4 p-3 " style="background-color: #cfe2ff;">
    <form action="{{ route('educacion.index') }}" method="GET" class="mb-3">
        <label for="anio_actual" class="col-sm-8 col-form-label col-form-label-lg">Filtrar por año:</label>
        <select name="anio_actual" id="anio_actual" class="form-select form-select-lg" onchange="this.form.submit()">
            <option value="">Seleccionar año</option>
            <option value="2024" {{ $anioActual == '2024' ? 'selected' : '' }}>2024</option>
            <option value="2025" {{ $anioActual == '2025' ? 'selected' : '' }}>2025</option>
            <option value="2026" {{ $anioActual == '2026' ? 'selected' : '' }}>2026</option>
        </select>
    </form>
</div>

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
    <a href="{{ route('educacion.create') }}" class="btn btn-primary mb-3">Crear nuevo registro</a>
    <table class="table table-bordered" id="educativo">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tema</th>
                <th>Beneficiarios</th>
                <th>Cantidad</th>
                <th>Ciudad</th>
                <th>Medio de Verificación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($educacions as $educacion)
            <tr>
                <td>{{ $educacion->EDU_id }}</td>
                <td>{{ $educacion->EDU_tema }}</td>
                <td>{{ $educacion->EDU_beneficiarios }}</td>
                <td>{{ $educacion->EDU_cantidad_beneficiarios }}</td>
                <td>{{ $educacion->EDU_ciudad }}</td>
                <td>{{ $educacion->EDU_medio_verificacion }}</td>
                <td>
                    <a href="{{ route('educacion.edit', $educacion->EDU_id) }}" class="btn btn-sm col-8 btn-warning mb-2 box-shadow">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('educacion.destroy', $educacion->EDU_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm col-8 btn-danger box-shadow">
                          <i class="fas fa-trash-alt"></i>Eliminar
                      </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<style>
    /* ESTILOS PARA EL DATATABLE */

    .dataTables_wrapper .top {
        display: flex;
        justify-content: space-between;
    }
    .dataTables_info{
        padding-top: 0.3em !important;
    }

    table.modal-body tbody tr td{
        padding-top: 0px !important;
        padding-left: 10px !important;
    }
</style>
<script>
    $(document).ready(function() {
        $('#educativo').DataTable({
            "columnDefs": [
                {
                    "targets": [0], // Índice de la columna que deseas ocultar
                    "visible": false, // Hacer la columna no visible
                }
            ],
            "order": [[0, "desc"]],
            "dom": '<"top"ilf>rt<"bottom"p><"clear">',

            "language": {
                //"info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                "info": "<b>_TOTAL_</b> resultados",
                "lengthMenu": "Mostrar _MENU_ elementos",
                "search": "Buscar:"
            }
        });
    });
  </script>
{{-- Cantidad de Beneficiarios por Ciudad --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var data = @json($beneficiariosPorCiudad);

        var ciudades = data.map(function (item) {
            return item.edu_ciudad;
        });

        var beneficiarios = data.map(function (item) {
            return parseInt(item.total_beneficiarios);
        });

        Highcharts.chart('beneficiariosPorCiudadContainer', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Cantidad de Beneficiarios por Ciudad'
            },
            xAxis: {
                categories: ciudades,
                title: {
                    text: 'Ciudad'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cantidad de Beneficiarios'
                }
            },
            series: [{
                name: 'Beneficiarios',
                data: beneficiarios
            }],
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>

{{-- Cantidad de Beneficiarios por Tipo --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var data = @json($beneficiariosPorTipo);

        var tipos = data.map(function (item) {
            return item.edu_beneficiarios;
        });

        var beneficiarios = data.map(function (item) {
            return parseInt(item.total_beneficiarios);
        });

        Highcharts.chart('beneficiariosPorTipoContainer', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Cantidad de Beneficiarios por Tipo'
            },
            xAxis: {
                categories: tipos,
                title: {
                    text: 'Tipo de Beneficiarios'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cantidad de Beneficiarios'
                }
            },
            series: [{
                name: 'Beneficiarios',
                data: beneficiarios
            }],
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>

{{-- Cantidad de Temas por Ciudad --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var data = @json($temasPorCiudad);

        var ciudades = data.map(function (item) {
            return item.edu_ciudad;
        });

        var temas = data.map(function (item) {
            return parseInt(item.total_temas);
        });

        Highcharts.chart('temasPorCiudadContainer', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Cantidad de Temas por Ciudad'
            },
            xAxis: {
                categories: ciudades,
                title: {
                    text: 'Ciudad'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cantidad de Temas'
                }
            },
            series: [{
                name: 'Temas',
                data: temas
            }],
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>

{{-- //Temas Abordados y Cantidad de Beneficiarios --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var data = @json($temasBeneficiarios);

        var temas = data.map(function (item) {
            return item.edu_tema;
        });

        var beneficiarios = data.map(function (item) {
            return parseInt(item.cantidad_beneficiarios);
        });

        Highcharts.chart('temasAbordadosContainer', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Temas Abordados y Cantidad de Beneficiarios'
            },
            xAxis: {
                categories: temas,
                title: {
                    text: 'Tema'
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cantidad de Beneficiarios'
                }
            },
            series: [{
                name: 'Beneficiarios',
                data: beneficiarios
            }],
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.0f}'  // Mostrar valor en cada barra
                    }
                }
            }
        });
    });
</script>
@endsection
