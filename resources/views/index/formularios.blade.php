
@php
    $series = array();
    foreach ($datos as $key=>$item){
        array_push( $series, ['name' => $item->EST_nombre, 'data' => array_values( array_slice($item->toArray(),3)) ] );
    }

    $series = json_encode( $series );
    $categorias = json_encode( array_keys(array_slice($datos->toArray()[0], 3)) );
    $titulo = $datos->toArray()[0]['BCP_pregunta'];
    dump( $series, $categorias, $titulo);
    // exit;
@endphp


<div class="col-sm-3 d-flex align-items-center">
    <h4>Opciones:</h4>
</div>
<div class="col-sm-9">
    <figure class="highcharts-figure">
        <div id="container1"> </div>
    </figure>
</div>

<script type="text/javascript">
    var s = <?php echo $series; ?>;
    console.log(s);
    Highcharts.chart('container1', {
        chart: { type: 'column' },
            title: {
                text: "<?php echo $titulo; ?>" /* el título del gráfico */
            },
            xAxis: {
                categories: <?php echo $categorias; ?> /*las opciones de respuesta*/
            },
            // yAxis: {
            //     allowDecimals: false,
            //     min: 0,
            //     title: {
            //         text: 'Cantidad'
            //     },
            //     stackLabels: {
            //         enabled: true,
            //         style: {
            //             color: 'black',
            //         },
            //         formatter: function () {
            //             return this.total + "";
            //         }
            //     }
            // },
            // tooltip: {
            //     formatter: function () {
            //         // return '<b>' + this.x + '</b><br/>';
            //         return this.series.name + ': ' + this.y ;
            //         // +'Total: ' + this.point.stackTotal;
            //     }
            // },
            plotOptions: {
                column: {
                    stacking: 'normal'
                },
            },
            series:  <?php echo $series; ?>, /* Los totales */
            credits: {
                text: 'MNP - Mecanismo Nacional de la Prevención de la Tortura',
                href: 'https://www.defensoria.gob.bo',
            },
        });
</script>






