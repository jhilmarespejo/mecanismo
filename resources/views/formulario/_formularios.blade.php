<div class="row">
    @foreach($formularios as $formulario)
        <div class="col-md-4 mb-3">
            {{-- <div class="card text-white"> <!-- Altura uniforme para todas las tarjetas -->
                <div class="card-header">
                    <h5 class="card-title" style="font-size: 1.25rem; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{ $formulario->FRM_titulo }}</h5> <!-- Títulos uniformes -->
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <p class="card-text"><strong>ID:</strong> {{ $formulario->FRM_id }}</p>
                    <p class="card-text"><strong>Fecha:</strong> {{ $formulario->FRM_fecha }}</p>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <!-- Botones con íconos -->
                    <button class="btn btn-light">
                        <i class="bi bi-eye"></i> Ver
                    </button>
                    <button class="btn btn-light">
                        <i class="bi bi-plus-circle"></i> Asignar
                    </button>
                </div>
            </div> --}}

            <div class="card mb-3" style="height: 100%;">
                <div class="card-body text-dark" style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                    <!-- Texto ajustado al espacio del div -->
                    <h5 class="card-title">
                        {{ $formulario->FRM_titulo }}
                    </h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-light">
                        <i class="bi bi-eye"></i> Ver
                    </button>
                    <button class="btn btn-light">
                        <i class="bi bi-plus-circle"></i> Asignar
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>
