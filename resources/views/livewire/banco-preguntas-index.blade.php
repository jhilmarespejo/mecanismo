<div class="my-5 p-5 bg-withe">

    <div class="text-center" >
        <h2 class="text-primary fs-2">Banco de Preguntas </h2>
    </div>

    <div class="row mb-3 w-100">
        <div class="col-5">
            <div class="input-group">
                <div class="input-group-text">Mostrar: </div>
                <select class="form-select col-1" wire:model="resultadosPorPagina">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <div class="input-group-text"> Resultados por página</div>
            </div>
        </div>
        <div class="col-5 ">
            <div class="input-group">
                <x-jet-input placeholder="Buscar" type="search" wire:model='buscarPregunta' />
                <div class="input-group-text btn-secondary" wire:click="$set('buscarPregunta', '')"> <i class="bi bi-x-lg"></i></div>
            </div>
        </div>
        <div id="pregunta-nueva" class="col">
            @livewire('banco-preguntas-nueva')
        </div>
    </div>
    {{--
        <input type="text">
        <input type="date" name=" id=">
        <input type="number"> --}}


        <table class="table table-hover table-responsive bg-light pt-3" id="banco-preguntas">
            <thead>
                <tr>
                    <th scope="col" style="cursor: pointer"  wire:click="ordenar('BCP_id')">#</th>
                    <th scope="col" style="cursor: pointer"  wire:click="ordenar('BCP_pregunta')">Pregunta</th>
                    <th scope="col" style="cursor: pointer"  wire:click="ordenar('BCP_tipoRespuesta')">Tipo</th>
                    <th scope="col" >Complemento</th>
                    <th scope="col" style="cursor: pointer"  wire:click="ordenar('CAT_categoria')">Categoría</th>
                    <th scope="col" style="cursor: pointer"  wire:click="ordenar('subCategoria')">Sub Categoría</th>
                    <th scope="col" >Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bancoPreguntas as $pegunta)
                <tr>
                    <th scope="row">{{ $pegunta->BCP_id }}</th>
                    <td class="col-4">{{ $pegunta->BCP_pregunta }}</td>
                    <td class="col-2">{{ $pegunta->BCP_tipoRespuesta }}</td>
                    <td class="col-2">{{ $pegunta->BCP_complemento }}</td>

                    <td class="col-2">{{ ($pegunta->CAT_id)? $pegunta->CAT_categoria : $pegunta->subCategoria  }}</td>
                    <td class="col-2">{{ ($pegunta->CAT_id)? $pegunta->subCategoria : '' }}</td>
                    <td >
                        <a class="btn btn-primary shadow" wire:click="verPregunta('{{ $pegunta->BCP_id }}')">
                            <i class="bi bi-pencil-square fs-5 text-light"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            {{-- <tfoot>
                <tr>
                    <th class="id"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="acciones"></th>
                </tr>
            </tfoot> --}}
        </table>

        @if ( !is_null($bancoPreguntas->hasPages()) )
            <div class="flex flex-row mt-2">
                {{ $bancoPreguntas->links('pagination::bootstrap-5')  }}
            </div>

        @endif



        {{-- modal para editar la pregunta --}}
        <x-jet-dialog-modal wire:model="modalEditarPregunta" maxWidth="lg">
            <x-slot name="title">
                Editar Pregunta
            </x-slot>
            <x-slot name="content">
                <form wire:submit.prevent="editarPregunta">
                    {{-- <div class="col mb-3">
                        <label class="form-label">Pregunta</label>
                        <input type="text" class="form-control" wire:model.defer="BCP_pregunta">
                        <x-jet-input-error for="BCP_pregunta" />
                    </div> --}}

                    <div class="form-floating mb-3">
                        {{-- <input type="email" class="form-control" placeholder="name@example.com"> --}}
                        <input type="text" class="form-control" wire:model.defer="BCP_pregunta" placeholder="Pregunta">
                        <label >Pregunta</label>
                        <x-jet-input-error for="BCP_pregunta" />
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" wire:model="BCP_tipoRespuesta" placeholder="Pregunta">
                            <option selected>Selecione...</option>
                            <option value="Afirmación" >Afirmación</option>
                            <option value="Casilla verificación" >Casilla verificación</option>
                            <option value="Lista desplegable" >Lista desplegable</option>
                            <option value="Fecha" >Fecha</option>
                            <option value="Numeral" >Numeral</option>
                            <option value="Respuesta corta" >Respuesta corta</option>
                            <option value="Respuesta larga" >Respuesta larga</option>
                        </select>
                        <label>Tipo de respuesta</label>
                        <x-jet-input-error for="BCP_tipoRespuesta" />
                        <input type="hidden" wire:model="BCP_id" >
                    </div>

                    <div class="mb-1" >
                        @if ($muestraBoton || $BCP_tipoRespuesta == 'Casilla verificación' || $BCP_tipoRespuesta == 'Afirmación' || $BCP_tipoRespuesta == 'Lista desplegable')
                        <label>Opciones</label>

                        @if ($BCP_opciones)
                        @foreach( $BCP_opciones as $key => $value )
                        <div class="input-group mb-1 ps-4">
                            <div class="input-group-text btn btn-outline-secondary" wire:click="removerOpcion( {{$key}}, {{$BCP_id}} )"> <i class="bi bi-trash"></i> </div>

                            <input type="text" class="form-control" placeholder="Opción" wire:model="BCP_opciones.{{$key}}" >
                        </div>
                        @endforeach

                        @endif

                        {{-- HAY UN ERROR AL TIPEAR EN EL NUEVO INPUT --}}
                        @foreach($inputs as $key => $value)
                        <div class="input-group mb-1 ps-4">
                            <div class="input-group-text btn btn-outline-secondary" wire:click. prevent="removerInput({{$key}})"> <i class="bi bi-trash"></i>
                            </div>
                            <input type="text" class="form-control" placeholder="Opción" wire:model.defer="BCPopcionesAdicionales.{{ $value }}">
                        </div>
                        @endforeach
                        <div class="mb-1 ps-4">
                            <button type="button" class="btn text-white btn-primary btn-sm" wire:click="adicionarInput( {{$i}} )" ><i class="bi bi-plus-circle"></i> Adicionar opción</button>
                        </div>
                        @else
                        Sin opciones
                        {{-- {{$cantidadOpciones}} --}}
                        @endif
                    </div>

                    @livewire('categorias')

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
                    <button type="button" class="btn btn-danger text-light me-5" wire:click="$emit('desactivarPregunta',{{ $BCP_id }})">
                        Desactivar</button>

                        <button type="button" class="btn btn-secondary text-light" wire:click="$set('modalEditarPregunta', false)">Cancelar
                        </button>

                        <button class="btn btn-success text-light" type="submit">
                            Guardar
                        </button>
                    </form>
                </x-slot>
            </x-jet-dialog-modal>

            @push('js')
            <script>
                Livewire.on('desactivarPregunta', BCPid => {
                    Swal.fire({
                        title: '¿Seguro de desactivar ésta pregunta?',
                        // text: "No podra revertir esta acción",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Desactivar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.emitTo('banco-preguntas-index', 'desabilitarPregunta', BCPid);
                            Swal.fire(
                            'Pregunta desactivada',
                            // 'El Post ha sido eliminado',
                            // 'success'
                            )
                        }
                    })
                })
            </script>
            @endpush
        </div>
