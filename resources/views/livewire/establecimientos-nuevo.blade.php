<div>
    <x-jet-danger-button wire:click="$set('abreModal',true)">
        Agregar Establecimiento
    </x-jet-danger-button>

    <form wire:submit.prevent="save">
        <x-jet-dialog-modal wire:model.defer="abreModal" maxWidth="lg">
            <x-slot name="title">
                Nuevo Establecimiento
            </x-slot>

            <x-slot name="content">
                <div class="mb-4 row">
                    <div class="col">
                        <x-jet-label value="Nombre del establecimiento" />
                        <x-jet-input type="text" class="w-full" wire:model.defer="EST_nombre" />
                        <x-jet-input-error for="EST_nombre" />
                    </div>
                    <div class="col">
                        <x-jet-label value="Dirección del establecimiento" />
                        <x-jet-input type="text" class="w-full" wire:model.defer="EST_direccion" />
                        <x-jet-input-error for="EST_direccion" />
                    </div>
                </div>
                <div class="mb-4 row">
                    <div class="col">
                        <x-jet-label value="Normativa interna" />
                        {{-- <x-jet-input type="text" class="w-full" wire:model.defer="EST_normativaInterna" /> --}}

                        <select class="form-select" wire:model.defer="EST_normativaInterna">
                            <option selected>Seleccione...</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                        <x-jet-input-error for="EST_normativaInterna" />
                    </div>
                    <div class="col">
                        <x-jet-label value="Teléfono de contacto" />
                        <x-jet-input type="text" class="w-full" wire:model.defer="EST_telefonoContacto" />
                        <x-jet-input-error for="EST_telefonoContacto" />
                    </div>
                </div>
                <div class="mb-4 row">
                    <div class="col">
                        <label class="">Género</label>
                        <select class="form-select" wire:model.defer="EST_genero">
                            <option selected>Seleccione...</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Mixto">Mixto</option>
                        </select>
                        <x-jet-input-error for="EST_genero" />
                    </div>
                    {{-- HACER UNA TABLA PARA ESTA OPCION Y COMPLETAR LOS DATOS --}}
                    <div class="col">
                        <label class="">Grupo Generacional</label>
                        <select class="form-select" wire:model.defer="EST_grupoGeneracional">
                            <option selected>Seleccione...</option>
                            <option value="1 año a 4 años">1 año a 4 años</option>
                            <option value="5 años a 10 años">5 años a 10 años</option>
                            <option value="10 años a 15 años">10 años a 15 años</option>
                            <option value="16 años en Adelante">16 años en Adelante</option>
                            <option value="18 años en adelante">18 años en adelante</option>
                            <option value="18 años a 60 años">18 años a 60 años</option>
                            <option value="18 años a 65 Años">18 años a 65 Años</option>
                        </select>
                        <x-jet-input-error for="EST_grupoGeneracional" />
                    </div>
                    {{-- HACER UNA TABLA PARA ESTA OPCION Y COMPLETAR LOS DATOS --}}

                </div>
                <div class="mb-4 row">
                    <div class="col-6">
                        <label class="">Población</label>
                        <select class="form-select" wire:model.defer="EST_poblacion">
                            <option selected>Seleccione...</option>
                            <option value="Abandono Y Orfandad">Abandono Y Orfandad</option>
                            <option value="Abandono Y Orfandad Con Enfermedades Cronicas">Abandono Y Orfandad Con Enfermedades Cronicas</option>
                            <option value="Adolescentes ">Adolescentes </option>
                            <option value="Adolescentes - Jovenes - Tercera Edad">Adolescentes - Jovenes - Tercera Edad</option>
                            <option value="Adolescentes Con Conducta De Consumo">Adolescentes Con Conducta De Consumo</option>
                            <option value="Adolescentes En Situacion De Abandono Y Riesgo Social  Y Con Discapacidad Fisica, Auditiva Y Mental">Adolescentes En Situacion De Abandono Y Riesgo Social  Y Con Discapacidad Fisica, Auditiva Y Mental</option>
                            <option value="Adolescentes En Situacion De Calle">Adolescentes En Situacion De Calle</option>
                            <option value="Adolescentes En Situacion De Orfandad, Abandono, Fuga, Extravio Y Riego Social Inminente">Adolescentes En Situacion De Orfandad, Abandono, Fuga, Extravio Y Riego Social Inminente</option>
                            <option value="Adolescentes En Situacion De Violencia Sexual">Adolescentes En Situacion De Violencia Sexual</option>
                            <option value="Adolescentes Infractores De La Ley Penal">Adolescentes Infractores De La Ley Penal</option>
                            <option value="Adolescentes Victimas De Trata Y Trafico">Adolescentes Victimas De Trata Y Trafico</option>
                        </select>
                        <x-jet-input-error for="EST_poblacion" />
                    </div>

                </div>
                <div class="mb-4 row">
                    <div class="col">
                        <x-jet-label value="Superficie" />
                        <div class="input-group">
                            {{-- <input type="text" class="form-control" id="inlineFormInputGroupUsername" placeholder="Username"> --}}
                            <x-jet-input type="number" class="w-full" wire:model.defer="EST_superficie" />
                            <div class="input-group-text">Mt<sup>2</sup> </div>
                        </div>
                        <x-jet-input-error for="EST_superficie" />
                    </div>
                    <div class="col-6">
                        <x-jet-label value="Superficie construida" />
                        <div class="input-group">
                            <x-jet-input type="number" class="w-full" wire:model.defer="EST_superficieConstruida" />
                            <div class="input-group-text">Mt<sup>2</sup> </div>
                        </div>
                        <x-jet-input-error for="EST_superficieConstruida" />
                    </div>
                </div>

                <div class="mb-4 row">
                    <div class="col-6">
                        <label class="">Tipo de establecimiento</label>
                        <select class="form-select" wire:model.defer="FK_TES_id">
                            <option selected>Tipo de ..</option>
                            @foreach ($tiposEstablecimiento as $tiposEstablecimiento)
                                <option value="{{$tiposEstablecimiento->TES_id}}">{{$tiposEstablecimiento->TES_tipo}}</option>
                            @endforeach

                        </select>
                        <x-jet-input-error for="FK_TES_id" />
                    </div>
                    <div class="col-6">
                        <label class="">Nivel de seguridad</label>
                        <select class="form-select" wire:model.defer="FK_NSG_id" >
                            <option selected>Nivel de seguridad...</option>
                            @foreach ($nivelesSeguridad as $nivelesSeguridad)
                                <option value="{{$nivelesSeguridad->NSG_id}}">{{$nivelesSeguridad->NSG_nivelSeguridad}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for="FK_NSG_id" />
                    </div>
                </div>


                <div >
                    @livewire('ciudades')
                    {{-- <x-jet-input type="text" wire:model="FK_PAT_id" /> --}}
                    {{-- <x-jet-input-error for="FK_CID_id" /> --}}
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$set('abreModal',false)">
                    Cancelar
                </x-jet-secondary-button>
                <button class="btn btn-primary" type="submit">Crear</button>

            </x-slot>

        </x-jet-dialog-modal>
    </form>
</div>
{{-- <script>

    $( "#CID_id" ).on("change", function() {
        //console.log( $(this).val());
        $('#FK_CID').text($(this).val());
        $('#FK_CID').val($(this).val());
    });
</script> --}}
