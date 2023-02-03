{{-- Lista desplegable para mostrar los formularios --}}

{{-- mostar las cosultas agrupadas por establecimientos sin repetir nombres --}}

@if ( isset( $idFormularios ) )
    <form method="POST" id="form_formularios" action="{{route('index.buscarIdForm')}}" >
        <select class="form-select" id="select_formularios" name="formulario">
            <option selected disabled>Seleccione</option>
            @foreach ($idFormularios as $idFormulario)
                <option value="{{$formulario->FRM_titulo}}"> {{$formulario->FRM_titulo}}</option>
            @endforeach
        </select>
    </form>
@endif

{{-- Lista desplegable para mostrar las categorias del formulario selecciondo --}}
@if ( isset( $categoriasFormulario ) )
    <form method="POST" id="form_categorias" action="{{route('index.busquedaDinamica')}}" >
        <label for="exampleFormControlInput1" class="form-label">Categorias: </label>
        <div class="input-group">
            <select class="form-select" id="select_categorias" name="categoria">
                <option selected disabled>Seleccione</option>
                @foreach ($categoriasFormulario as $categoria)
                    <option value="{{$categoria->CAT_id}}"> {{$categoria->CAT_categoria}}</option>
                @endforeach
            </select>
            <div id="spinner_categorias" class="input-group-text visually-hidden">
                <div class="spinner-border text-primary"></div>
            </div>
            <input type="hidden" name="formularios" value="{{json_encode($idFromularios)}}">
            <input type="hidden" name="nombreCategoria" id="nombreCategoria" value="">
        </div>
    </form>
@endif

@if ( isset( $afirmaciones ) )

    @if ( count($afirmaciones) > 0 )
        <figure class="highcharts-figure">
            <div id="containerAfirmaciones"> </div>
        </figure>
        @php
            // $series = array();
            $items = json_decode(json_encode($afirmaciones), true);
            // dump($items);

            for ( $i=0; $i < count( $items ); $i++ ){
                $series[$i]= $items[$i]['BCP_pregunta'];
                $si[$i]= $items[$i]['si'];
                $no[$i]= $items[$i]['no'];
                $nulo[$i]= $items[$i]['nulo'];
            }

            // $series = json_encode( $series, JSON_UNESCAPED_UNICODE );

            // $categorias = json_encode(array_keys(array_slice($items[0], 2)), JSON_UNESCAPED_UNICODE );
            // $titulo = ' ';
            // dump( $series, $categorias );
            // echo ($series); //exit;
        @endphp
        <script >

            var series = <?php echo json_encode( $series, JSON_UNESCAPED_UNICODE ); ?>;
            var si = <?php echo json_encode( $si ); ?>;
            var no = <?php echo json_encode( $no ); ?>;
            var nulo = <?php echo json_encode( $nulo ); ?>;
            var titulo = '{{ $titulo }}';

            // console.log(series);
            Highcharts.chart('containerAfirmaciones', {
                chart: { type: 'column' },
                // title: { text: 'Historic World Population by Region' },
                xAxis: { categories: series },
                yAxis: [{ stackLabels: { enabled: true, } }],
                plotOptions: { column: { dataLabels: { enabled: true } } },
                title: { text: titulo },
                series: [{ name: 'Si', data: si }, { name: 'No', data: no }, { name: 'Sin respuesta', data: nulo } ]
            });

            // var datos = <?php echo '$categorias'; ?>;
                //     var titulo = '{{ $titulo }}';
                //     var series = <?php echo '$series'; ?>;
                //     console.log( datos, series );
                //     Highcharts.chart('containerAfirmaciones', {
                //             chart: {
                //                 type: 'bar',
                //                 borderColor: '#ced4da',
                //                 borderWidth: 1,
                //             },
                //             plotOptions: {
                //                 bar: { stacking: 'normal' },
                //                 column: {
                //                     dataLabels: {
                //                         // enabled: true
                //                     },
                //                 },
                //             },
                //             title: { text: titulo },
                //             xAxis: { categories: datos,},
                //             yAxis: {
                //                 allowDecimals: false,
                //                 min: 0,
                //                 title: {
                //                     text: 'Cantidad'
                //                 },
                //                 stackLabels: {
                //                     enabled: true,
                //                     style: {
                //                         color: 'black',
                //                     },
                //                     formatter: function () {
                //                         return this.total + "";
                //                     }
                //                 }
                //             },
                //             tooltip: {
                //                 formatter: function () {
                //                     return this.series.name + ': ' + this.y ;
                //                 }
                //             },
                //             legend: {
                //                 layout: 'horizontal',
                //                 align: 'center',
                //                 verticalAlign: 'bottom',
                //                 // x: -40, // y: 300,
                //                 floating: false,
                //                 borderWidth: 2,
                //                 backgroundColor:
                //                 Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                //                 shadow: true
                //             },
                //             credits: {
                //                 text: 'MNP - Mecanismo Nacional de la Prevención de la Tortura',
                //                 href: 'https://www.defensoria.gob.bo'
                //             },
                //             series: series,
        // });
        </script>
    @else
        <p class="text-danger">Esta categoría es diferente</p>
    @endif

@endif


@if ( isset($preguntas) and count($preguntas) > 0 )
    <form method="POST" id="form_preguntas" action="{{route('index.buscarListasCasillas')}}" >
        <label class="form-label">Seleccione una opcion: </label>
        <div class="input-group">
            <select class="form-select" id="select_preguntas" name="BCP_id">
                <option selected disabled>Seleccione</option>
                @foreach ($preguntas as $pregunta)
                    <option value="{{$pregunta->BCP_id}}"> {{$pregunta->BCP_pregunta}}</option>
                @endforeach
            </select>
            <div id="spinner_preguntas" class="input-group-text visually-hidden">
                <div class="spinner-border text-primary"></div>
            </div>
            <input type="hidden" name="formularios" value="{{ $formularios }}">
        </div>
    </form>
@endif

@if ( isset( $listaCasillas ) )
    <figure class="highcharts-figure">
        <div id="containerPreguntas"> </div>
    </figure>
        @php
            $series = array();
            $items = json_decode(json_encode($listaCasillas), true);
            for ( $i=0; $i < count( $items ); $i++ ){
                array_push( $series, ['name' => $items[$i]['EST_nombre'], 'data' => array_values( array_slice($items[$i], 2)) ] );
            }

            $series = json_encode( $series );
            $categorias = json_encode(array_keys(array_slice($items[0], 2)), JSON_UNESCAPED_UNICODE );
            // $titulo = ' ';
            // dump($series,$categorias);
        @endphp

        <script >
            var datos = <?php echo $categorias; ?>;
            var titulo = '{{ $titulo }}';
            var series = <?php echo $series?>;
            // console.log( datos );
            Highcharts.chart('containerPreguntas', {
                chart: {
                    type: 'bar',
                    borderColor: '#ced4da',
                    borderWidth: 1,
                },
                plotOptions: {
                    bar: { stacking: 'normal' },
                },
                title: { text: titulo },
                xAxis: { categories: datos,},
                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: 'Cantidad'
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            color: 'black',
                        },
                        formatter: function () {
                            return this.total + "";
                        }
                    }
                },
                tooltip: {
                    formatter: function () {
                        return this.series.name + ': ' + this.y ;
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    // x: -40, // y: 300,
                    floating: false,
                    borderWidth: 2,
                    backgroundColor:
                    Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                    shadow: true
                },
                credits: {
                    text: 'MNP - Mecanismo Nacional de la Prevención de la Tortura',
                    href: 'https://www.defensoria.gob.bo'
                },
                series: series,
            });
        </script>


@endif


