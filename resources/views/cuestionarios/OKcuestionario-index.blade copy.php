
@extends('layouts.app')
@section('title', 'Cuestionario')


@section('content')

<div class="row">
    <form action="{{ route('cuestionario.store') }}" method="Post" id="frm_cuestionario_nuevo">
        @csrf
        <div class="col mx-2 my-2 p-2" id="cuestionario">
            <div class="card">
                <div class="card-body">
                <span class="text-center"><h3>{{ $formulario->FRM_titulo }}</h3></span>
                <div class="d-flex justify-content-center">
                    <h4>{{ $formulario->EST_nombre }}</h4>
                </div>
                <h4 class="mt-3">Número de formulario: {{ $formulario->FRM_version }}</h4>
                <h5>Fecha de creación: {{ $formulario->FRM_fecha }}</h5>
                <input type="hidden" name="FRM_id" value="{{$formulario->FRM_id}}">
                </div>
                <div class="position-fixed mx-2 my-2 p-2 col " style="z-index: 10">
                    <span class="btn btn-success btn text-light mt-1 shadow" id="add-categoria">Adicionar CATEGORÍA</span>
                </div>
            </div>
            {{-- <div class="card mt-2 p-2 my-3" id="c_1"> --}}
                {{-- <div class="d-flex card-header p-0">
                    <div class="input-group">
                        <div class="input-group-text">Categoría:</div>
                        <select class="form-select cbo_categoria" name="categoria" id="cbo_categoria_1">
                                <option value="0" selected>Seleccione...</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->CAT_id }}">{{$categoria->CAT_categoria}}</option>
                            @endforeach
                        </select>
                        <div class="input-group-text">
                            <div class="p-2 input-group-text btn btn-xs btn-outline-secondary categoria" id="btn_categoria_reset_1">
                                <i class="bi bi-x-lg"></i>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- <div class="card-body">
                    <div id="div_subcategorias"></div>
                    <p class="ps-4 my-2">Preguntas:</p>
                    <div id="div_preguntas"></div>
                </div>
                <div class="card-footer input-group-text d-flex p-0">
                    <div class="me-auto p-2 "><i class="bi bi-tools"></i></div>
                    <div class="p-2 input-group-text btn btn-xs btn-outline-secondary"><i class="bi bi-plus-square"> Subcategoría</i></div>&nbsp;
                    <div class="p-2 input-group-text btn btn-xs btn-outline-secondary" id="btn_pregunta_agregar"><i class="bi bi-bookmark-plus"> Pregunta</i></div>
                </div> --}}
            {{-- </div> --}}

        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>


<script>
    var i = 0;
    var j = 0;
    var k = 0;
    // var categoia =0;
    $("#add-categoria").click(function () {
        ++i;
        $("#cuestionario").append('<div class="card mt-2 p-2 my-3" id="c_'+i+'"><div class="d-flex card-header p-0"><div class="input-group"><div class="input-group-text">Categoría:</div><select class="form-select cbo-categoria" name="RBF_categoriaId.'+i+'" id="cbo_categoria_'+i+'"><option value="0" selected="selected">Seleccione...</option>@foreach ($categorias as $categoria)<option value="{{ $categoria->CAT_id }}">{{$categoria->CAT_categoria}}</option>@endforeach</select>       <input type="hidden" name="RBF_categoria.'+i+'" id="txt_categoria_'+i+'">      <div class=" btn btn-xs btn-danger btn-categoria-reset" id="btn_categoria_reset_'+i+'"><i class="bi bi-eraser"></i></div>&nbsp;&nbsp;&nbsp;    <div class="visually-hidden btn btn-xs btn-success plegar" id="plegar_'+i+'"><i class="bi bi-caret-down" ></i></div>    <div class=" btn btn-xs btn-warning desplegar" id="desplegar_'+i+'"><i class="bi bi-caret-up" ></i></div>        </div></div>        <div class="card-body"><div id="div_subcategorias_'+i+'" >  </div> </div>        <div class="card-footer input-group-text d-flex p-0"><div class="me-auto p-2"><i class="bi bi-tools" ></i></div>    <div class="m-0 mx-4 alert alert-warning text-center visually-hidden" id="msg_tool_'+i+'"><div class="spinner-grow spinner-grow-sm text-warning" role="status"></div>&nbsp; </div>     <div class="p-2 input-group-text btn btn-xs btn-success btn-agrega-subcategoria" id="btn_subcat_'+i+'"><i class="bi bi-plus-circle"> Subcategoría</i></div>&nbsp;<div class="p-2 input-group-text btn btn-xs btn-success btn-agregar-pregunta" id="btn_pregunta_agregar_'+i+'"><i class="bi bi-plus-circle"> Pregunta</i></div></div></div>');
    });

    // Desplegar, replegar el contenido de la categoria
    $(document).on('click','.desplegar, .plegar', function () {
        let id = parseInt((this.id).replace(/[^0-9.]/g, ""));
        $('#div_subcategorias_'+id).toggle("slow");

        let btn = (this.id).split("_");
        if( btn[0] == 'desplegar' ){
            $('#plegar_'+id).removeClass('visually-hidden').animate();
            $('#desplegar_'+id).addClass('visually-hidden').animate();
            $("#c_"+id+" > div.card-body").css({"background":"#e9ecef"});
        } else {
            $('#plegar_'+id).addClass('visually-hidden');
            $('#desplegar_'+id).removeClass('visually-hidden');
            $("#c_"+id+" > div.card-body").css({"background":"none"});
        }
        // console.log( btn[0] );
    });

    // Agrega una nueva pregunta
    $(document).on('click', '.btn-agregar-pregunta', function () {

        j++;
        var id = parseInt((this.id).replace(/[^0-9.]/g, ""));

        /* busca el valor de la ULTIMA subcategoria seleccionada para obtener el FK_CAT_id */
        var FK_CAT_id = $("#c_"+id).find('select.cbo-subCategoria:last').val();

        /* Si no existe subcatergoria busca el FK_CAT_id en el ultimo combobox de categoria*/
        if( !FK_CAT_id ){
            var FK_CAT_id = $("#c_"+id).find('select.cbo-categoria:last').val();
        }
        console.log(FK_CAT_id);

        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "{{ route('categorias.buscarPregunta') }}",
            type: 'POST',
            data: {FK_CAT_id: FK_CAT_id, index: j},
            beforeSend: function () {},
            success: function (data, response) {
                $("#div_subcategorias_"+id).append(data);
                if(!data){
                    if($("#msg_tool_"+id+" span").length == 0){
                        $("#msg_tool_"+id).removeClass("visually-hidden");
                        $("#msg_tool_"+id).append('<span>Debe seleccionar un dato</span>')
                    }
                } else {
                    $("#msg_tool_"+id).addClass("visually-hidden");
                    $("#msg_tool_"+id).children('span').remove();
                };
            },
            error: function(response){}
        });
    });

    // Elimina la pregunta selecionada
    $(document).on('click', '.btn-remover-pregunta', function () {
        // var id = parseInt((this.id).replace(/[^0-9.]/g, ""));
        $(this).parent().parent().remove()
    });

    // Elimina la SUBCATEGORIA selecionada
    $(document).on('click', '.btn-remover-subcategoria', function () {
        var id = (this.id).split("-");
        $("#cbo_subcategoria_"+id[1]).parent().remove();
        // console.log($('cbo_subcategoria_'+id[1]).parent());
    });

    // Verifica preguntas repetidas
    $(document).on('change', '.cbo-preguntas', function () {
        idPatent =  $(this).parent().parent().attr('id');
       var arrayIds = [];
        $("#"+idPatent+" select.cbo-preguntas").each(function(){
            arrayIds.push($(this).val())
        });

        const set = new Set(arrayIds);
        const duplicados = arrayIds.filter(item => {
            if (set.has(item)) { set.delete(item); }
            else { return item; }
        });

        if(duplicados.length > 0){
            if($("#msg_tool_"+id+" span").length == 0){
                $("#msg_tool_"+id).removeClass("visually-hidden");
                $("#msg_tool_"+id).append('<span>Existen datos duplicados</span>')
                $("#btn_pregunta_agregar_"+id).prop("disabled",true);
                // console.log(duplicados);

            }
        }else{
            $("#msg_tool_"+id).addClass("visually-hidden");
            $("#msg_tool_"+id).children('span').remove();
            $("#btn_pregunta_agregar_"+id).prop("disabled",false);
        };
        });

    // Agrega subcategoria
    $(document).on('click', '.btn-agrega-subcategoria', function () {
        ++k;
        var id = parseInt((this.id).replace(/[^0-9.]/g, ""));
        categoria = $("#cbo_categoria_"+id).val();
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "{{ route('categorias.buscarSubcategoria') }}",
            type: 'POST',
            data: {CAT_id: categoria, index: k},
            beforeSend: function () {},
            success: function (data, response) {
                // $("#div_preguntas_"+id).children().remove();
                $("#div_subcategorias_"+id).append(data);
            },
            error: function(response){}
        });
    });

    //Al seleccionar una SUBcategoria se cargan en pantalla las sus preguntas correcpondiente
    $(document).on('change', '.cbo-subCategoria', function () {
        let id = $( "#"+$(this).attr('id')+" option:selected" ).val();

        ids = (((this.id).split("_")).slice(-2));
        console.log( ids[0], ids[1]);
        $("#txt_subcategoria_"+ids[0]+"_"+ids[1]).val( $("#"+this.id+" option:selected").text() );
        $.ajax({
                async: true,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: "{{ route('categorias.buscarElementos') }}",
                type: 'POST',
                data: {FK_CAT_id: id, elementos: 'subCategoria'},
                beforeSend: function () {  },
                success: function (data, response) {
                    $("#preguntas_subcategoria_"+ids[0]+"_"+ids[1]).html(data);
                    // $("#"+elemento).html(data);
                },
                error: function(response){ }
            });
    });

    // Reinicia el select box de la categoria seleccionada
    $(document).on('click', '.btn-categoria-reset', function () {
        id = parseInt((this.id).replace(/[^0-9.]/g, ""));
        $("#cbo_categoria_"+id).val('0');
        $("#div_subcategorias_"+id).children().remove();

    });

    // //En caso de que la categoría seleccionada tenga subcategoria, busca y muestra una subcategoria
        // $(document).on('change', '.cbo-categoria', function () {
        //     id = parseInt((this.id).replace(/[^0-9.]/g, ""));
        //     $.ajax({
        //         async: true,
        //         headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        //         url: "{{ route('categorias.buscarSubcategoria') }}",
        //         type: 'POST',
        //         data: {CAT_id: $(this).val(), index: '0'},
        //         beforeSend: function () {
        //         },
        //         success: function (data, response) {
        //             $('#div_subcategorias_'+id).html(data);
        //             $("#div_preguntas_"+id).children().remove();
        //             $("#txt_categoria_"+id).val( $("#cbo_categoria_"+id+" option:selected").text() );
        //         },
        //         error: function(response){ }
        //     });
        // });


    //En caso de que la categoría seleccionada tenga subcategoria, busca y muestra una subcategoria
    // si no hay SUBCATEGORIA muestra una lista de todas las preguntas
    $(document).on('change', '.cbo-categoria', function () {
        id = parseInt((this.id).replace(/[^0-9.]/g, ""));
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "{{ route('categorias.buscarElementos') }}",
            type: 'POST',
            data: {FK_CAT_id: $(this).val(), index: '0'},
            beforeSend: function () {},
            success: function (data, response) {
                $('#div_subcategorias_'+id).html(data);
                // $("#div_preguntas_"+id).children().remove();
                // $("#txt_categoria_"+id).val( $("#cbo_categoria_"+id+" option:selected").text() );
            },
            error: function(response){ }
        });
    });

    $(document).on('change', '#cuestionario', function () {
        // console.log( $('#cuestionario').find('li') );

        // $('#cuestionario').find('li').css({"color": "red", "border": "2px solid red"});


    });


</script>

@endsection

{{-- TAREAS
    1. Poner las categorias en un accordion
    2. Poner color a los botones +Subcategoria y + Pregunta
    --}}
