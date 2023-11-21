<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        {{-- <h5 class="modal-title" id="staticBackdropLabel">Visitas</h5> --}}
        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
      </div>
      <div class="modal-body">
        <figure class="highcharts-figure">
            <div id="container1"></div>
            <p class="highcharts-description">
                Se realizó un total de <strong>22 vistas 30 de marzo de 2023</strong>
            </p>
        </figure>
      </div>
      {{-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
      </div> --}}
    </div>
  </div>

<script type="text/javascript">
    // Build the chart
Highcharts.chart('container1', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },

    title: {
        text: 'Lugares de detención visitados'
    },
    tooltip: {
        pointFormat: '{series.name}: {point.percentage:.1f}%'
    },
    accessibility: {
        // point: {
        //     // valueSuffix: '%'
        // }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.y}',
                connectorColor: 'silver',

            },
            dataLabels: {
                formatter: function() {
                    const point = this.point;
                    return '<span style="color: ' + point.color + ';font-size: 12px;">' +
                    point.name + ': ' + point.y + '</span>';
                }
            }
        }
    },
    credits: {
        text: 'MNP - Mecanismo Nacional de Prevención de la Tortura',
        href: 'https://www.defensoria.gob.bo'
    },
    series: [{
        name: 'Cantidad',
        data: [
            { name: 'Celdas policiales', y: 0 },
            { name: 'Cuarteles', y: 0 },
            { name: 'Centros de acogida', y: 0 },
            { name: 'Centros penitenciarios', y: 10 },
            { name: 'Centros de reintegración p/adolescentes', y: 0 },
            { name: 'Centros de formación militar y policial', y: 12 },
            { name: 'Hospitales psiquiatricos', y: 0 }
            // { name: 'Centros de acogida (niños, adultos mayores y drogodependientes)', y: 17 },
            // { name: 'Centros de reintegración para adolescentes con responsabilidad penal', y: 14 },
        ],

    }]
});

</script>
