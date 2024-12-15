<script src="charts/highcharts.js"></script>
<script src="charts/exporting.js"></script>
<script src="charts/export-data.js"></script>
{{-- <script src="charts/variable-pie.js"></script> --}}
<script src="charts/accessibility.js"></script>



@extends('layouts.app')
@section('title', 'Interoperabilidad')

@section('content')
    <div class="text-center p-3"><h3>Módulo de interoperabilidad</h3></div>
    <div class="row p-2">
        <div class="col me-1">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header py-1 px-4 text-center fs-5">Cantidad de personas privadas de libertad <br/> con <b>detención preventiva y con sentencia</b> por departamento</div>
                <div class="card-body" id="i-1"> </div>
            </div>
        </div>
        <div class="col ms-1">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header py-1 px-4 text-center fs-5">Cantidad de personas privadas de libertad <br/> <b>por sexo</b> y departamento</div>
                <div class="card-body" id="i-2"> </div>
            </div>
        </div>
    </div>

    <div class="row p-2 mt-2">
        <div class="col me-1">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header py-1 px-4 text-center fs-5">Cantidad de personas privadas de libertad <br/> por <b>delito</b> y departamento</div>
                <div class="card-body" id="i-3"> </div>
            </div>
        </div>
        <div class="col ms-1">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header py-1 px-4 text-center fs-5">Cantidad de personas privadas de libertad <br/> <b>por rango de edades</b> y departamento</div>
                <div class="card-body" id="i-4"> </div>
            </div>
        </div>
    </div>


    <div class="row p-2 mt-2">
        <div class="col me-1">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header py-1 px-4 text-center fs-5">Cantidad diaria ingresos nuevos a centros de privacion de libertad por departamento</div>
                <div class="card-body" id="i-5"> </div>
            </div>
        </div>
        <div class="col ms-1">
            <div class="card text-dark bg-light mb-3">
                <div class="card-header py-1 px-4 text-center fs-5">Cantidad diaria de egresos en centros de privación de libertad por departamento</div>
                <div class="card-body" id="i-6"> </div>
            </div>
        </div>
    </div>
    @php
        dump($datos_api);
        // exit;
        //------Cantidad_delitos------------------------
        $cantidad_delitos = $datos_api['cantidad_delitos'];
        $series = array_map(function($item) {
            return [
                'name' => $item['delito'],
                'data' => [$item['cantidad']]
            ];
        }, $cantidad_delitos);
        $delitos = json_encode($series);

        //--------Rangos de edad----------------------
            $rangos_edad = $datos_api['rangos_edad'];
            $new_data = [];

            foreach ($rangos_edad as $rango) {
                // Construir un nuevo objeto con el formato deseado
                $new_data[] = [
                    'name' => "{$rango['rango_edad']} años",
                    'y' => $rango['cantidad']
                ];
            }

            $result = [
                [
                    'name' => 'Cantidad',
                    'data' => $new_data
                ]
            ];
            $rangos_edad = json_encode($result, JSON_UNESCAPED_UNICODE);

    @endphp
    <script>


        document.addEventListener('DOMContentLoaded', function () {
            var estado_legal = {!! json_encode($datos_api['estado_legal'], JSON_UNESCAPED_UNICODE) !!};

            var categories = estado_legal.map(function(item) {
                return item.departamento;
            });

            var sentenciadosData = estado_legal.map(function(item) {
                return item.sentenciados;
            });

            var preventivosData = estado_legal.map(function(item) {
                return item.preventivos;
            });

            Highcharts.chart('i-1', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: categories,
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true
                        }
                    }
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
                    name: 'Sentenciados',
                    data: sentenciadosData
                }, {
                    name: 'Preventivos',
                    data: preventivosData
                }]
            });
        });


        document.addEventListener('DOMContentLoaded', function () {
            var genero_departamento = {!! json_encode($datos_api['genero_departamento'], JSON_UNESCAPED_UNICODE) !!};

            var categories = genero_departamento.map(function(item) {
                return item.departamento;
            });

            var femeninoData = genero_departamento.map(function(item) {
                return item.femenino;
            });

            var masculinoData = genero_departamento.map(function(item) {
                return item.masculino;
            });

            Highcharts.chart('i-2', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: ''
                },
                colors: [
                        '#060A80',
                        '#FF3549',
                    ],

                xAxis: {
                    categories: categories,
                    title: { text: null }
                },
                yAxis: {
                    min: 0,
                    labels: {
                        overflow: 'justify'
                    }
                },
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
                    name: 'Hombres',
                    data: masculinoData
                }, {
                    name: 'Mujeres',
                    data: femeninoData
                },

                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var cantidad_delitos = {!! $delitos !!};

            Highcharts.chart('i-3', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                yAxis: {
                    title: {
                        useHTML: true,
                        text: 'Clasificación por delito'
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
                series: cantidad_delitos,
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var rangos = {!! $rangos_edad !!};
            Highcharts.chart('i-4', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },

                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: {point.percentage:.1f}%'
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
                series: rangos,
            });

        });





        Highcharts.chart('i-5', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '',
                align: 'center',
                verticalAlign: 'middle',
                y: 60
            },
            // colors: [
            //     '#ff6eb4',
            //     '#008b8b',
            //     '#000'
            // ],
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
                    ['Beni', 1],
                    ['Chuquisaca', 2],
                    ['Cochabamba', 3],
                    ['La Paz', 1],
                    ['Oruro', 2],
                    ['Pando', 3],
                    ['Potosí', 1],
                    ['Santa Cruz', 3],
                    ['Tarija', 2]
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

        Highcharts.chart('i-6', {
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: ''
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
                name: 'Beni',
                data: [3]

            }, {
                name: 'Chuquisaca',
                data: [2]

            }, {
                name: 'Cochabamba',
                data: [4]

            }, {
                name: 'La Paz',
                data: [2]

            }, {
                name: 'Oruro',
                data: [3]

            }, {
                name: 'Potosí',
                data: [2]

            },
            {
                name: 'Pando',
                data: [2]

            },
            {
                name: 'Santa Cruz',
                data: [6]

            },
            {
                name: 'Tarija',
                data: [2]

            },

        ],
        });

    </script>

@endsection

