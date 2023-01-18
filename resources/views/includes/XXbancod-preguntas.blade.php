<div>
    <fieldset class="border border-secondary overflow-scroll p-3" style="height: 480px">
        <div class="row my-1 mx-2 p1">
            <x-jet-input class="flex-1 mx-4" placeholder="Búsqueda" type="search"
                        wire:model='buscar' />
        </div>
        <legend class="float-none w-50 fs-5 ms-2">Banco de Preguntas</legend>
          <div class="accordion mt-3" id="banco_preguntas">
            @php
                $categorias = array();
                $dbCategorias = array();
                foreach ($bancoPreguntas as $pregunta){
                        $categorias[$pregunta->CAT_id] = array($pregunta->CAT_id,$pregunta->CAT_categoria, $pregunta->subCatId);
                }
                foreach ($listaCategorias as $listaCategoria) {
                    array_push($dbCategorias, [$listaCategoria->CAT_id, $listaCategoria->CAT_categoria]);
                }
            @endphp

                
            @foreach ( $categorias as $categoria ) 
                    <div class="accordion-item banco-preguntas" id="accordion_{{ $categoria[0] }}">
                            @if ( !$categoria[2] )
                                <h2 class="accordion-header" id="heading_{{ $categoria[0] }}">
                                    <button class="p-2 accordion-button hover {{ $buscar? '' : 'collapsed'}}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $categoria[0] }}" aria-expanded="false" >
                                        {{$categoria[1]}}
                                        {{$categoria[2]}}
                                    </button>
                                </h2>
                            @endif
                        <div id="collapse_{{ $categoria[0] }}" class="accordion-collapse collapse {{ $buscar? 'show' : ''}}" aria-labelledby="heading_{{ $categoria[0] }}" data-bs-parent="#accordionExample">
                            <div class="accordion-body" id="banco_categoria_{{$categoria[0]}}">
                                <ul class="list-group" id="banco_preguntas">
                                    @foreach ($bancoPreguntas as $pregunta)
                                        @if ($pregunta->FK_CAT_id == $categoria[0])
                                            <li class="list-group-item hover p-1" id="{{$pregunta->BCP_id}}"    
                                                wire:click="save({{ $pregunta->BCP_id }})" 
                                                >
                                                {{ $pregunta->BCP_pregunta }}
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
            @endforeach
            @if ( !$categorias )
                <div class="alert alert-danger m-4 " role="alert">
                    No se encotraron datos!
                </div>
            @endif
    </fieldset>
</div>
