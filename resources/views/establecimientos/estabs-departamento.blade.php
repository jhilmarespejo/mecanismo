<div class="card">
    <div class="card-header">
        1. Distribución de Establecimientos por Tipo
    </div>
    <div class="card-body">
        <div id="departamentos-chart"></div>
        <strong>Total general: {{ $totalGeneral }} lugres de detención</strong>

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Highcharts.chart('departamentos-chart', {
            chart: {
                type: 'column' // Puede ser 'bar', 'column', 'pie', etc.
            },
            title: {
                text: 'Establecimientos por Departamento'
            },
            xAxis: {
                type: 'category',
                title: {
                    text: 'Departamento'
                }
            },
            yAxis: {
                title: {
                    text: 'Cantidad de Establecimientos'
                }
            },
            series: [{
                name: 'Establecimientos',
                colorByPoint: true,
                data: @json($estabsPorDepartamento),
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



