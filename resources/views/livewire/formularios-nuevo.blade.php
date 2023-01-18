<div>
    <x-jet-danger-button wire:click="$set('abreModal',true)">
        Agregar Formulario
    </x-jet-danger-button>


    <x-jet-dialog-modal wire:model="abreModal">
        <x-slot name="title">
            Nuevo Formulario
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-jet-label value="Título del formulario" />
                <x-jet-input type="text" class="w-full" wire:model.defer="FRM_titulo" />
                <x-jet-input-error for="FRM_titulo" />
            </div>
            <div class="mb-4">

                <x-jet-label value="Establecimiento" />
                {{-- wire:change="asignarEstablecimientoId('{{$EST_nombre}}')" --}}
                <input list="establecimientos" class="form-control w-full" wire:model="EST_nombre" wire:change="asignarEstablecimientoId('{{$EST_nombre}}')">
                    <datalist id="establecimientos">
                        @foreach ($establecimientos as $establecimiento)
                            <option value="{{$establecimiento->EST_nombre}} [{{$establecimiento->Ciudad}}]">
                        @endforeach
                    </datalist>
                <x-jet-input-error for="EST_nombre" />
            </div>
            <div class="mb-4">
                <x-jet-label value="Número de formulario" />
                <x-jet-input type="number" class="w-full" wire:model.defer="FRM_version" />
                <x-jet-input-error for="FRM_version" />
            </div>
            <div class="mb-4">
                <x-jet-label value="Fecha" />
                <x-jet-input type="text" class="w-full" wire:model.defer="FRM_fecha" />
                <x-jet-input-error for="FRM_fecha" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('abreModal',false)">
                Cancelar
            </x-jet-secondary-button>
            <x-jet-danger-button wire:click="save"
                class="disabled:opacity-25">
                Crear
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>



</div>
