@extends('layouts.app')
@section('title', 'Reportes - indicadores')

@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>

<style>
  
    /* @media (max-width: 768px) {
       .custom-select option {
           max-width: 300px;
       }
       
       .custom-select {
           padding: 0.625rem 0.875rem;
       }
       
       .select-card {
           padding: 1rem;
       }
    } */
</style>

<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)
    <h1 class="text-center text-primary">Reportes</h1>
    <div class="row g-4">
        <!-- Categorías -->
        <div class="col-12 col-md-4">
            <div class="select-card card text-dark bg-light mb-3">
                <div class="card-header fs-5 text-center">
                    Categoría:
                </div>
                <div class="card-body bg-white">
                    <select class="form-select custom-select" id="categoria">
                        <option value="">-- Seleccione una categoría --</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria }}" title="{{ $categoria }}">{{ $categoria }}</option>
                        @endforeach
                    </select>
                </div>
                
                
            </div>
        </div>
        <!-- Indicadores -->
        <div class="col-12 col-md-4">
            <div class="select-card card text-dark bg-light mb-3">
                <div class="card-header fs-5 text-center">
                    Indicador:
                </div>
                <div class="card-body bg-white">
                    <select class="form-select custom-select" id="indicador" disabled>
                        <option value="">-- Seleccione un indicador --</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Parámetros -->
        <div class="col-12 col-md-4">
            <div class="select-card card text-dark bg-light mb-3">
                <div class="card-header fs-5 text-center">
                    Parámetros:
                </div>
                <div class="card-body bg-white">
                    <select class="form-select custom-select" id="parametro" disabled>
                        <option value="">-- Seleccione un parámetro --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Para que los graficos tengan la misma altura */
        .container.row {
                display: flex;
                align-items: stretch;
            }

            .indicadores, .parametros {
                display: flex;
                flex-direction: column;
            }
            
            .card {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .grafico-container {
                flex-grow: 1;
            }
      </style>
      
    <div class="container row g-4 mt-4"  >
        <!-- Gráfico Estadístico del INDICADOR seleccionado -->
        <div class="col-12 col-md-6 indicadores"> <!-- Ocupa 8 columnas en pantallas medianas y más grandes, 12 en móviles -->
            <div id="graficoIndicadores" style="display: none;" class="card">
                <h3 id="tituloIndicador" class="mb-4"></h3>
                <div id="graficoIndicador" class="grafico-container"  ></div>
            </div>
        </div>
    
        <!-- Gráfico Estadístico del PARAMETRO seleccionado -->
        <div class="col-12 col-md-6 parametros"> <!-- Ocupa 4 columnas en pantallas medianas y más grandes, 12 en móviles -->
            <div id="graficoParametros" style="display: none;" class="card">
                <h3 id="tituloParametro" class="mb-4"></h3>
                <!-- Asegúrate de que el div interno tenga un id único -->
                <div id="graficoParametro" class="grafico-container"  ></div>
            </div>
        </div>
    </div>
</div>
<!-- Script para manejar la lógica de los combobox -->
<script>
    let chart = null;

    function actualizarGraficoParametros(parametroPorAnyo, nombreParametro) {
        const categorias = [];
        const resultados = [];
        
        console.log(parametroPorAnyo);
        Object.keys(parametroPorAnyo).sort().forEach(year => {
            const item = parametroPorAnyo[year];
            categorias.push(parseInt(year));

            // Verificamos exactamente los valores de HIN_respuesta
            let respuesta = item.HIN_respuesta;
            let valor, color;
            
            if (respuesta === "Si") {
                valor = 1;
                color = "green";
            } else if (respuesta === "No") {
                valor = -1;
                color = "orange";
            } else {
                // Casos donde la respuesta es null, undefined o diferente de "Si"/"No"
                valor = 0;
                color = "red";
            }

            resultados.push({ y: valor, color: color });

            console.log(`Año: ${year}, Respuesta: ${respuesta}, Valor: ${valor}, Color: ${color}`);
        });

        // Creamos el nuevo gráfico
        chart = Highcharts.chart('graficoParametros', {
            chart: {
                type: 'column'
            },
            title: {
                text: nombreParametro
            },
            xAxis: {
                categories: categorias
            },
            yAxis: {
                min: -1,
                max: 1,
                title: {
                    text: ''
                },
                labels: {
                    formatter: function () {
                        return this.value === 1 ? 'Si' : this.value === -1 ? 'No' : 'No reportado';
                    },
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                },
                tickPositions: [-1, 0, 1], // Mostrar etiquetas en -1, 0 y 1
                gridLineWidth: 1
            },
            tooltip: {
                formatter: function() {
                    return `<b>Gestión ${this.x}</b><br> Respuesta: ${
                        this.y === 1 ? 'Si' : this.y === -1 ? 'No' : 'No reportado'
                    }`;
                }
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return this.y === 1 ? 'Si' : this.y === -1 ? 'No' : '<span style="color: red;">Sin dato!</span>'; 
                        },
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    pointPadding: 0.2,
                    groupPadding: 0.1
                }
            },
            exporting: {
                csv: {
                    columnHeaderFormatter: function(item) {
                        if (item) {
                            return item.isXAxis ? 'Gestión' : nombreParametro;
                        }
                        return false;
                    }
                },
                filename: nombreParametro
            },
            series: [{
                name: 'Resultados por parámetro y gestión',
                data: resultados
            }],
            credits: {
                enabled: false
            }
        });

        $('#graficoParametros').show();
    }
        
    
        
    
    function actualizarGraficoIndicador(indicadorPorAnyo, nombreIndicador) {
        const categorias = [];
        const resultados = [];
        
        indicadorPorAnyo.forEach(function(item) {
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
           
            title: {
                text: nombreIndicador
            },
            xAxis: {
                categories: categorias,
                // title: {
                //     text: 'Gestión'
                // }
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
                name: 'Resultado por gindicador y estión',
                data: resultados,
                color: '#007bff'
            }],
            credits: {
                enabled: false
            }
        });
        // Actualizamos el título y mostramos el contenedor
        // $('#tituloIndicador').text(nombreIndicador);
        $('#graficoIndicadores').show();
    }

    $(document).ready(function () {
        // Cuando se selecciona una categoría
        $('#categoria').change(function () {
            let categoria = $(this).val();
            if (categoria) {
                $('#indicador').prop('disabled', false);
                $.ajax({
                    url: "{{ route('indicadores.reportes') }}",
                    type: "GET",
                    data: { categoria_id: categoria },
                    success: function (response) {
                        $('#indicador').empty().append('<option value="">-- Seleccione --</option>');
                        response.forEach(function (indicador) {
                            $('#indicador').append('<option value="' + indicador.IND_indicador + '">' + indicador.IND_numero +'. '+ indicador.IND_indicador + '</option>');
                        });

                        // $("#graficoIndicadores").hide();
                        // $("#graficoParametros").hide();
                    }
                });
            } else {
                $('#indicador').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#graficoIndicadores').hide();
            }
        });

        // Cuando se selecciona un indicador
        $('#indicador').change(function () {
            let indicadorId = $(this).val();
            let nombreIndicador = $('#indicador option:selected').text();
            
            if (indicadorId) {
                $('#parametro').prop('disabled', false);
                $.ajax({
                    url: "{{ route('indicadores.reportes') }}",
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
                        if (response.indicadorPorAnyo && response.indicadorPorAnyo.length > 0) {
                            // console.log(response.indicadorPorAnyo);
                            actualizarGraficoIndicador(response.indicadorPorAnyo, nombreIndicador);
                            // Mover la pantalla para enfocar en el div graficoIndicador
                            document.getElementById('graficoIndicador').scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            } else {
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#graficoIndicadores').hide();
            }
        });
        
        // Mantenemos la funcionalidad existente del parámetro
        $('#parametro').change(function () {
            let parametroId = $(this).val();
            let nombreParametro = $('#parametro option:selected').text();
            
            if (parametroId) {
                $.ajax({
                    url: "{{ route('indicadores.reportes') }}",
                    type: "GET",
                    data: { 
                        parametro_id: parametroId 
                    },
                    success: function (response) {
                        if (response.parametroPorAnyo) {
                            actualizarGraficoParametros(response.parametroPorAnyo, nombreParametro);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al obtener resultados:", error);
                    }
                });
            }
        });
    });
</script>


@endsection