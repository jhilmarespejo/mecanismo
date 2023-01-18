<div class="row">
    <div class="col-9 mx-2 my-2 p-2">
        <div class="card">
            <div class="card-body">
              <span class="text-center"><h3>{{ $formulario->FRM_titulo }}</h3></span>
              <h4>Número de formulario:{{ $formulario->FRM_version }}</h4>
              <h5>Fecha de creación: {{ $formulario->FRM_fecha }}</h5>
            </div>
          </div>

        <div class="card add-input mt-2 p-4 ">
            @foreach($inputs as $key => $value)
                <div class="row">
                    <fieldset class="col border p-2">
                        {{-- <div class="input-group">
                            <div class="input-group-text">CATEGORÍA: </div>
                            <select class="form-select" wire:model="categoria.{{ $value }}" >
                                    <option selected>Selecione...</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->CAT_id }}" wire:change="change('{{$key}}')" >{{$categoria->CAT_categoria}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        @livewire('categorias', key($key))
                    </fieldset>

                    <div class="col-md-2">
                        <button class="btn btn-danger btn-sm" wire:click.prevent="remove({{$key}})">Remover</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col ">
        <div class="position-fixed card mx-2 my-2 p-2 col">
            <button class="btn btn-success btn text-light mt-1 col" wire:click.prevent="agreagarCategoria({{$i}})">Adicionar CATEGORÍA</button>

            <button class="btn btn-success btn text-light mt-1 ms-2" wire:click.prevent="agreagarCategoria({{$i}})">Adicionar Subcategoria</button>

            <button class="btn btn-primary btn text-light mt-1 ms-4" wire:click.prevent="agreagarCategoria({{$i}})">Adicionar Pregunta</button>
        </div>
    </div>

</div>
