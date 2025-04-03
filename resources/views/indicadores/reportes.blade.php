@extends('layouts.app')
@section('title', 'Reportes - indicadores')

@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/draggable-points.js"></script>

<style>
  
    /* @media (max-width: 768px) {
       .custom-select option {
           max-width: 300px;
       }
       
       .custom-select {
           padding: 0.625rem 0.875rem;
       }
       
       .select-card {
           padding: 1rem;
       }
    } */
</style>

<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)
    <h1 class="text-center text-primary">Reportes</h1>
    <div class="row g-4">
        <!-- Categorías -->
        <div class="col-12 col-md-4">
            <div class="select-card card text-dark bg-light mb-1">
                <div class="card-header fs-5 text-center">
                    Categoría:
                </div>
                <div class="card-body bg-white">
                    <select class="form-select custom-select" id="categoria">
                        <option value="">-- Seleccione una categoría --</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria }}" title="{{ $categoria }}">{{ $categoria }}</option>
                        @endforeach
                    </select>
                </div>
                
                
            </div>
        </div>
        <!-- Indicadores -->
        <div class="col-12 col-md-4">
            <div class="select-card card text-dark bg-light mb-3">
                <div class="card-header fs-5 text-center">
                    Indicador:
                </div>
                <div class="card-body bg-white">
                    <select class="form-select custom-select" id="indicador" disabled>
                        <option value="">-- Seleccione un indicador --</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Parámetros -->
        <div class="col-12 col-md-4">
            <div class="select-card card text-dark bg-light mb-3">
                <div class="card-header fs-5 text-center">
                    Parámetros:
                </div>
                <div class="card-body bg-white">
                    <select class="form-select custom-select" id="parametro" disabled>
                        <option value="">-- Seleccione un parámetro --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Para que los graficos tengan la misma altura */
        .container.row {
                display: flex;
                align-items: stretch;
            }

            .indicadores, .parametros {
                display: flex;
                flex-direction: column;
            }
            
            .card {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }

            .grafico-container {
                flex-grow: 1;
            }
            .controls {
                text-align: center;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 5px;
                margin: 10px 0;
            }

            .controls button {
                margin: 0 10px;
            }
            
            .controls input[type="range"] {
                vertical-align: middle;
                margin: 0 10px;
            }
      </style>
      
    <div class="container row g-4 mt-1"  >
        <!-- Gráfico Estadístico del INDICADOR seleccionado -->
        <div class="col-12 col-md-6 indicadores"> <!-- Ocupa 8 columnas en pantallas medianas y más grandes, 12 en móviles -->
            <div id="graficoIndicadores" style="display: none;" class="card">
                <h3 id="tituloIndicador" class="mb-4"></h3>
                <div id="graficoIndicador" class="grafico-container"  ></div>
            </div>
        </div>
    
        <!-- Gráfico Estadístico del PARAMETRO seleccionado -->
        <div class="col-12 col-md-6 parametros"> <!-- Ocupa 4 columnas en pantallas medianas y más grandes, 12 en móviles -->
            <div id="graficoParametros" style="display: none;" class="card">
                <h3 id="tituloParametro" class="mb-4"></h3>
                <!-- Asegúrate de que el div interno tenga un id único -->
                <div id="graficoParametro" class="grafico-container"  ></div>
            </div>
        </div>
        <!-- Gráfico Estadístico del PARAMETRO seleccionado cuando este corresponde a una lista de centros penitenciarios -->
        <div class="col-12 col-md-12 listaCentros"> <!-- Ocupa 4 columnas en pantallas medianas y más grandes, 12 en móviles -->
            <div id="graficoListaCentros" style="display: none;" class="card">
                <h3 id="tituloListaCentros" class="mb-1"></h3>
                <!-- Asegúrate de que el div interno tenga un id único -->
                <div id="graficoListaCentro" class="grafico-container"  ></div>
            </div>
        </div>
    </div>
</div>
<!-- Script para manejar la lógica de los combobox -->
<script>
    let chart = null;
    
    function actualizarGraficoCentrosPorAnio(data, nombreParametro) {
    // Procesamos los datos para el formato requerido
    let allData = {};
    let centros = new Set();
    let years = data.map(item => item.HIN_gestion);
    
    // Extraer años y datos
    data.forEach(item => {
        if (item.HIN_respuesta) {
            try {
                let respuestas = JSON.parse(item.HIN_respuesta);
                Object.entries(respuestas).forEach(([centro, poblacion]) => {
                    centros.add(centro);
                    if (!allData[centro]) {
                        allData[centro] = {};
                    }
                    allData[centro][item.HIN_gestion] = parseInt(poblacion) || null;
                });
            } catch (e) {
                console.error('Error parsing JSON:', e);
            }
        }
    });

    // Obtener la cantidad de centros únicos y ajustar el contenedor
    const cantidadCentros = centros.size;
    const graficoListaCentrosDiv = document.getElementById('graficoListaCentros');
    const parentContainer = graficoListaCentrosDiv.parentElement;
    
    // Remover clases previas de columnas
    parentContainer.classList.remove('col-12', 'col-md-6', 'col-md-12');
    
    // Ajustar el ancho según la cantidad de centros
    if (cantidadCentros <= 4) {
        parentContainer.classList.add('col-12', 'col-md-6');
    } else {
        parentContainer.classList.add('col-12', 'col-md-12');
    }

    // Ajustar la altura del gráfico según la cantidad de centros
    const chartHeight = Math.max(500, cantidadCentros * 40);

    // Convertir datos al formato necesario para la carrera de barras
    let chartData = [];
    let colorMap = {};
    let colorIndex = 0;
    centros.forEach(centro => {
        // Asignar un color fijo a cada centro
        colorMap[centro] = Highcharts.getOptions().colors[colorIndex % 20];
        colorIndex++;
        years.forEach(year => {
            chartData.push({
                name: centro,
                y: allData[centro]?.[year] ?? null,
                year: year
            });
        });
    });

    // Eliminar controles existentes si los hay
    const existingControls = graficoListaCentrosDiv.querySelector('.controls');
    if (existingControls) {
        existingControls.remove();
    }

    // Crear nuevos controles
    const controls = document.createElement('div');
    controls.className = 'controls mb-1';
    controls.innerHTML = `
        <button id="play-pause" class="btn btn-primary">
            <i class="bi bi-play-fill"></i> Iniciar
        </button>
        <input type="range" min="0" max="${years.length - 1}" value="0" id="year-range" style="width: 200px">
        <span id="year-label">${years[0]}</span>
    `;
    
    // Insertamos los controles al inicio del contenedor del gráfico
    graficoListaCentrosDiv.insertBefore(controls, graficoListaCentrosDiv.firstChild);

    // Configuración del gráfico
    const chart = Highcharts.chart('graficoListaCentro', {
        chart: {
            type: 'column',
            animation: {
                duration: 500
            },
            height: 400
        },
        title: {
            text: nombreParametro,
            align: 'center'
        },
        subtitle: {
            text: 'Gestión: <b>' + years[0] + '</b>',
            align: 'center',          // Centra el texto
            style: {
                fontSize: '18px'      // Tamaño de fuente más grande
            },
            useHTML: true            // Permite usar HTML en el texto
        },
        xAxis: {
            type: 'category',
            labels: {
                style: {
                    fontSize: '11px'
                }
            }
        },
        yAxis: {
            title: {
                text: 'Población'
            }
        },
        series: [{
            name: 'Población',
            data: [],
            dataLabels: {
                enabled: true,
                formatter: function() {
                    if (this.y === null || this.y === undefined) {
                        return '⚠️ Sin datos';
                    }
                    return this.y;
                }
            }
        }],
        plotOptions: {
            series: {
                animation: false,
                groupPadding: 0,
                pointPadding: cantidadCentros > 10 ? 0.05 : 0.1,
                borderWidth: 0,
                colorByPoint: true,
                dataSorting: {
                    enabled: true,
                    matchByName: true
                },
                type: 'bar'
            }
        },
        colors: [
            '#2f7ed8',   // Azul
            '#0d6efd',   // Azul primario
            '#6610f2',   // Indigo
            '#6f42c1',   // Púrpura
            '#d63384',   // Rosa
            '#dc3545',   // Rojo
            '#fd7e14',   // Naranja
            '#ffc107',   // Amarillo
            '#198754',   // Verde
            '#20c997',   // Verde azulado
            '#0dcaf0',   // Cyan
            '#0d6efd',   // Azul claro
            '#6f42c1',   // Violeta
            '#d63384',   // Rosa oscuro
            '#dc3545',   // Rojo brillante
            '#fd7e14',   // Naranja brillante
            '#ffc107',   // Amarillo brillante
            '#198754',   // Verde oscuro
            '#20c997',   // Turquesa
            '#0dcaf0'    // Celeste
        ],
        tooltip: {
            formatter: function() {
                if (this.y === null || this.y === undefined) {
                    return `<b>${this.point.name}</b><br>Sin datos reportados`;
                }
                return `<b>${this.point.name}</b><br>Población: ${this.y}`;
            }
        },
        legend: {
            enabled: false
        },
        credits: {
            enabled: false
        },
        exporting: {
            enabled: true
        }
    });

    // Variables para la animación
    let currentYear = 0;
    const yearTotal = years.length;
    let playing = false;

    // Función para actualizar los datos
    function updateData(year) {
        const yearData = chartData
            .filter(row => row.year === years[year])
            .map(row => ({
                name: row.name,
                y: row.y,
                color: row.y === null ? '#ff9999' : colorMap[row.name]
            }));

        chart.series[0].setData(yearData);
        chart.setTitle(null, { 
            text: 'Gestión: <b>' + years[year] + '</b>',
            align: 'center',
            style: {
                fontSize: '18px'
            },
            useHTML: true
        });
    }
    
    // Event listeners para los controles
    document.getElementById('play-pause').addEventListener('click', function() {
        if (playing) {
            playing = false;
            this.innerHTML = '<i class="bi bi-play-fill"></i> Reproducir';
        } else {
            // Si la animación terminó (estamos en el último año), reiniciar desde el principio
            if (currentYear >= yearTotal - 1) {
                currentYear = 0;
                document.getElementById('year-range').value = currentYear;
                document.getElementById('year-label').textContent = years[currentYear];
                updateData(currentYear);
            }
            playing = true;
            this.innerHTML = '<i class="bi bi-pause-fill"></i> Pausar';
            play();
        }
    });

    document.getElementById('year-range').addEventListener('input', function() {
        currentYear = parseInt(this.value, 10);
        updateData(currentYear);
        document.getElementById('year-label').textContent = years[currentYear];
    });

    // Función para reproducir la animación
    function play() {
        if (!playing) return;

        // Si ya terminó la animación, reiniciar desde el primer año
        if (currentYear >= yearTotal - 1) {
            currentYear = 0;
            document.getElementById('year-range').value = currentYear;
            document.getElementById('year-label').textContent = years[currentYear];
            updateData(currentYear);
            playing = false;
            document.getElementById('play-pause').innerHTML = '<i class="bi bi-play-fill"></i> Reproducir';
            return;
        }

        currentYear++;
        document.getElementById('year-range').value = currentYear;
        document.getElementById('year-label').textContent = years[currentYear];
        updateData(currentYear);
        setTimeout(play, 2000);
    }
    
    // Inicializar con el primer año
    updateData(0);
    $('#graficoIndicadores').hide();
    $('#graficoParametros').hide();
    $('#graficoListaCentros').show();

    // Enfocar el gráfico
    graficoListaCentrosDiv.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center'
    });
}

    function actualizarGraficoParametroSiNo(parametroPorAnio, nombreParametro) {
        const categorias = [];
        const resultados = [];
        
        // console.log(parametroPorAnio);
        Object.keys(parametroPorAnio).sort().forEach(year => {
            const item = parametroPorAnio[year];
            categorias.push(parseInt(year));

            // Verificamos exactamente los valores de HIN_respuesta
            let respuesta = item.HIN_respuesta;
            let valor, color;
            
            if (respuesta === "Si") {
                valor = 1;
                color = "green";
            } else if (respuesta === "No") {
                valor = -1;
                color = "orange";
            } else {
                // Casos donde la respuesta es null, undefined o diferente de "Si"/"No"
                valor = 0;
                color = "red";
            }

            resultados.push({ y: valor, color: color });

            console.log(`Año: ${year}, Respuesta: ${respuesta}, Valor: ${valor}, Color: ${color}`);
        });

        // Creamos el nuevo gráfico
        chart = Highcharts.chart('graficoParametros', {
            chart: {
                type: 'column'
            },
            title: {
                text: nombreParametro
            },
            xAxis: {
                categories: categorias
            },
            yAxis: {
                min: -1,
                max: 1,
                title: {
                    text: ''
                },
                labels: {
                    formatter: function () {
                        return this.value === 1 ? 'Si' : this.value === -1 ? 'No' : 'No reportado';
                    },
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                },
                tickPositions: [-1, 0, 1], // Mostrar etiquetas en -1, 0 y 1
                gridLineWidth: 1
            },
            tooltip: {
                formatter: function() {
                    return `<b>Gestión ${this.x}</b><br> Respuesta: ${
                        this.y === 1 ? 'Si' : this.y === -1 ? 'No' : 'No reportado'
                    }`;
                }
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return this.y === 1 ? 'Si' : this.y === -1 ? 'No' : '<span style="color: red;">Sin dato!</span>'; 
                        },
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    pointPadding: 0.2,
                    groupPadding: 0.1
                }
            },
            exporting: {
                csv: {
                    columnHeaderFormatter: function(item) {
                        if (item) {
                            return item.isXAxis ? 'Gestión' : nombreParametro;
                        }
                        return false;
                    }
                },
                filename: nombreParametro
            },
            series: [{
                name: 'Resultados por parámetro y gestión',
                data: resultados
            }],
            credits: {
                enabled: false
            }
        });

        $('#graficoParametros').show();
        $('#graficoListaCentros').hide();
    }
        
    function actualizarGraficoIndicador(indicadorPorAnio, nombreIndicador) {
        const categorias = [];
        const resultados = [];
        
        indicadorPorAnio.forEach(function(item) {
            categorias.push(item.gestion);
            resultados.push(parseFloat(item.resultado_final));
        });
        
        // Si ya existe un gráfico, lo destruimos
        if (chart) {
            chart.destroy();
        }
        
        // Creamos el nuevo gráfico
        chart = Highcharts.chart('graficoIndicador', {
            chart: {
                type: 'column'
            },
           
            title: {
                text: nombreIndicador
            },
            xAxis: {
                categories: categorias,
                // title: {
                //     text: 'Gestión'
                // }
            },
            yAxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Porcentaje (%)'
                },
                labels: {
                    format: '{value}%'
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y:.1f}%</b><br/>'
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}%'
                    }
                }
            },
            exporting: {
                csv: {
                    columnHeaderFormatter: function(item) {
                        if (item) {
                            if (item.isXAxis) {
                                return 'Gestión';
                            }
                            // Para la columna de datos
                            return nombreIndicador;
                        }
                        return false; // Usa el encabezado por defecto si no hay item
                    }
                },
                filename: nombreIndicador // El archivo se guardará con el nombre del indicador
            },
            series: [{
                name: 'Resultado por indicador y gestión',
                data: resultados,
                color: '#007bff'
            }],
            credits: {
                enabled: false
            }
        });
        // Actualizamos el título y mostramos el contenedor
        // $('#tituloIndicador').text(nombreIndicador);
        $('#graficoIndicadores').show();
        $('#graficoListaCentros').hide();
    }

    $(document).ready(function () {
        // Cuando se selecciona una categoría
        $('#categoria').change(function () {
            let categoria = $(this).val();
            if (categoria) {
                $('#indicador').prop('disabled', false);
                $.ajax({
                    url: "{{ route('indicadores.reportes') }}",
                    type: "GET",
                    data: { categoria_id: categoria },
                    success: function (response) {
                        $('#indicador').empty().append('<option value="">-- Seleccione --</option>');
                        response.forEach(function (indicador) {
                            $('#indicador').append('<option value="' + indicador.IND_indicador + '">' + indicador.IND_numero +'. '+ indicador.IND_indicador + '</option>');
                        });

                        // $("#graficoIndicadores").hide();
                        // $("#graficoParametros").hide();
                    }
                });
            } else {
                $('#indicador').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#graficoIndicadores').hide();
            }
        });

        // Cuando se selecciona un indicador
        $('#indicador').change(function () {
            let indicadorId = $(this).val();
            let nombreIndicador = $('#indicador option:selected').text();
            
            if (indicadorId) {
                $('#parametro').prop('disabled', false);
                $.ajax({
                    url: "{{ route('indicadores.reportes') }}",
                    type: "GET",
                    data: { indicador_indicador: indicadorId },
                    success: function (response) {
                        $('#parametro').empty().append('<option value="">-- Seleccione --</option>');
                        
                        if (response.parametros) {
                            response.parametros.forEach(function(parametro) {
                                $('#parametro').append(
                                    '<option value="' + parametro.IND_id + '">' + parametro.IND_parametro + '</option>'
                                );
                            });
                        }
                        
                        // Actualizamos el gráfico con los nuevos datos
                        if (response.indicadorPorAnio && response.indicadorPorAnio.length > 0) {
                            // console.log(response.indicadorPorAnio);
                            actualizarGraficoIndicador(response.indicadorPorAnio, nombreIndicador);
                            // Mover la pantalla para enfocar en el div graficoIndicador
                            document.getElementById('graficoIndicador').scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            } else {
                $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione --</option>');
                $('#graficoIndicadores').hide();
            }
        });
        
        // Mantenemos la funcionalidad existente del parámetro
        $('#parametro').change(function () {
            let parametroId = $(this).val();
            let nombreParametro = $('#parametro option:selected').text();
            
            if (parametroId) {
                $.ajax({
                    url: "{{ route('indicadores.reportes') }}",
                    type: "GET",
                    data: { 
                        parametro_id: parametroId 
                    },
                    success: function (response) {
                        if (response.parametroPorAnioSiNo) {
                            actualizarGraficoParametroSiNo(response.parametroPorAnioSiNo, nombreParametro);
                        }
                        if (response.listaCentrosPorAnio) {
                            // console.log('Lista Centros Penitenciarios');
                            // console.log(response.listaCentrosPorAnio);
                            actualizarGraficoCentrosPorAnio(response.listaCentrosPorAnio, nombreParametro);
                        }
                        
                    },
                    error: function (xhr, status, error) {
                        console.error("Error al obtener resultados:", error);
                    }
                });
            }
        });
    });
</script>
@endsection