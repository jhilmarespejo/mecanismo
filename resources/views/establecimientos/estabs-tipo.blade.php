
<div class="card">
    <div class="card-header">
        2. Establecimientos por Departamento
    </div>
    <div class="card-body">
        <div id="tipo-establecimientos-chart"></div>
        <strong>Total general: {{ $totalGeneral }} lugres de detención</strong>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        Highcharts.chart('tipo-establecimientos-chart', {
            chart: {
                type: 'pie' // Tipo de gráfico: pastel
            },
            title: {
                text: 'Distribución de Establecimientos por Tipo'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)' // Muestra nombre, cantidad y porcentaje
                    }
                }
            },
            series: [{
                name: 'Establecimientos',
                colorByPoint: true,
                data: @json($estabsPorTipo)
            }]
        });
    });
</script>