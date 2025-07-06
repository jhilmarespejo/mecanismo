{{-- 
    Archivo: resources/views/indicadores/reportes/controles-principales.blade.php
    Descripción: Comboboxes anidados para selección de categoría → indicador → parámetro
--}}

<div class="row g-4">
    <!-- Categorías -->
    <div class="col-12 col-md-4">
        <div class="select-card card text-dark bg-light mb-1">
            <div class="card-header fs-5 text-center">
                <i class="bi bi-tags me-2"></i>Categoría:
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
                <i class="bi bi-graph-up me-2"></i>Indicador:
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
                <i class="bi bi-list-check me-2"></i>Parámetros:
            </div>
            <div class="card-body bg-white">
                <select class="form-select custom-select" id="parametro" disabled>
                    <option value="">-- Seleccione un parámetro --</option>
                </select>
            </div>
        </div>
    </div>
</div>

