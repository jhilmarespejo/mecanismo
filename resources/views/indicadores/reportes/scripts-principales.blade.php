{{-- 
    Archivo: resources/views/indicadores/reportes/scripts-principales.blade.php
    Descripción: Scripts JavaScript para manejo de gráficos y lógica de reportes
--}}

<script>
/**
 * Variables globales para el manejo de gráficos
 */
let chartIndicador = null;
let chartParametro = null;
let chartCentros = null;
let chartNumeral = null;
let chartDelitos = null;
let chartDepartamentos = null;
let chartSexo = null;

/**
 * Configuración de colores para diferentes tipos de gráficos
 */
const CHART_COLORS = {
    primary: '#2f7ed8',
    success: '#198754',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#0dcaf0',
    secondary: '#6c757d',
    palette: [
        '#2f7ed8', '#0d6efd', '#6610f2', '#6f42c1', '#d63384', 
        '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997',
        '#0dcaf0', '#0d6efd', '#6f42c1', '#d63384', '#dc3545',
        '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'
    ]
};

/**
 * Configuración base para gráficos Highcharts
 */
const BASE_CHART_CONFIG = {
    credits: { enabled: false },
    exporting: { enabled: true },
    tooltip: { 
        backgroundColor: 'rgba(255, 255, 255, 0.95)',
        borderColor: '#cccccc',
        borderRadius: 8,
        shadow: true
    },
    title: {
        style: { fontSize: '16px', fontWeight: 'bold' }
    },
    subtitle: {
        style: { fontSize: '12px', color: '#666666' }
    }
};

/**
 * Oculta todos los gráficos de parámetros
 */
function ocultarTodosLosGraficosParametros() {
    $('#graficoParametros').hide();
    $('#graficoListaCentros').hide();
    $('#graficoNumerales').hide();
    $('#graficoDelitos').hide();
    $('#graficoDepartamentos').hide();
    $('#graficoSexo').hide();
    $('#noDataMessage').hide();
    $('#loadingParametros').removeClass('show');
}

/**
 * Actualiza el gráfico de indicadores (Si/No por año)
 */
function actualizarGraficoIndicador(indicadorPorAnio, nombreIndicador) {
    if (!indicadorPorAnio || indicadorPorAnio.length === 0) {
        console.warn('No hay datos para el indicador');
        return;
    }

    const categorias = indicadorPorAnio.map(item => item.gestion);
    const resultados = indicadorPorAnio.map(item => parseFloat(item.resultado_final));
    
    // Destruir gráfico anterior si existe
    if (chartIndicador) {
        chartIndicador.destroy();
    }
    
    chartIndicador = Highcharts.chart('graficoIndicador', {
        ...BASE_CHART_CONFIG,
        chart: { type: 'column' },
        title: { text: nombreIndicador },
        xAxis: { 
            categories: categorias,
            title: { text: 'Gestión' }
        },
        yAxis: {
            min: 0, max: 100,
            title: { text: 'Porcentaje (%)' },
            labels: { format: '{value}%' }
        },
        tooltip: {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y:.1f}%</b><br/>'
        },
        plotOptions: {
            column: {
                dataLabels: { enabled: true, format: '{point.y:.1f}%' },
                color: CHART_COLORS.primary
            }
        },
        series: [{
            name: 'Porcentaje de Cumplimiento',
            data: resultados
        }]
    });
    
    $('#tituloIndicador').text(nombreIndicador);
    $('#graficoIndicadores').show();
}

/**
 * Actualiza el gráfico de parámetros Si/No
 */
function actualizarGraficoParametroSiNo(parametroPorAnio, nombreParametro) {
    const categorias = [];
    const resultados = [];
    
    Object.keys(parametroPorAnio).sort().forEach(year => {
        const item = parametroPorAnio[year];
        categorias.push(parseInt(year));

        let respuesta = item.HIN_respuesta;
        let valor, color;
        
        if (respuesta === "Si") {
            valor = 1;
            color = CHART_COLORS.success;
        } else if (respuesta === "No") {
            valor = -1;
            color = CHART_COLORS.warning;
        } else {
            valor = 0;
            color = CHART_COLORS.danger;
        }

        resultados.push({ y: valor, color: color });
    });

    if (chartParametro) {
        chartParametro.destroy();
    }

    chartParametro = Highcharts.chart('graficoParametro', {
        ...BASE_CHART_CONFIG,
        chart: { type: 'column' },
        title: { text: nombreParametro },
        xAxis: { categories: categorias },
        yAxis: {
            min: -1, max: 1,
            title: { text: '' },
            labels: {
                formatter: function () {
                    return this.value === 1 ? 'Si' : this.value === -1 ? 'No' : 'No reportado';
                },
                style: { fontSize: '14px', fontWeight: 'bold' }
            },
            tickPositions: [-1, 0, 1],
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
                    style: { fontSize: '14px', fontWeight: 'bold' }
                },
                pointPadding: 0.2,
                groupPadding: 0.1
            }
        },
        series: [{
            name: 'Resultados por parámetro y gestión',
            data: resultados
        }]
    });

    $('#graficoParametros').show();
    ocultarGraficosEspecificos(['centros', 'numerales', 'delitos', 'departamentos', 'sexo']);
}

/**
 * Actualiza el gráfico de datos numéricos
 */
function actualizarGraficoNumeral(numeralPorAnio, nombreParametro) {
    const categorias = numeralPorAnio.map(item => item.year);
    const datos = numeralPorAnio.map(item => {
        if (item.hasData && item.value !== null) {
            return {
                y: item.value,
                color: item.value === 0 ? CHART_COLORS.warning : CHART_COLORS.primary
            };
        } else {
            return {
                y: null,
                color: CHART_COLORS.danger,
                marker: { symbol: 'diamond' }
            };
        }
    });

    if (chartNumeral) {
        chartNumeral.destroy();
    }

    chartNumeral = Highcharts.chart('graficoNumeral', {
        ...BASE_CHART_CONFIG,
        chart: { type: 'line' },
        title: { text: nombreParametro },
        xAxis: { 
            categories: categorias,
            title: { text: 'Gestión' }
        },
        yAxis: {
            title: { text: 'Valor' },
            min: 0
        },
        tooltip: {
            formatter: function() {
                if (this.y === null) {
                    return `<b>Gestión ${this.x}</b><br>Sin datos reportados`;
                }
                return `<b>Gestión ${this.x}</b><br>Valor: ${this.y}`;
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return this.y === null ? '⚠️' : this.y;
                    }
                },
                marker: { enabled: true, radius: 6 }
            }
        },
        series: [{
            name: 'Valores Numéricos',
            data: datos,
            connectNulls: false
        }]
    });

    $('#tituloNumerales').text(nombreParametro);
    $('#graficoNumerales').show();
    ocultarGraficosEspecificos(['parametros', 'centros', 'delitos', 'departamentos', 'sexo']);
}

/**
 * Actualiza el gráfico de delitos por año
 */
function actualizarGraficoDelitos(delitosPorAnio, nombreParametro) {
    let allData = {};
    let delitos = new Set();
    let years = delitosPorAnio.map(item => item.HIN_gestion);
    
    // Mapeo de claves a nombres legibles
    const delitosNames = {
        'violencia_familiar': 'Violencia Familiar',
        'robo_sin_violencia': 'Robo sin Violencia',
        'estafa_fraude': 'Estafa/Fraude',
        'ciberdelitos': 'Ciberdelitos',
        'robo_con_violencia': 'Robo con Violencia',
        'hurto': 'Hurto',
        'robo_autopartes': 'Robo de Autopartes'
    };

    // Procesar datos
    delitosPorAnio.forEach(item => {
        if (item.HIN_respuesta) {
            try {
                let respuestas = JSON.parse(item.HIN_respuesta);
                Object.entries(respuestas).forEach(([delito, cantidad]) => {
                    const nombreDelito = delitosNames[delito] || delito;
                    delitos.add(nombreDelito);
                    if (!allData[nombreDelito]) {
                        allData[nombreDelito] = {};
                    }
                    allData[nombreDelito][item.HIN_gestion] = parseInt(cantidad) || 0;
                });
            } catch (e) {
                console.error('Error parsing JSON delitos:', e);
            }
        }
    });

    // Crear series para el gráfico
    const series = [];
    let colorIndex = 0;
    delitos.forEach(delito => {
        const data = years.map(year => allData[delito]?.[year] ?? null);
        series.push({
            name: delito,
            data: data,
            color: CHART_COLORS.palette[colorIndex % CHART_COLORS.palette.length]
        });
        colorIndex++;
    });

    if (chartDelitos) {
        chartDelitos.destroy();
    }

    chartDelitos = Highcharts.chart('graficoDelito', {
        ...BASE_CHART_CONFIG,
        chart: { type: 'column' },
        title: { text: nombreParametro },
        xAxis: { 
            categories: years,
            title: { text: 'Gestión' }
        },
        yAxis: {
            title: { text: 'Número de Casos' },
            min: 0
        },
        tooltip: {
            formatter: function() {
                if (this.y === null) {
                    return `<b>${this.series.name}</b><br>Gestión ${this.x}: Sin datos`;
                }
                return `<b>${this.series.name}</b><br>Gestión ${this.x}: ${this.y} casos`;
            }
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return this.y === null ? '⚠️' : this.y;
                    }
                }
            }
        },
        series: series
    });

    $('#tituloDelitos').text(nombreParametro);
    $('#graficoDelitos').show();
    ocultarGraficosEspecificos(['parametros', 'centros', 'numerales', 'departamentos', 'sexo']);
}

/**
 * Actualiza el gráfico de departamentos por año
 */
function actualizarGraficoDepartamentos(departamentosPorAnio, nombreParametro) {
    let allData = {};
    let departamentos = new Set();
    let years = departamentosPorAnio.map(item => item.HIN_gestion);
    
    // Mapeo de claves a nombres legibles
    const departamentosNames = {
        'la_paz': 'La Paz',
        'santa_cruz': 'Santa Cruz',
        'cochabamba': 'Cochabamba',
        'oruro': 'Oruro',
        'potosi': 'Potosí',
        'chuquisaca': 'Chuquisaca',
        'tarija': 'Tarija',
        'beni': 'Beni',
        'pando': 'Pando'
    };

    // Procesar datos
    departamentosPorAnio.forEach(item => {
        if (item.HIN_respuesta) {
            try {
                let respuestas = JSON.parse(item.HIN_respuesta);
                Object.entries(respuestas).forEach(([depto, cantidad]) => {
                    const nombreDepto = departamentosNames[depto] || depto;
                    departamentos.add(nombreDepto);
                    if (!allData[nombreDepto]) {
                        allData[nombreDepto] = {};
                    }
                    allData[nombreDepto][item.HIN_gestion] = parseInt(cantidad) || 0;
                });
            } catch (e) {
                console.error('Error parsing JSON departamentos:', e);
            }
        }
    });

    // Crear series para el gráfico
    const series = [];
    let colorIndex = 0;
    departamentos.forEach(depto => {
        const data = years.map(year => allData[depto]?.[year] ?? null);
        series.push({
            name: depto,
            data: data,
            color: CHART_COLORS.palette[colorIndex % CHART_COLORS.palette.length]
        });
        colorIndex++;
    });

    if (chartDepartamentos) {
        chartDepartamentos.destroy();
    }

    chartDepartamentos = Highcharts.chart('graficoDepartamento', {
        ...BASE_CHART_CONFIG,
        chart: { type: 'column' },
        title: { text: nombreParametro },
        xAxis: { 
            categories: years,
            title: { text: 'Gestión' }
        },
        yAxis: {
            title: { text: 'Cantidad' },
            min: 0
        },
        tooltip: {
            formatter: function() {
                if (this.y === null) {
                    return `<b>${this.series.name}</b><br>Gestión ${this.x}: Sin datos`;
                }
                return `<b>${this.series.name}</b><br>Gestión ${this.x}: ${this.y}`;
            }
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return this.y === null ? '⚠️' : this.y;
                    }
                }
            }
        },
        series: series
    });

    $('#tituloDepartamentos').text(nombreParametro);
    $('#graficoDepartamentos').show();
    ocultarGraficosEspecificos(['parametros', 'centros', 'numerales', 'delitos', 'sexo']);
}

/**
 * Actualiza el gráfico de sexo por año
 */
function actualizarGraficoSexo(sexoPorAnio, nombreParametro) {
    let allData = {};
    let years = sexoPorAnio.map(item => item.HIN_gestion);
    
    const sexoNames = {
        'femenino': 'Femenino',
        'masculino': 'Masculino'
    };

    // Procesar datos
    sexoPorAnio.forEach(item => {
        if (item.HIN_respuesta) {
            try {
                let respuestas = JSON.parse(item.HIN_respuesta);
                Object.entries(respuestas).forEach(([sexo, cantidad]) => {
                    const nombreSexo = sexoNames[sexo] || sexo;
                    if (!allData[nombreSexo]) {
                        allData[nombreSexo] = {};
                    }
                    allData[nombreSexo][item.HIN_gestion] = parseInt(cantidad) || 0;
                });
            } catch (e) {
                console.error('Error parsing JSON sexo:', e);
            }
        }
    });

    // Crear series
    const series = [
        {
            name: 'Femenino',
            data: years.map(year => allData['Femenino']?.[year] ?? null),
            color: '#d63384'
        },
        {
            name: 'Masculino',
            data: years.map(year => allData['Masculino']?.[year] ?? null),
            color: '#0d6efd'
        }
    ];

    if (chartSexo) {
        chartSexo.destroy();
    }

    chartSexo = Highcharts.chart('graficoSex', {
        ...BASE_CHART_CONFIG,
        chart: { type: 'column' },
        title: { text: nombreParametro },
        xAxis: { 
            categories: years,
            title: { text: 'Gestión' }
        },
        yAxis: {
            title: { text: 'Cantidad' },
            min: 0
        },
        tooltip: {
            formatter: function() {
                if (this.y === null) {
                    return `<b>${this.series.name}</b><br>Gestión ${this.x}: Sin datos`;
                }
                return `<b>${this.series.name}</b><br>Gestión ${this.x}: ${this.y} personas`;
            }
        },
        plotOptions: {
            column: {
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        return this.y === null ? '⚠️' : this.y;
                    }
                }
            }
        },
        series: series
    });

    $('#tituloSexo').text(nombreParametro);
    $('#graficoSexo').show();
    ocultarGraficosEspecificos(['parametros', 'centros', 'numerales', 'delitos', 'departamentos']);
}

/**
 * Función existente para centros penitenciarios (mantener como está)
 */
/**
 * Actualiza el gráfico de centros penitenciarios por año (FUNCIÓN COMPLETA)
 * Incluye controles de animación y carrera de barras
 */
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
                console.error('Error parsing JSON centros:', e);
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
        colorMap[centro] = CHART_COLORS.palette[colorIndex % CHART_COLORS.palette.length];
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

    // Crear nuevos controles de animación
    const controls = document.createElement('div');
    controls.className = 'controls mb-3';
    controls.innerHTML = `
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
            <button id="play-pause" class="btn btn-primary">
                <i class="bi bi-play-fill"></i> Iniciar Animación
            </button>
            <div class="d-flex align-items-center">
                <label for="year-range" class="form-label me-2 mb-0">Año:</label>
                <input type="range" min="0" max="${years.length - 1}" value="0" 
                       id="year-range" class="form-range" style="width: 200px">
            </div>
            <span id="year-label" class="badge bg-secondary fs-6">${years[0]}</span>
            <button id="reset-animation" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-counterclockwise"></i> Reiniciar
            </button>
        </div>
    `;
    
    // Insertar los controles al inicio del contenedor del gráfico
    graficoListaCentrosDiv.insertBefore(controls, graficoListaCentrosDiv.firstChild);

    // Destruir gráfico anterior si existe
    if (chartCentros) {
        chartCentros.destroy();
    }

    // Configuración del gráfico
    chartCentros = Highcharts.chart('graficoListaCentro', {
        ...BASE_CHART_CONFIG,
        chart: {
            type: 'bar',
            animation: {
                duration: 500
            },
            height: Math.min(chartHeight, 600)
        },
        title: {
            text: nombreParametro,
            align: 'center',
            style: {
                fontSize: '18px',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Gestión: <b>' + years[0] + '</b>',
            align: 'center',
            style: {
                fontSize: '16px'
            },
            useHTML: true
        },
        xAxis: {
            type: 'category',
            labels: {
                style: {
                    fontSize: '11px'
                }
            },
            title: {
                text: 'Centros Penitenciarios'
            }
        },
        yAxis: {
            title: {
                text: 'Población'
            },
            min: 0
        },
        series: [{
            name: 'Población',
            data: [],
            dataLabels: {
                enabled: true,
                formatter: function() {
                    if (this.y === null || this.y === undefined) {
                        return '<span style="color: red;">⚠️</span>';
                    }
                    return '<b>' + this.y + '</b>';
                },
                style: {
                    fontSize: '11px',
                    fontWeight: 'bold'
                }
            }
        }],
        plotOptions: {
            bar: {
                animation: {
                    duration: 800,
                    easing: 'easeOutBounce'
                },
                groupPadding: 0,
                pointPadding: cantidadCentros > 15 ? 0.05 : 0.1,
                borderWidth: 0,
                colorByPoint: true,
                dataSorting: {
                    enabled: true,
                    matchByName: true
                }
            }
        },
        colors: CHART_COLORS.palette,
        tooltip: {
            formatter: function() {
                if (this.y === null || this.y === undefined) {
                    return `<b>${this.point.name}</b><br><span style="color: red;">Sin datos reportados</span>`;
                }
                return `<b>${this.point.name}</b><br>Población: <b>${this.y}</b> personas`;
            }
        },
        legend: {
            enabled: false
        }
    });

    // Variables para la animación
    let currentYear = 0;
    const yearTotal = years.length;
    let playing = false;
    let animationTimer = null;

    // Función para actualizar los datos del gráfico
    function updateData(yearIndex) {
        const year = years[yearIndex];
        const yearData = chartData
            .filter(row => row.year === year)
            .map(row => ({
                name: row.name,
                y: row.y,
                color: row.y === null ? '#ff9999' : colorMap[row.name]
            }))
            .sort((a, b) => (b.y || 0) - (a.y || 0)); // Ordenar por valor descendente

        chartCentros.series[0].setData(yearData, true, { duration: 800 });
        chartCentros.setTitle(null, { 
            text: 'Gestión: <b>' + year + '</b>',
            align: 'center',
            style: {
                fontSize: '16px'
            },
            useHTML: true
        });
    }
    
    // Event listeners para los controles
    document.getElementById('play-pause').addEventListener('click', function() {
        if (playing) {
            // Pausar
            playing = false;
            if (animationTimer) {
                clearTimeout(animationTimer);
                animationTimer = null;
            }
            this.innerHTML = '<i class="bi bi-play-fill"></i> Continuar';
        } else {
            // Reproducir
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
        
        // Si está reproduciéndose, pausar
        if (playing) {
            playing = false;
            if (animationTimer) {
                clearTimeout(animationTimer);
                animationTimer = null;
            }
            document.getElementById('play-pause').innerHTML = '<i class="bi bi-play-fill"></i> Continuar';
        }
    });

    document.getElementById('reset-animation').addEventListener('click', function() {
        // Reiniciar la animación
        playing = false;
        if (animationTimer) {
            clearTimeout(animationTimer);
            animationTimer = null;
        }
        currentYear = 0;
        document.getElementById('year-range').value = currentYear;
        document.getElementById('year-label').textContent = years[currentYear];
        document.getElementById('play-pause').innerHTML = '<i class="bi bi-play-fill"></i> Iniciar Animación';
        updateData(currentYear);
    });

    // Función para reproducir la animación automática
    function play() {
        if (!playing) return;

        // Si ya terminó la animación, reiniciar desde el primer año
        if (currentYear >= yearTotal - 1) {
            currentYear = 0;
            document.getElementById('year-range').value = currentYear;
            document.getElementById('year-label').textContent = years[currentYear];
            updateData(currentYear);
            playing = false;
            document.getElementById('play-pause').innerHTML = '<i class="bi bi-play-fill"></i> Iniciar Animación';
            return;
        }

        currentYear++;
        document.getElementById('year-range').value = currentYear;
        document.getElementById('year-label').textContent = years[currentYear];
        updateData(currentYear);
        
        // Continuar la animación
        animationTimer = setTimeout(play, 2000); // 2 segundos por frame
    }
    
    // Inicializar con el primer año
    updateData(0);
    
    // Mostrar el gráfico y ocultar otros
    $('#graficoListaCentros').show();
    ocultarGraficosEspecificos(['parametros', 'numerales', 'delitos', 'departamentos', 'sexo']);

    // Enfocar el gráfico
    setTimeout(() => {
        graficoListaCentrosDiv.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center'
        });
    }, 300);

    // Actualizar título
    $('#tituloListaCentros').text(nombreParametro);
}

/**
 * Oculta gráficos específicos
 */
function ocultarGraficosEspecificos(tipos) {
    const graficos = {
        'parametros': '#graficoParametros',
        'centros': '#graficoListaCentros',
        'numerales': '#graficoNumerales',
        'delitos': '#graficoDelitos',
        'departamentos': '#graficoDepartamentos',
        'sexo': '#graficoSexo'
    };
    
    tipos.forEach(tipo => {
        if (graficos[tipo]) {
            $(graficos[tipo]).hide();
        }
    });
}

/**
 * Lógica principal de los comboboxes
 */
$(document).ready(function () {
    // Cuando se selecciona una categoría
    $('#categoria').change(function () {
        let categoria = $(this).val();
        if (categoria) {
            $('#indicador').prop('disabled', false);
            $('#loadingIndicadores').addClass('show');
            
            $.ajax({
                url: "{{ route('indicadores.reportes') }}",
                type: "GET",
                data: { categoria_id: categoria },
                success: function (response) {
                    $('#indicador').empty().append('<option value="">-- Seleccione un indicador --</option>');
                    response.forEach(function (indicador) {
                        $('#indicador').append('<option value="' + indicador.IND_indicador + '">' + indicador.IND_numero +'. '+ indicador.IND_indicador + '</option>');
                    });
                    $('#loadingIndicadores').removeClass('show');
                },
                error: function() {
                    $('#loadingIndicadores').removeClass('show');
                }
            });
        } else {
            $('#indicador').prop('disabled', true).empty().append('<option value="">-- Seleccione un indicador --</option>');
            $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione un parámetro --</option>');
            $('#graficoIndicadores').hide();
            ocultarTodosLosGraficosParametros();
        }
    });

    // Cuando se selecciona un indicador
    $('#indicador').change(function () {
        let indicadorId = $(this).val();
        let nombreIndicador = $('#indicador option:selected').text();
        
        if (indicadorId) {
            $('#parametro').prop('disabled', false);
            $('#loadingIndicadores').addClass('show');
            
            $.ajax({
                url: "{{ route('indicadores.reportes') }}",
                type: "GET",
                data: { indicador_indicador: indicadorId },
                success: function (response) {
                    $('#parametro').empty().append('<option value="">-- Seleccione un parámetro --</option>');
                    
                    if (response.parametros) {
                        response.parametros.forEach(function(parametro) {
                            $('#parametro').append(
                                '<option value="' + parametro.IND_id + '" data-tipo="' + parametro.IND_tipo_repuesta + '">' + parametro.IND_parametro + '</option>'
                            );
                        });
                    }
                    
                    // Actualizar gráfico de indicadores
                    if (response.indicadorPorAnio && response.indicadorPorAnio.length > 0) {
                        actualizarGraficoIndicador(response.indicadorPorAnio, nombreIndicador);
                        document.getElementById('graficoIndicador').scrollIntoView({ behavior: 'smooth' });
                    }
                    
                    $('#loadingIndicadores').removeClass('show');
                },
                error: function() {
                    $('#loadingIndicadores').removeClass('show');
                }
            });
        } else {
            $('#parametro').prop('disabled', true).empty().append('<option value="">-- Seleccione un parámetro --</option>');
            $('#graficoIndicadores').hide();
            ocultarTodosLosGraficosParametros();
        }
    });
    
    // Cuando se selecciona un parámetro
    $('#parametro').change(function () {
        let parametroId = $(this).val();
        let nombreParametro = $('#parametro option:selected').text();
        let tipoParametro = $('#parametro option:selected').data('tipo');
        
        if (parametroId) {
            // Mostrar información del tipo de parámetro
            $('#tipo-parametro-texto').text(tipoParametro);
            
            $('#loadingParametros').addClass('show');
            ocultarTodosLosGraficosParametros();
            
            $.ajax({
                url: "{{ route('indicadores.reportes') }}",
                type: "GET",
                data: { parametro_id: parametroId },
                success: function (response) {
                    $('#loadingParametros').removeClass('show');
                    
                    // Determinar qué tipo de gráfico mostrar
                    if (response.parametroPorAnioSiNo) {
                        actualizarGraficoParametroSiNo(response.parametroPorAnioSiNo, nombreParametro);
                    } else if (response.listaCentrosPorAnio) {
                        actualizarGraficoCentrosPorAnio(response.listaCentrosPorAnio, nombreParametro);
                    } else if (response.numeralPorAnio) {
                        actualizarGraficoNumeral(response.numeralPorAnio, nombreParametro);
                    } else if (response.delitosPorAnio) {
                        actualizarGraficoDelitos(response.delitosPorAnio, nombreParametro);
                    } else if (response.departamentosPorAnio) {
                        actualizarGraficoDepartamentos(response.departamentosPorAnio, nombreParametro);
                    } else if (response.sexoPorAnio) {
                        actualizarGraficoSexo(response.sexoPorAnio, nombreParametro);
                    } else {
                        $('#noDataMessage').show();
                    }
                },
                error: function (xhr, status, error) {
                    $('#loadingParametros').removeClass('show');
                    console.error("Error al obtener resultados:", error);
                    $('#noDataMessage').show();
                }
            });
        } else {
            ocultarTodosLosGraficosParametros();
        }
    });
});
</script>