<button type="button" class="my-2 btn btn-primary text-light text-shadow box-shadow" data-bs-toggle="modal" data-bs-target="#nuevoFormulario">
    <i class="bi bi-clipboard-plus"></i> Agregar formulario
</button>

<!-- Modal -->
<div class="modal fade" id="nuevoFormulario" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Nuevo Formulario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="Post" id="frm_formulario_nuevo" action="/formulario/store">
            {{-- @csrf --}}
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label" >Establecimiento</label>
                    <input class="form-control" list="establecimientos" value="{{$EST_nombre}}" disabled>
                    <input type="hidden" name="FK_EST_id" id="FK_EST_id" value="{{$EST_id}}">

                </div>
                <div class="mb-4">
                    <label class="form-label">Título del formulario</label>
                    <input class="form-control" type="text" name="FRM_titulo" max="100" min="0">
                    <small class="text-danger" id="FRM_titulo_err"></small>
                </div>
                <div class="mb-4">
                    <label class="form-label">Número de formulario</label>
                    <input class="form-control" type="number" name="FRM_version" max="100" min="0">
                    <small class="text-danger" id="FRM_version_err"></small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Fecha</label>
                    <input class="form-control" type="date" name="FRM_fecha" max="100" min="0">
                    <small class="text-danger" id="FRM_fecha_err"></small>
                </div>


            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-formulario-cancelar">Cancelar</button>
            <button type="submit" class="btn btn-success" id="btn-formulario-nuevo">Guardar</button>
            </div>
        </form>

      </div>
    </div>
</div>

  <script type="text/javascript">

    $(document).ready( function () {
        $("#btn-formulario-nuevo").click(function(e){
                e.preventDefault();
            $('small').empty();
                $.ajax({
                    async: true,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    url: $('#frm_formulario_nuevo').attr("action"),
                    type: 'post',
                    data: $('#frm_formulario_nuevo').serialize(),
                    beforeSend: function () { },
                    success: function (data, response) {
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
                    error: function(response){ }
                });
        });
    });
</script>
