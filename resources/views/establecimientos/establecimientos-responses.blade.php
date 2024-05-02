@if (isset($establecimientos))

    <div class="text-center py-4" >
        <h2 class="text-primary fs-2 ">Establecimientos</h2>
    </div>

    @if (count($establecimientos))
        <table class="table table-responsive table-hover bg-light p-4" id="tabla_establecimientos">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre </th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Departamento</th>
                    <th scope="col">Municipio</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($establecimientos as $key=>$establecimiento)
                    <tr>
                        <th></th>
                        <td> <a class="text-decoration-none" href="/visita/historial/{{$establecimiento->EST_id}}">{{ $establecimiento->EST_nombre }}</a></td>
                        <td>{{ $establecimiento->TES_tipo }}</td>
                        <td>{{ $establecimiento->EST_departamento }}</td>
                        <td>{{ $establecimiento->EST_municipio }}</td>
                       
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-danger" role="alert">
            Sin resultados para la búsqueda
        </div>
    @endif
@endif


{{-- Controles adicionales --}}
<div class="col-sm pt-2-sm" id="tipos_establecimientos">
    <select class="form-select form-select-sm" id="cbo_tipos">
        <option disabled>Seleccione...</option>
        <option value="todo"  >Todos los establecimientos</option>

        @foreach ($tiposEstablecimientos as $establecimiento)
            <option value="{{$establecimiento->TES_id}}" {{ ($FK_TES_id == $establecimiento->TES_id)? 'selected' : ''}}>{{ $establecimiento->TES_tipo }}</option>
        @endforeach
    </select>
</div>

        @if(Auth::user()->rol == 'Administrador' )
            <div id='nuevo_establecimiento' class='col-sm pt-2-sm text-center' data-bs-toggle='modal' data-bs-target='#modal_nuevo_establecimiento' >
                <span class='btn btn-sm btn-primary text-light text-shadow'>Nuevo establecimiento</span>
            </div>

            {{-- MODAL PARA NUEVO ESTABLECIMIENTO --}}
            <div class="modal fade" id="modal_nuevo_establecimiento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo establecimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form_nuevo_establecimiento">
                            @csrf
                            <div class="mb-3">
                            <label class="form-label">Nombre del establecimiento</label>
                            <input type="text" class="form-control" name="EST_nombre">
                            <small class="error text-danger" id="EST_nombre_err" ></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Departamento</label>
                                <select class="form-select" name="EST_departamento" >
                                    <option value="" selected>Seleccione ...</option>
                                    <option value="La Paz">La Paz</option>
                                    <option value="Oruro">Oruro</option>
                                    <option value="Potosí">Potosí</option>
                                    <option value="Cochabamba">Cochabamba</option>
                                    <option value="Tarija">Tarija</option>
                                    <option value="Chuquisaca">Chuquisaca</option>
                                    <option value="Pando">Pando</option>
                                    <option value="Beni">Beni</option>
                                    <option value="">Santa Cruz</option>
                                </select>
                                <small class="error text-danger" id="EST_departamento_err" ></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Municipio</label>
                                <input type="text" class="form-control" name="EST_municipio">
                                <small class="error text-danger" id="EST_municipio_err" ></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono de contacto</label>
                                <input type="number" min="20000000" max="79999999" class="form-control" name="EST_telefonoContacto">
                                <small class="error text-danger" id="EST_telefonoContacto_err" ></small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de establecimiento</label>
                                <select class="form-select" name="FK_TES_id" >
                                    <option value="" selected>Seleccione ...</option>
                                    @foreach ($tiposEstablecimientos as $estab)
                                        <option value="{{ $estab->TES_id }}">{{ $estab->TES_tipo }}</option>
                                    @endforeach
                                </select>
                                <small class="error text-danger" id="FK_TES_id_err" ></small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <span class="btn btn-danger text-shadow text-light" data-bs-dismiss="modal">Cerrar</span>
                    <span class="btn btn-success text-shadow text-light" id="btn_guardar">Guardar datos</span>
                    </div>
                </div>
                </div>
            </div>
        @endif

<script>
    $(document).on('click', '#btn_guardar', function(e){
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "establecimientos/guardarNuevoEstablecimiento",
            type: 'post',
            data: $("#form_nuevo_establecimiento").serialize(),
            beforeSend: function () { },
            success: function (data, response) {
                $('small.error').empty();
                jQuery.each(data.errors, function( key, value ){
                    $('#'+key+'_err').append( value );
                });
                if(!data.errors){
                    Swal.fire({
                        text: 'Agregado correctamente',
                        icon: 'success',
                        confirmButtonText: 'ok!'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload()
                        }
                    })
                    // console.log('data.errors');
                    /*Swal.fire({
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                    });
                    setTimeout(function(){ location.reload() }, 2000);*/
                }
            },
            error: function(response){ console.log(response) }
        });
    });


    $(document).ready( function () {
        var t = $('#tabla_establecimientos').DataTable({
            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    targets: 0,
                },
            ],
            order: [[1, 'asc']],
        });
        t.on('order.dt search.dt', function () {
            let i = 1;

            t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                this.data(i++);
            });
        }).draw();
        $('div#tabla_establecimientos_wrapper div').first().append($('#tipos_establecimientos, #nuevo_establecimiento'));
    } );

</script>
