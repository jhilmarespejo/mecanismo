@extends('layouts.app')
@section('title', 'Panel de datos - indicadores')

@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Evolución de Indicadores por Año</h3>
                </div>
                <div class="card-body">
                    <div id="graficoIndicadores" style="min-height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Procesamos los datos recibidos de Laravel
        const datosPhp = @json($resultados);
        
        // Obtenemos años únicos
        const years = [...new Set(datosPhp.map(item => item.year))];
        
        // Obtenemos indicadores únicos
        const indicadores = [...new Set(datosPhp.map(item => item.indicador_numero))];
        
        // Preparamos las series para Highcharts
        const series = indicadores.map(indicador => {
            const datosIndicador = years.map(year => {
                const dato = datosPhp.find(item => 
                    item.year === year && 
                    item.indicador_numero === indicador
                );
                return dato ? dato.porcentaje : 0;
            });

            // Encontramos el nombre completo del indicador
            const nombreIndicador = datosPhp.find(item => 
                item.indicador_numero === indicador
            ).indicador_nombre;

            return {
                name: `${indicador}. ${nombreIndicador}`,
                data: datosIndicador
            };
        });

        // Configuración del gráfico
        Highcharts.chart('graficoIndicadores', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Porcentaje de Cumplimiento de Indicadores por Año'
            },
            xAxis: {
                categories: years,
                title: {
                    text: 'Año'
                }
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Porcentaje de Cumplimiento'
                },
                labels: {
                    format: '{value}%'
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y:.1f}%</b><br/>',
                shared: true
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}%'
                    }
                }
            },
            series: series,
            credits: {
                enabled: false
            },
            exporting: {
                buttons: {
                    contextButton: {
                        menuItems: ['downloadPNG', 'downloadPDF', 'downloadXLS']
                    }
                }
            }
        });
    });
</script>
@endsection