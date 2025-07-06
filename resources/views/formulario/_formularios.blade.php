@if (count($formularios) == 0)
    <div class="col-md-12 mb-3 ">
        <div class="card mb-3 alert alert-warning">
            <div class="card-body text-dark">
                <h5 class="card-title">
                    <i class="bi bi-info-circle"></i> No hay formularios disponibles con ese criterio de búsqueda
                </h5>
            </div>
        </div>
    </div>
@else
    @foreach($formularios as $formulario)
        <div class="col-md-4 mb-3">
            <div class="card mb-3" style="height: 100%;">
                <div class="card-body text-dark" style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                    <!-- Texto ajustado al espacio del div -->
                    <h5 class="card-title">
                        {{ $formulario->FRM_titulo }}
                    </h5>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a class="btn btn-light" href="{{ route('formulario.verFormularioCreado', $formulario->FRM_id) }}">
                        <i class="bi bi-eye"></i> Ver formulario
                    </a>
                    <a class="btn btn-primary" href="{{ route('formulario.editar', $formulario->FRM_id) }}">
                        <i class="bi bi-pencil-square"></i> Editar formulario
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@endif