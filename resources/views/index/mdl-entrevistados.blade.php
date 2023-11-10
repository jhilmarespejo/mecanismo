<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            {{-- <h5 class="modal-title" id="staticBackdropLabel">Visitas</h5> --}}
            {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
        </div>
        <div class="modal-body">
            <figure class="highcharts-figure">
                <div id="container3"></div>
                {{-- <p class="highcharts-description">
                    Pie chart showing a hollow semi-circle. This is a compact visualization,
                    often used in dashboards.
                </p> --}}
            </figure>

        </div>
        {{-- <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
        </div> --}}
    </div>
</div>

<script type="text/javascript">
    // Data retrieved from https://netmarketshare.com/
    Highcharts.chart('container3', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: 'Cantidad total <br> entrevistados <br>por sexo <br>2023<br>TOTAL:150 ',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        colors: [
            '#ff6eb4',
            '#008b8b',
            '#000'
        ],
        // tooltip: {
        //     pointFormat: '{series.name}: <b>{point.y}%</b>'
        // },
        tooltip: {
            pointFormat: '{series.name}: {point.percentage:.1f}%'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%'],
                size: '130%'
            }

        },
        credits: {
            text: 'MNP - Mecanismo Nacional de Prevención de la Tortura',
            href: 'https://www.defensoria.gob.bo'
        },
        series: [{
            type: 'pie',
            name: 'Cantidad:',
            innerSize: '50%',
            data: [
                ['Mujeres', 263],
                ['Hombres', 1033],
                // ['TOTAL', 403]
            ],
            // ['Safari', 2.98],
            // ['Internet Explorer', 1.90],
            // {
            //     name: 'Other',
            //     y: 3.77,
            //     dataLabels: {
            //         enabled: false
            //     }
            // },
            dataLabels: {
                enabled: true,
                // rotation: -90,
                // color: '#FFFFFF',
                align: 'right',
                // format: '{point.y}', // one decimal
                format: '{point.name}:<br> {point.y}',
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });

</script>
