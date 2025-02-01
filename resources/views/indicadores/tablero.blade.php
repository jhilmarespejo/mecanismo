@extends('layouts.app')
@section('title', 'Tablero de datos - indicadores')

@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>

<style>
    .select-card {
       background: white;
       padding: 1.5rem;
       border-radius: 12px;
       box-shadow: 0 4px 6px rgba(0,0,0,0.1);
       transition: all 0.3s ease;
       border: 1px solid #e9ecef;
    }
    
    .select-card:hover {
       box-shadow: 0 8px 15px rgba(0,0,0,0.1);
       transform: translateY(-2px);
    }
    
    .custom-select {
       padding: 0.75rem 1rem;
       border: 2px solid #e9ecef;
       border-radius: 8px;
       font-size: 1rem;
       transition: all 0.3s ease;
       background-color: #fff;
       width: 100%;
    }
    
    .custom-select:not([disabled]) {
       cursor: pointer;
    }
    
    .custom-select:not([disabled]):hover {
       border-color: #80bdff;
    }
    
    .custom-select:focus {
       border-color: #80bdff;
       box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
       outline: 0;
    }
    
    .custom-select[disabled] {
       background-color: #f8f9fa;
       cursor: not-allowed;
       opacity: 0.8;
    }
    
    .custom-select option {
       padding: 10px;
       white-space: normal;
       word-wrap: break-word;
       min-width: 200px;
       max-width: 400px;
    }
    
    .text-primary {
       color: #0d6efd !important;
       text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    
    .form-label {
       margin-bottom: 0.75rem;
       font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
       .custom-select option {
           max-width: 300px;
       }
       
       .custom-select {
           padding: 0.625rem 0.875rem;
       }
       
       .select-card {
           padding: 1rem;
       }
    }
</style>

<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)
    <h1 class="text-center text-primary">Tablero de Indicadores</h1>
    <div class="row g-4">
        <!-- Categorías -->
        <div class="col-12 col-md-4">
            <div class="select-card">
                <label for="categoria" class="form-label fw-semibold text-secondary">Categoría:</label>
                <select class="form-select custom-select" id="categoria">
                    <option value="">-- Seleccione una categoría --</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria }}" title="{{ $categoria }}">{{ $categoria }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- Indicadores -->
        <div class="col-12 col-md-4">
            <div class="select-card">
                <label for="indicador" class="form-label fw-semibold text-secondary">Indicador:</label>
                <select class="form-select custom-select" id="indicador" disabled>
                    <option value="">-- Seleccione un indicador --</option>
                </select>
            </div>
        </div>
        <!-- Parámetros -->
        <div class="col-12 col-md-4">
            <div class="select-card">
                <label for="parametro" class="form-label fw-semibold text-secondary">Parámetros:</label>
                <select class="form-select custom-select" id="parametro" disabled>
                    <option value="">-- Seleccione un parámetro --</option>
                </select>
            </div>
        </div>
    </div>
    
    {{-- grafico estadistico del indicador seleccionado --}}
    <div class="container row g-4 mt-4"> 
        <div id="contenedorGrafico" style="display: none;">
            <h3 id="tituloIndicador" class="mb-4"></h3>
            <div id="graficoIndicador" style="width:100%; height:400px;"></div>
        </div>
    </div>

<!-- Script para manejar la lógica de los combobox -->
<script>
    let chart = null;

    function actualizarGrafico(promediosPorAnyo, nombreIndicador) {
        const categorias = [];
        const resultados = [];
        
        promediosPorAnyo.forEach(function(item) {
            categorias.push(item.gestion);
            resultados.push(parseFloat(item.resultado_final));
        });
        
        // Si ya existe un gráfico, lo destruimos
        if (chart) {
            chart.destroy();
        }
        
        // Creamos el nuevo gráfico
        chart = Highcharts.chart('graficoIndicador', {
            chart: {
                type: 'column'
            },
            // title: {
            //     text: 'Resultados por Gestión',
            //     margin: 5
            // },
            title: {
                text: nombreIndicador
            },
            xAxis: {
                categories: categorias,
                title: {
                    text: 'Gestión'
                }
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Porcentaje (%)'
                },
                labels: {
                    format: '{value}%'
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y:.1f}%</b><br/>'
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}%'
                    }
                }
            },
            exporting: {
                csv: {
                    columnHeaderFormatter: function(item) {
                        if (item) {
                            if (item.isXAxis) {
                                return 'Gestión';
                            }
                            // Para la columna de datos
                            return nombreIndicador;
                        }
                        return false; // Usa el encabezado por defecto si no hay item
                    }
                },
                filename: nombreIndicador // El archivo se guardará con el nombre del indicador
            },
            series: [{
                name: 'Resultado',
                data: resultados,
                color: '#007bff'
            }],
            credits: {
                enabled: false
            }
        });
        // Actualizamos el título y mostramos el contenedor
        // $('#tituloIndicador').text(nombreIndicador);
        $('#contenedorGrafico').show();
    }

    $(document).ready(function () {
        // Cuando se selecciona una categoría
        $('#categoria').change(function () {
            let categoria = $(this).val();
            if (categoria) {
                $('#indicador').prop('disabled', false);
                $.ajax({
                    url: "{{ route('indicadores.tablero') }}",
                    type: "GET",
                    data: { categoria_id: categoria },
                    success: function (response) {
                        $('#indicador').empty().append('<option value="">-- Seleccione --</option>');
                        response.forEach(function (indicador) {
                            $('#indicador').append('<option value="' + indicador.IND_indicador + '">' + indicador.IND_numero +'. '+ indicador.IND_indicador + '</option>');
                        });
                    }
                });
            } else {
                $('#indicador').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#contenedorGrafico').hide();
            }
        });

        // Cuando se selecciona un indicador
        $('#indicador').change(function () {
            let indicadorId = $(this).val();
            let nombreIndicador = $('#indicador option:selected').text();
            
            if (indicadorId) {
                $('#parametro').prop('disabled', false);
                $.ajax({
                    url: "{{ route('indicadores.tablero') }}",
                    type: "GET",
                    data: { indicador_indicador: indicadorId },
                    success: function (response) {
                        $('#parametro').empty().append('<option value="">-- Seleccione --</option>');
                        
                        if (response.parametros) {
                            response.parametros.forEach(function(parametro) {
                                $('#parametro').append(
                                    '<option value="' + parametro.IND_id + '">' + parametro.IND_parametro + '</option>'
                                );
                            });
                        }

                        // Actualizamos el gráfico con los nuevos datos
                        if (response.promediosPorAnyo && response.promediosPorAnyo.length > 0) {
                            console.log(response.promediosPorAnyo);
                            actualizarGrafico(response.promediosPorAnyo, nombreIndicador);
                        }
                    }
                });
            } else {
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#contenedorGrafico').hide();
            }
        });

        // Mantenemos la funcionalidad existente del parámetro
        $('#parametro').change(function () {
            let parametroId = $(this).val();
            if (parametroId) {
                $.ajax({
                    url: "{{ route('indicadores.resultados') }}",
                    type: "GET",
                    data: { 
                        parametro_id: parametroId 
                    },
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al obtener resultados:", error);
                    }
                });
            }
        });
    });
</script>
{{-- <script>
    $(document).ready(function () {
        // Cuando se selecciona una categoría
        $('#categoria').change(function () {
            let categoria = $(this).val();
            if (categoria) {
                // Habilitar el combobox de indicadores
                $('#indicador').prop('disabled', false);

                // Hacer una petición AJAX para obtener los indicadores de la categoría seleccionada
                $.ajax({
                    url: "{{ route('indicadores.tablero') }}",
                    type: "GET",
                    data: { categoria_id: categoria },
                    success: function (response) {
                        $('#indicador').empty().append('<option value="">-- Seleccione --</option>');
                        response.forEach(function (indicador) {
                            $('#indicador').append('<option value="' + indicador.IND_indicador + '">' + indicador.IND_numero +'. '+ indicador.IND_indicador + '</option>');
                        });
                    }
                });
            } else {
                $('#indicador').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
            }
        });

        // Cuando se selecciona un indicador
        $('#indicador').change(function () {
            let indicadorId = $(this).val();
            if (indicadorId) {
                // Habilitar el combobox de parámetros
                $('#parametro').prop('disabled', false);

                // Hacer una petición AJAX para obtener los parámetros del indicador seleccionado
                $.ajax({
                    url: "{{ route('indicadores.tablero') }}",
                    type: "GET",
                    data: { indicador_indicador: indicadorId },
                    success: function (response) {
                        $('#parametro').empty().append('<option value="">-- Seleccione --</option>');

                        if (response.parametros) {
                            // Recorre la lista de parámetros y agrega cada uno como opción
                            response.parametros.forEach(function(parametro) {
                                $('#parametro').append(
                                    '<option value="' + parametro.IND_id + '">' + parametro.IND_parametro + '</option>'
                                );
                            });
                        }
                    }
                });
            } else {
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
            }
        });

        // Cuando se selecciona un parámetro
        $('#parametro').change(function () {
            let parametroId = $(this).val();
            if (parametroId) {
                // Hacer una petición AJAX para obtener los resultados del parámetro
                $.ajax({
                    url: "{{ route('indicadores.resultados') }}", // Nueva ruta
                    type: "GET",
                    data: { 
                        parametro_id: parametroId 
                    },
                    success: function (response) {
                        // Aquí podrías manejar la respuesta, por ejemplo, 
                        // mostrar una tabla o gráfico con los resultados
                        console.log(response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al obtener resultados:", error);
                    }
                });
            }
        });
    });
</script> --}}

@endsection