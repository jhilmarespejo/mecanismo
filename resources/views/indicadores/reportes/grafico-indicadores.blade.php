{{-- 
    Archivo: resources/views/indicadores/reportes/grafico-indicadores.blade.php
    Descripción: Contenedor para el gráfico de indicadores (columna izquierda)
--}}

<div class="col-12 col-md-6 indicadores">
    <div id="graficoIndicadores" style="display: none;" class="card chart-container">
       
       
        <div id="graficoIndicador" class="grafico-container"></div>
        <div class="card-footer text-muted text-center">
            <i class="bi bi-info-circle me-2"></i>
            Resultados por indicador y gestión
        </div>
    </div>

    {{-- Indicador de carga para indicadores --}}
    <div id="loadingIndicadores" class="loading-indicator">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-2">Cargando datos del indicador...</p>
    </div>
</div>