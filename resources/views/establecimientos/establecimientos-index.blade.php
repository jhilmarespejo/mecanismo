@extends('layouts.app')
@section('title', 'Establecimientos')


@section('content')
<div id="establecimientos">
    <div class="spinner-border" role="status"> </div>
    Cargando...
</div>

<script>
    $( '#establecimientos' ).ready(function() {
        listar();
    });
    function listar( id = null, tipo = null ){
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "/establecimientos/listar",
            type: 'post',
            data: {accion: 'cargar_lista', FK_TES_id: id, tipo_establecimiento: tipo},
            // data: data,
            beforeSend: function () { },
            success: function (data, response) {
                $('#establecimientos').html(data);
            },
            error: function(response){ console.log(response) }
        });
    }
    $(document).on('change', '#cbo_tipos', function(e){
        listar($(this).val());
    });

</script>
@endsection
{{--  --}}
