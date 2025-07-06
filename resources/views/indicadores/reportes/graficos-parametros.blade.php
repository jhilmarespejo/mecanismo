{{-- 
    Archivo: resources/views/indicadores/reportes/graficos-parametros.blade.php
    Descripción: Contenedores para todos los tipos de gráficos de parámetros
--}}

{{-- Gráfico Parámetros Si/No (columna derecha) --}}
<div class="col-12 col-md-6 parametros">
    <div id="graficoParametros" style="display: none;" class="card chart-container">
        
        {{-- <div class="chart-subtitle">
            <i class="bi bi-info-circle me-2"></i>
           
        </div> --}}
        <div id="graficoParametro" class="grafico-container"></div>
        <div class="card-footer text-muted text-center">
            <i class="bi bi-info-circle me-2"></i>
            Resultados por parámetro y gestión
        </div>
    </div>

    {{-- Indicador de carga para parámetros --}}
    <div id="loadingParametros" class="loading-indicator">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2">Cargando datos del parámetro...</p>
    </div>
</div>

{{-- Gráfico Lista Centros Penitenciarios (ancho completo) --}}
<div class="col-12 listaCentros">
    <div id="graficoListaCentros" style="display: none;" class="card chart-container grafico-centros">
        
        <div id="graficoListaCentro" class="grafico-container"></div>
        <div class="card-footer text-muted text-center">
            <i class="bi bi-info-circle me-2"></i>
            Resultados por parámetro y gestión
        </div>
    </div>
</div>

{{-- Gráfico Numeral (ancho completo) --}}
<div class="col-12 numerales">
    <div id="graficoNumerales" style="display: none;" class="card chart-container grafico-numeral">
        
        {{-- <div class="chart-subtitle">
            <i class="bi bi-info-circle me-2"></i>
            Evolución de valores numéricos por gestión
        </div> --}}
        <div id="graficoNumeral" class="grafico-container"></div>
        <div class="card-footer text-muted text-center">
            <i class="bi bi-info-circle me-2"></i>
            Resultados por indicador y gestión
        </div>
    </div>
</div>

{{-- Gráfico Lista Delitos (ancho completo) --}}
<div class="col-12 delitos">
    <div id="graficoDelitos" style="display: none;" class="card chart-container grafico-delitos">
        
        <div id="graficoDelito" class="grafico-container"></div>
        <div class="card-footer text-muted text-center">
            <i class="bi bi-info-circle me-2"></i>
            Resultados por indicador y gestión
        </div>
    </div>
</div>

{{-- Gráfico Lista Departamentos (ancho completo) --}}
<div class="col-12 departamentos">
    <div id="graficoDepartamentos" style="display: none;" class="card chart-container grafico-departamentos">
       
        <div id="graficoDepartamento" class="grafico-container"></div>
        <div class="card-footer text-muted text-center">
            <i class="bi bi-info-circle me-2"></i>
            Resultados por indicador y gestión
        </div>
    </div>
</div>

{{-- Gráfico Lista Sexo (columna derecha cuando se muestra junto con indicadores) --}}
<div class="col-12 col-md-6 sexo">
    <div id="graficoSexo" style="display: none;" class="card chart-container grafico-sexo">
        
        {{-- <div class="chart-subtitle">
            <i class="bi bi-info-circle me-2"></i>
            Distribución por género y gestión
        </div> --}}
        <div id="graficoSex" class="grafico-container"></div>
        <div class="card-footer text-muted text-center">
            <i class="bi bi-info-circle me-2"></i>
            Resultados por indicador y gestión
        </div>
    </div>
</div>

{{-- Mensaje cuando no hay datos --}}
<div class="col-12">
    <div id="noDataMessage" style="display: none;" class="card">
        <div class="card-body no-data-message">
            <i class="bi bi-inbox"></i>
            <h5>No hay datos disponibles</h5>
            <p>No se encontraron datos para el parámetro seleccionado en las gestiones consultadas.</p>
        </div>
    </div>
</div>