

<div class="col-sm-4 d-flex align-items-center">
    <div class="card w-100 text-start">
        <div class="card-body">
          <h5 class="card-title">Seleccione un formulario y una categoría</h5>
          {{-- <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6> --}}

          <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Formularios: </label>
            <form method="POST" id="form_formularios" action="{{route('index.buscarIdForm')}}" >
                <div class="input-group">
                    <select class="form-select" id="select_formularios" name="formulario">
                        <option selected disabled>Seleccione</option>
                        @foreach ($formularios as $formulario)
                            <option value="{{$formulario->FRM_titulo}}"> {{$formulario->FRM_titulo}}</option>
                        @endforeach
                    </select>

                    <div id="spinner_formularios" class="input-group-text visually-hidden">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </form>
          </div>
          <div id="categorias_formulario" class="py-2"></div>
          <div id="preguntas_categoria" class="py-2"></div>

        </div>
    </div>
</div>
<div class="col-sm-8" div="graficos">
    <div id="grafico1"></div>
    <div id="grafico2"></div>
</div>


<script>
    $(document).on('change', '#select_preguntas', function(){
        $.ajax({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: $('#form_preguntas').attr("action"),
            type: "POST",
            data: $('#form_preguntas').serialize(),
            beforeSend: function () {
                $('#spinner_preguntas').removeClass('visually-hidden');
            },
            success:  function (response, params) {
                $('#grafico2').html(response);
                $('#spinner_preguntas').addClass('visually-hidden');
                // $('#grafico2').empty();
            },
            complete : function(response){
                // $('#grafico2').hide().appendTo('#graficos').show("slide", { direction: "left" }, 1000);
            },
            error:function(){
                console.log('Error')
            }
        });
    });

    $(document).on('change', '#select_categorias', function(){

        $("#nombreCategoria").val($( "#select_categorias option:selected" ).text());
        $.ajax({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: $('#form_categorias').attr("action"),
            type: "POST",
            data: $('#form_categorias').serialize(),
            beforeSend: function () {
                $('#spinner_categorias').removeClass('visually-hidden');
            },
            success:  function (response, params) {
                $('#grafico1').html(response);
                $('#spinner_categorias').addClass('visually-hidden');
                $('#preguntas_categoria').empty();
            },
            complete : function(response){
                $('#form_preguntas').hide().appendTo('#preguntas_categoria').show("slide", { direction: "left" }, 1000);
            },
            error:function(){
                console.log('Error')
            }
        });
    });

    $('#select_formularios').change(function (params) {
        $.ajax({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: $('#form_formularios').attr("action"),
            type: "POST",
            data: $('#form_formularios').serialize(),
            beforeSend: function () {
                $('#spinner_formularios').removeClass('visually-hidden');
            },
            success:  function (response) {
                $('#categorias_formulario').hide().html(response).show("slide", { direction: "left" }, 1000);;
                $('#spinner_formularios').addClass('visually-hidden');
            },
            error:function(){
                console.log('Error')
            }
        });
    });
</script>


