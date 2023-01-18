<div>
    <div class="row">
        <div class="form-floating mb-3 col">
            <select class="form-select" wire:model="categoria">
                    <option selected>Selecione...</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->CAT_id }}" >{{$categoria->CAT_categoria}}</option>
                @endforeach
            </select>
            <label class="form-label">Categoría</label>
        </div>
        <div class="col">
            @if ( $subCategorias->count() > 0 )
                <div class="form-floating mb-3">
                    <select class="form-select" wire:model="subCategoria">
                            <option selected>Selecione...</option>
                        @foreach ($subCategorias as $subCategoria)
                        <option value="{{ $subCategoria->CAT_id }}" >{{$subCategoria->CAT_categoria}}</option>
                        @endforeach
                    </select>
                    <label class="form-label">Sub Categoría</label>
                </div>
            @endif
        </div>
    </div>


</div>

