<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            {{-- <h5 class="modal-title" id="staticBackdropLabel">Cantidad de visitas por tipo</h5> --}}
            {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
        </div>
        <div class="modal-body">
            <figure class="highcharts-figure">
                <div id="container4"></div>
                {{-- <p class="highcharts-description">
                    x.
                </p> --}}
            </figure>
        </div>
        {{-- <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
        </div> --}}
    </div>
</div>

<script type="text/javascript">
    Highcharts.chart('container4', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'Nivel de hacinamiento'
    },
    colors: [
            '#95CEFF',
            '#ff3300',
            // '#0000ff'
        ],
    // subtitle: {
    //     text: 'Source: <a ' +
    //         'href="https://en.wikipedia.org/wiki/List_of_continents_and_continental_subregions_by_population"' +
    //         'target="_blank">Wikipedia.org</a>'
    // },
    xAxis: {
        categories: ['Beni','Chuquisaca', 'Cochabamba', 'La Paz', 'Oruro', 'Pando', 'Potosí', 'Santa Cruz', 'Tarija', 'TOTAL'],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        // title: {
        //     text: 'Population (millions)',
        //     align: 'high'
        // },
        labels: {
            overflow: 'justify'
        }
    },
    // tooltip: {
    //     valueSuffix: ' millions'
    // },
    plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            }
        },

    },
    credits: {
        text: 'MNP - Mecanismo Nacional de Prevención de la Tortura',
        href: 'https://www.defensoria.gob.bo'
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'top',
        x: -40,
        y: 80,
        floating: true,
        borderWidth: 1,
        backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
        shadow: true
    },

    series: [{
        name: 'Capacidad',
        data: [308, 151, 1218, 1118, 562, 128, 660, 2802, 602, 7549]
    }, {
        name: 'Población',
        data: [942, 742, 2822, 4411, 933, 406, 933, 7609, 1204, 20002]
    },

    ]
});


</script>
