<div class="py-4">
    {{-- establecimientos
    {{$ciudades}}
    {{$poblaciones}}
    {{$nivelSeguridad}}
    {{$tipoEstablecimiento}} --}}

    {{-- <fieldset class="border border-secondary overflow-scroll p-3">
        <legend class="float-none w-50 fs-5 ms-2">Establecimiento</legend>
        <form class="row g-3">
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Email</label>
                <input type="email" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Password</label>
                <input type="password" class="form-control" id="inputPassword4">
            </div>
            <div class="col-12">
                <label for="inputAddress" class="form-label">Address</label>
                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St">
            </div>
            <div class="col-12">
                <label for="inputAddress2" class="form-label">Address 2</label>
                <input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor">
            </div>
            <div class="col-md-6">
                <label for="inputCity" class="form-label">City</label>
                <input type="text" class="form-control" id="inputCity">
            </div>
            <div class="col-md-4">
                <label for="inputState" class="form-label">State</label>
                <select id="inputState" class="form-select">
                    <option selected>Choose...</option>
                    <option>...</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="inputZip" class="form-label">Zip</label>
                <input type="text" class="form-control" id="inputZip">
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="gridCheck">
                    <label class="form-check-label" for="gridCheck">
                        Check me out
                    </label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Sign in</button>
            </div>
        </form>
    </fieldset> --}}

    {{-- SE REEMPLAZÓ ESTE ARCHIVO POR OTRO --}}
    <div class="text-center" >
        <h2 class="text-primary fs-2">Establecimientos xxx</h2>
    </div>
    <div class="row m-2 p-2">
        <div class="col">
          <x-jet-input placeholder="Buscar" type="search" wire:model='buscarEstablecimiento'  />
        </div>
        <div class="col-3 ">
          {{-- @livewire('nuevo-formulario') --}}
          {{-- Nuevo formulario --}}
          <select class="form-select " wire:model='buscarPorTipo'>
            <option value="" selected >Seleccione</option>
            @foreach ($tiposEstablecimientos as $establecimiento)
                <option value="{{$establecimiento->TES_id}}" >{{ $establecimiento->TES_tipo }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-3 ">
            @livewire('establecimientos-nuevo')
        </div>
    </div>

    @if (count($establecimientos))
        <table class="table table-responsive table-hover bg-light">
            <thead>
            <tr>
                <th scope="col" style="cursor: pointer;" wire:click="ordenar('EST_id')" >#</th>
                <th scope="col" style="cursor: pointer;" wire:click="ordenar('EST_nombre')" >Nombre</th>
                <th scope="col" style="cursor: pointer;" wire:click="ordenar('Municipio')" >Municipio</th>
                <th scope="col" style="cursor: pointer;" wire:click="ordenar('Provincia')" >Provincia</th>
                <th scope="col" style="cursor: pointer;" wire:click="ordenar('Departamento')" >Departamento</th>
                <th scope="col" style="cursor: pointer;" wire:click="ordenar('EST_direccion')" >Dirección</th>
                <th scope="col" style="cursor: pointer;" wire:click="ordenar('TES_tipo')" >Tipo</th>
                <th scope="col">Teléfono de contacto</th>
                <th scope="col"> Opciones</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($establecimientos as $key=>$establecimiento)
                    <tr>
                        <th>{{ $key+1 }}</th>
                        <td>{{ $establecimiento->EST_nombre }}</td>
                        <td>{{ $establecimiento->Municipio }}</td>
                        <td>{{ $establecimiento->Provincia }}</td>
                        <td>{{ $establecimiento->Departamento }}</td>
                        <td>{{ $establecimiento->EST_direccion }}</td>
                        <td>{{ $establecimiento->TES_tipo }}</td>
                        <td>{{ $establecimiento->EST_telefonoContacto }}</td>
                        <td class=" col-2 text-center">
                            {{-- <span class="btn btn-success shadow" wire:click="edit({{ $establecimiento->EST_id }})">
                                <i class="bi bi-pen fs-6 text-light shadow-sm" ></i>
                            </span> --}}

                            {{-- <span class="btn btn-success shadow" wire:click="$emit('eliminar',{{ $establecimiento->EST_id }})">
                                <i class="bi bi-gear fs-6 text-light shadow-sm" > Deshabilitar establecimiento</i>
                            </span> --}}
                            {{-- </div>  --}}
                                <a href="/establecimientos/historial/{{$establecimiento->EST_id}}" class="btn btn-success text-light" ><i class="bi bi-clock-history"></i> Historial</a>
                            {{-- <div class="dropdown">
                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                  Opciones
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                  <li><a class="dropdown-item" href="#"><i class="bi bi-ui-checks-grid" ></i> Construir formulario</a></li>
                                  <li><a class="dropdown-item" href="/establecimientos/show/{{$establecimiento->EST_id}}"><i class="bi bi-clock-history"></i> Ver historial</a></li>
                                </ul>
                            </div> --}}

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ( !is_null($establecimientos->hasPages()) )
            <div class="px-6 py-3">
                {{ $establecimientos->links('pagination::bootstrap-5')  }}
            </div>
        @endif
    @else
        <div class="alert alert-danger" role="alert">
            Sin resultados para la búsqueda
        </div>
    @endif

    <x-jet-dialog-modal wire:model="modal_edicion">
        <x-slot name=title>
            Datos de establecimientos
        </x-slot>
        <x-slot name=content>
            @foreach ( $establecimientos as $establecimiento)
                {{$establecimiento->EST_nombre}}
            @endforeach
        </x-slot>
        <x-slot name=footer>
             <x-jet-secondary-button {{--wire:click="$set('open_edit',false)"--}}   >
                Cancelar
            </x-jet-secondary-button>
            <x-jet-danger-button {{-- wire:click="update" wire:loading.attr="disabled"--}} class="">
                Actualizar
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>

    @push('js')
    <script>
        Livewire.on('eliminar', EST_id => {
            Swal.fire({
                title: '¿Esta seguro?',
                text: "No podra revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Inhabilitar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emitTo('establecimientos', 'emit_eliminarEstablecimiento', EST_id);
                    // Swal.fire(
                    //     'Eliminado',
                    //     'El establecimiento ha sido inhabilitado',
                    //     'success'
                    // )
                }
            })
        })
    </script>
    @endpush
</div>
