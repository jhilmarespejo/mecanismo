<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            {{-- <h5 class="modal-title" id="staticBackdropLabel">Cantidad de visitas por tipo</h5> --}}
            {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
        </div>
        <div class="modal-body">
            <figure class="highcharts-figure">
                <div id="container2"></div>
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
<style>
    .highcharts-legend-item text{ font-size: 20px;}
</style>



<script type="text/javascript">
    Highcharts.chart('container2', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Tipos de visita'
        },
        yAxis: {
            title: {
                useHTML: true,
                text: 'Tipos de visita'
            }
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true
                },
            },
        },
        credits: {
            text: 'MNP - Mecanismo Nacional de Prevención de la Tortura',
            href: 'https://www.defensoria.gob.bo'
        },

        series: [{
            name: 'Visitas en profundidad',
            data: [12]

        }, {
            name: 'Visitas de seguimiento',
            data: [0]

        }, {
            name: 'Visitas reactivas',
            data: [0]

        }, {
            name: 'Visitas temáticas',
            data: [10]

        }, {
            name: 'Visitas Ad hoc',
            data: [0]

        }, {
            name: 'TOTAL',
            data: [10]

        },
    ],


    });


</script>
