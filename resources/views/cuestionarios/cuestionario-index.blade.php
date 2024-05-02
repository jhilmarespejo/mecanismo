
@extends('layouts.app')
@section('title', 'Cuestionario')


@section('content')
<script>
    $( '.item' ).draggable({
        helper: 'clone'
    });

    $( '.day, .x' ).droppable({
        accept: '.item',
        hoverClass: 'hovering',
        drop: function( ev, ui ) {
            ui.draggable.detach();
            $( this ).append( ui.draggable );
        }
    });
</script>




<div class="container">
    {{-- <form action="{{ route('cuestionario.store') }}" method="Post" id="frm_cuestionario_nuevo"> --}}
        {{-- <button type="submit" class="btn btn-success">Guardar</button> --}}
    {{-- </form> --}}
    {{-- @csrf --}}
    <div class="col mx-2 my-2 p-2" id="cuestionario">
        <h2 class="text-primary fs-2 text-center">Editor de cuestionarios</h2>

        <input class="form-control" type="text" id="buscador_preguntas" placeholder="Buscar preguntas" list="lista_preguntas">
        <datalist id="lista_preguntas" class="bg-dark"></datalist>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#buscador_preguntas').on('keyup', function() {
            var q = $(this).val();

            if (q.length >= 2) {
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    url: "{{ route('buscarPreguntas') }}",
                    method: 'POST',
                    data: { q: q },
                    dataType: 'json',
                    success: function(response) {
                        var resultados = '';
                        response.forEach(function(pregunta) {
                            resultados += '<option value="' + pregunta.BCP_pregunta + '">';
                            // resultados = '<option value="' + pregunta.BCP_pregunta + ' | Categoría:'+pregunta.categoria+' | Subcategoria: '+pregunta.subcategoria+'">';


                        });
                        $('#lista_preguntas').html(resultados);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    });
</script>




@endsection
