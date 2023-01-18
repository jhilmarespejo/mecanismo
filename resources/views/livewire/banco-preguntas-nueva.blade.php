<div>
    {{-- <div class="text-center"> --}}
        <button class="btn btn-primary text-light" wire:click="$set('modalPreguntaNueva',true)">
            Nueva pregunta
        </button>
    {{-- </div> --}}


    <x-jet-dialog-modal wire:model="modalPreguntaNueva" maxWidth="lg">
        <x-slot name="title">
            Nueva Pregunta
        </x-slot>
        <x-slot name="content">
            <form wire:submit.prevent="guardarPregunta">

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" wire:model.defer="BCP_pregunta" placeholder="Pregunta">
                    <label>Pregunta</label>
                    <x-jet-input-error for="BCP_pregunta" />
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" wire:model="BCP_tipoRespuesta" >
                    {{-- <select class="form-select" wire:model="BCPTipoRespuesta" wire:click="$emit('emitx', 'xxxx')"> --}}
                        <option value="" selected>Selecione...</option>
                        <option value="Afirmación" >Afirmación</option>
                        <option value="Casilla verificación" >Casilla verificación</option>
                        <option value="Lista desplegable" >Lista desplegable</option>
                        <option value="Fecha" >Fecha</option>
                        <option value="Numeral" >Numeral</option>
                        <option value="Respuesta corta" >Respuesta corta</option>
                        <option value="Respuesta larga" >Respuesta larga</option>
                    </select>
                    <label class="form-label">Tipo de respuesta</label>
                    <x-jet-input-error for="BCP_tipoRespuesta" />
                </div>

                <div class="form-floating mb-2">
                    @foreach($inputs as $key => $value)
                        <div class="input-group mb-1 ps-4">
                            <div class="input-group-text btn btn-outline-secondary" wire:click.prevent="removerInput({{$key}})"> <i class="bi bi-trash"></i></div>
                            <input type="text" class="form-control" placeholder="Opción" wire:model.defer="BCP_opciones.{{ $value }}">
                            {{-- <x-jet-input-error for="BCP_opciones.{{ $key }}" /> --}}
                        </div>
                    @endforeach
                </div>
                @if ($muestraBoton)
                    <div class="mb-2 ps-4"  wire:model="botonAdicionarPregunta" >
                        <button class="btn text-white btn-primary btn-sm" wire:click.prevent="adicionaInput({{$i}})"><i class="bi bi-plus-circle"></i> Adicionar opción</button>
                    </div>
                @endif

                @livewire('categorias')
                <x-jet-input-error for="FK_CAT_id" />
                
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" wire:model.defer="BCP_complemento" placeholder="Complemento">
                    <label >Complemento a la pregunta</label>
                    <x-jet-input-error for="BCP_complemento" />
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" wire:model.defer="BCP_aclaracion" placeholder="Aclaración">
                    <label >Aclaración a la pregunta</label>
                    <x-jet-input-error for="BCP_aclaracion" />
                </div>
                </x-slot>
                <x-slot name="footer">
                <x-jet-secondary-button wire:click="$set('modalPreguntaNueva',false)">
                    Cancelar
                </x-jet-secondary-button>
                <button class="btn btn-success text-light" type="submit">
                    Guardar pregunta
                </button>
            </form>

        </x-slot>


    </x-jet-dialog-modal>

</div>
