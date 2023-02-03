{{-- CATEGORIAS RESPONSES --}}

@isset($subCategorias)
    <div class="input-group ps-2 pt-2">
        <div class="input-group-text">Subcategoría:</div>
        <select class="form-select cbo-subCategoria" name="RBF_subcategoriaId.{{$index}}" id="cbo_subcategoria_{{$id}}_{{$index}}">
            <option value="0" selected>Seleccione...</option>
            @foreach ($subCategorias as $subCategorias)
                <option value="{{ $subCategorias->CAT_id }}">{{$subCategorias->CAT_categoria}}</option>
            @endforeach
        </select>

        <div class="input-group-text btn-danger btn-remover-subcategoria text-dark" id="remover_subcategoria-{{$id}}_{{$index}}"><i class="bi bi-trash"></i></div>

        <input type="hidden" name="RBF_subcategoria.{{$index}}" id="txt_subcategoria_{{$id}}_{{$index}}">
    </div>
    <div id="preguntas_subcategoria_{{$id}}_{{$index}}" class="ps-2" ></div>
@endisset

{{-- <ol> --}}
@isset($preguntas)
    <li class="hover list-group-item ps-2 pt-1 pb-0 border-0">
        <div class="input-group my-0 ">
            <select class="form-select cbo-preguntas hover" name="RBF_preguntaId.{{$index}}" id="cbo_preguntas_{{$index}}">
                <option value="0" selected>Seleccione...</option>
                @foreach ($preguntas as $pregunta)
                    <option value="{{ $pregunta->BCP_id }}">{{$pregunta->BCP_pregunta}}</option>
                @endforeach
            </select>
            <div class="input-group-text btn-danger btn-remover-pregunta text-light" id="remover_pregunta_{{$index}}"><i class="bi bi-x-lg"></i></div>
            {{-- <div class="input-group-text btn-success ver-pregunta text-light" id="ver_pregunta_{{$index}}"><i class="bi bi-eye"></i></div> --}}
        </div>
    </li>
@endisset
{{-- </ol> --}}
@isset($listaPreguntas)

        <ol class="list-group grupo-preguntas">
            @foreach ($listaPreguntas as $key=>$pregunta)
                {{-- <li class="py-0 px-2 list-group-item hover" id="elemento_{{$key+1}}"> --}}
                <li class="py-0 px-2 list-group-item hover" id="elemento_{{ $pregunta->BCP_id }}">
                    <div class="input-group hover">
                        <span class="col"><strong>{{$key+1}}.</strong> {{$pregunta->BCP_pregunta}}</span>

                        <input type="hidden" name="RBF_preguntaId.{{ $pregunta->BCP_id }}{{$key}}" value="{{ $pregunta->BCP_id }}">

                        <span class=" input-group-text btn-danger btn-remover-pregunta text-dark" id="remover_pregunta_{{$pregunta->BCP_id}}"><i class="bi bi-x-lg"></i></span>

                        {{-- <span class="input-group-text btn-success ver-pregunta text-light" id="ver_pregunta_{{$key+1}}"><i class="bi bi-eye"></i></span> --}}
                    </div>
                </li>
            @endforeach
        </ol>

        {{-- <input type="hidden" name="RBF_pregunta.{{$index}}" id="txt_pregunta_{{$index}}"> --}}
    {{-- </div> --}}
@endisset


