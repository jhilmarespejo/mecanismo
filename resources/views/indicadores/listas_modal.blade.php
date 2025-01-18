{{-- <div class="modal fade" id="centrosModal" tabindex="-1" aria-labelledby="centrosModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="centrosModalLabel">Centros Penitenciarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Parámetro recibido: <strong>{{ $parametro }}</strong></p>
                <!-- Aquí puedes agregar contenido adicional basado en el parámetro -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

 --}}



<!-- Modal CENTROS PENITENCIARIOS -->
@if ($tipo == 'centros penitenciarios')
    <div class="modal fade" id="centrosModal" tabindex="-1" aria-labelledby="centrosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="centrosModalLabel">{{ $parametro }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @foreach ($centrosPenitenciarios as $departamento => $centros)
                        <div class="mb-3">
                            <h6 class="fw-bold">{{ $departamento }}</h6>
                            <ul class="list-group">
                                @foreach ($centros as $centro)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $centro->EST_nombre }}
                                        <input type="number" name="numero[{{ $centro->EST_nombre }}]" class="form-control w-25" placeholder="0">
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endif
