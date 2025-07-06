@extends('layouts.app')
@section('title', 'Reportes - indicadores')

@section('content')
{{-- Librerías de Highcharts para gráficos --}}
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/draggable-points.js"></script>

<div class="container mt-3 p-4 bg-white">
    @include('layouts.breadcrumbs', $breadcrumbs)
    <h1 class="text-center text-primary">Reportes Estadísticos</h1>
    
    {{-- Controles principales (comboboxes) --}}
    @include('indicadores.reportes.controles-principales')
    
    {{-- Estilos para gráficos --}}
    @include('indicadores.reportes.estilos-graficos')
    
    {{-- Contenedor de gráficos --}}
    <div class="container row g-4 mt-1">
        {{-- Gráfico de Indicadores (columna izquierda) --}}
        @include('indicadores.reportes.grafico-indicadores')
        
        {{-- Gráficos de Parámetros (columna derecha y completa) --}}
        @include('indicadores.reportes.graficos-parametros')
    </div>
</div>

{{-- Scripts JavaScript modularizados --}}
@include('indicadores.reportes.scripts-principales')

@endsection