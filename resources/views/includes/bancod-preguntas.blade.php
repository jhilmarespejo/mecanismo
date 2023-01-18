<div>
    <fieldset class="border border-secondary overflow-scroll p-3" style="height: auto">
        <div class="row my-1 mx-2 p1">
            <x-jet-input class="flex-1 mx-4" placeholder="Búsqueda" type="search"
                        wire:model='buscar' />
        </div>
        <legend class="float-none w-50 fs-5 ms-2">Banco de Preguntas</legend>

        @foreach ($bancoPreguntas as $preguntas)
            {{-- {{ $preguntas }} <hr/> --}}
        @endforeach

        @php
            $categorias = array();
            $subCategorias = array();
            $preguntas = array();
            $i = 0;
            foreach ($bancoPreguntas as $pregunta){
                if( is_null($pregunta->CAT_id) && is_null($pregunta->CAT_categoria) ){
                    $categorias[$pregunta->FK_CAT_id] = array($pregunta->FK_CAT_id, $pregunta->subCategoria );

                } else {
                    $subCategorias[$pregunta->FK_CAT_id] = array($pregunta->CAT_id, $pregunta->FK_CAT_id, $pregunta->CAT_categoria, $pregunta->subCategoria);

                    $categorias[$pregunta->CAT_id] = array($pregunta->CAT_id , $pregunta->CAT_categoria);
                }
                //echo $pregunta;
            }
            ksort($categorias);
            // dump($categorias);
            // dump($subCategorias);

        @endphp

        <div class="accordion" id="accordionCategorias">

              <div class="accordion-item">
                @foreach ($categorias as $categoria)
                    <h2 class="accordion-header" id="heading_{{$categoria[0]}}">
                      <button class="p-2 accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$categoria[0]}}" aria-expanded="true" aria-controls="collapse_{{$categoria[0]}}">
                        {{ $categoria[1] }}
                      </button>
                    </h2>
                    <div id="collapse_{{$categoria[0]}}" class="accordion-collapse collapse" aria-labelledby="heading_{{$categoria[0]}}" data-bs-parent="#accordionCategorias">
                      <div class="accordion-body">
                        {{-- si la pregunta no pertenece a una subcategoria se imprime normal.
                          Si la pregunta pertenece a una subcategoria se genera el accordion para la nueva subcategoria y se imprime el accordion-header y el acoordion-body --}}

                          <ul class="list-group" id="bcp_cat_{{$categoria[0]}}">
                            @foreach ($bancoPreguntas as $pregunta)
                              @if ($pregunta->FK_CAT_id == $categoria[0])
                                <li class="list-group-item hover p-1" id="{{$pregunta->BCP_id}}"
                                    wire:click="save({{ $pregunta->BCP_id }})"
                                    >
                                    {{ $pregunta->BCP_pregunta }}
                                </li>
                              @endif

                              {{-- @if ( $pregunta->FK_CAT_id == $categoria[0] )

                              @endif --}}
                            @endforeach
                          </ul>

                      </div>
                    </div>
                @endforeach

              </div>{{-- END accordion-item --}}


        </div>
    </fieldset>
</div>


          {{-- <div class="accordion" id="accordionSUBCategorias">
            <div class="accordion-item">
              @foreach ($subCategorias as $subCategoria)
                <h2 class="accordion-header" id="heading_cubcat_">
                  <button class="p-1 accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_subcat_" aria-expanded="true" aria-controls="collapse_subcat_">
                  ssss
                  </button>
                </h2>
                <div id="collapse_subcat_" class="accordion-collapse collapse" aria-labelledby="heading_cubcat_" data-bs-parent="#accordionSUBCategorias">
                  <div class="accordion-body">
                    YYYYYY YYYYYYYY
                  </div>
                </div>
              @endforeach
            </div>
          </div> --}}

