{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'CAtegorías')

@section('content')
{{-- @dump( $categorias->toArray() ); --}}
@php
    $elementos = []; $i='';
    // dump( $categorias->toArray() );
    foreach ($categorias as $k=>$categoria){
        // if( $categoria->FK_CAT_id2 == null ){
        //     array_push($elementos, ['CAT_categoria' => $categoria->CAT_categoria]);
        // }

        if( $i != $categoria->CAT_id ){
            array_push( $elementos, ['CAT_id' => $categoria->CAT_id,  'CAT_categoria' => $categoria->CAT_categoria] );
        }
            // dump($categoria->CAT_id);
        $i = $categoria->CAT_id;
    }

    // dump($elementos);
@endphp

<div class="container m-sm-3 p-sm-4 p-0" style="overflow-x:auto;">
    <h2 class="text-center text-primary py-3">Categorías</h2>
    <table class="p-3 table table-border table-hover table-responsive-lg bg-light border" id="categorias">
        <thead>
            <tr>
                <th>#</th>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categorias as $k=>$categoria)
            <tr>
                <td>{{$k+1}}</td>
                <td>{{ $categoria->CAT_categoria }}</td>
                <td>{{ $categoria->subcategoria }}</td>
                <td>...</td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>

<!-- Modal PARA agregar nueva categoria/subcategoria-->
    <div class="modal fade modal-fullscreen" id="modal_nueva_categoria"  data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Nueva categoría</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="form_nueva_categoria" >@csrf
                    <div class="">
                        <label class="form-label">Tipo de elemento:</label>
                        <select class="form-select" name="tipo_elemento" id="tipo_elemento">
                            <option selected value="">Seleccione...</option>
                            <option value="Categoría">Categoría</option>
                            <option value="Subcategoría">Subcategoría</option>
                        </select>
                        <small class="error text-danger" id="tipo_elemento_err" ></small>
                    </div>
                    <div class="py-2">
                        <label class="form-label">Categoría</label>
                        <select class="form-select" name="FK_CAT_id" id="FK_CAT_id" disabled>
                            <option selected value="">Seleccione...</option>
                            @foreach ($elementos as $cat)
                                <option value="{{ $cat['CAT_id'] }}">{{$cat['CAT_categoria']}}</option>
                            @endforeach
                        </select>
                        <small class="error text-danger" id="FK_CAT_id_err" ></small>
                    </div>
                    <div class="">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="CAT_categoria" name="CAT_categoria">
                        <small class="error text-danger" id="CAT_categoria_err" ></small>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
            <span class="btn btn-warning text-light text-shadow" data-bs-dismiss="modal">Cancelar</span>
            <span class="btn btn-success text-light text-shadow" id="btn_guarda_categoria">Guardar</span>
            </div>
        </div>
        </div>
    </div>
    {{--  --}}
<script>


    $(document).ready(function () {
        $('#categorias').DataTable();
        $('div#categorias_wrapper div').first().append("<div id='btn_1' class='col text-center' data-bs-toggle='modal' data-bs-target='#modal_nueva_categoria' > <span class='btn btn-sm btn-primary text-light text-shadow' >Nueva categoría</span> </div>")

        $('#tipo_elemento').change(function(){
            if( $(this).val() == 'Categoría' ){
                $('#FK_CAT_id').prop('disabled', true);
                $('#FK_CAT_id').val( '' );
                // console.log( $('#FK_CAT_id').val('') );
            } else {
                $('#FK_CAT_id').prop('disabled', false);
            }
        });

        $("#btn_guarda_categoria").click( function(e){
            // console.log('xxxx');
            $.ajax({
                async: true,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: "categorias/guardaNuevaCategoria",
                type: 'post',
                data: $("#form_nueva_categoria").serialize(),
                beforeSend: function () { },
                success: function (data, response) {
                    $('small.error').empty();
                    jQuery.each(data.errors, function( key, value ){
                        $('#'+key+'_err').append( value );
                    });
                    if(!data.errors){
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                        });
                        setTimeout(function(){ location.reload() }, 2000);
                    }
                },
                error: function(response){ console.log(response) }
            });
        });


    });
// categorias.guardaNuevaCategoria
</script>

@endsection

