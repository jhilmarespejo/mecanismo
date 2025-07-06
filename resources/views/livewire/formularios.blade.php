<div class="my-5 p-5 bg-withe">

  <div class="px-6 py-4 flex items-center">

    <div class="text-center" >
        <h2 class="text-primary fs-2">Formularios</h2>
    </div>
    <div class="row m-2 p-2">
      <div class="col-9">
        <x-jet-input placeholder="Buscar" type="search" wire:model='buscarForm' />
      </div>
      <div class="col-3 ">
        @livewire('formularios-nuevo')
      </div>
    </div>

    {{-- @livewire('create-post') --}}
  </div>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Nombre del formulario</th>
            <th scope="col">Fecha de creación</th>
            <th scope="col">Opciones</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($formularios as $formulario)
                <tr>
                    <th scope="row">{{ $formulario->FRM_id }}</th>
                    <td>{{ $formulario->FRM_titulo }}</td>
                    <td>{{ $formulario->FRM_fecha }}</td>
                    <td class="">
                        <a href="/cuestionario/{{ $formulario->FRM_id }}">
                            <span class="btn btn-success shadow">
                            <i class="bi bi-wrench-adjustable fs-4 text-light shadow-sm"></i>
                            </span>
                        </a>
                        <a href="/cuestionario/show/{{ $formulario->FRM_id }}">
                            <span class="btn btn-primary shadow">
                                <i class="bi bi-eye fs-4 text-light shadow-sm"></i>
                            </span>
                        </a>
                    {{-- <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                          Acciones
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="#">Construir cuestionario</a></li>
                          <li><a class="dropdown-item" href="#">Ver cuestionario</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item" href="#">Separated link</a></li>
                        </ul>
                      </div> --}}
                    </td>
                </tr>
            @endforeach


        </tbody>
      </table>
      @if ($formularios->hasPages())
          <div class="px-6 py-3">
              {{ $formularios->links()  }}
          </div>
      @endif
</div>
