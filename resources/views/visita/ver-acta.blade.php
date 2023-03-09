@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')
{{-- <div class="modal fade" id="modal_acta" tabindex="-1" aria-labelledby="actaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="actaLabel">Acta de visita no anunciada</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            @if ( $ulrActa )
            <object data='{{Request::root().'/'.$ulrActa}}'
                type='application/pdf'
                width='100%'
                height='400px'>
            @else
                <form method="post" id="form_guarda_acta">
                    <div class="mb-3">
                        {{$VIS_id}}
                        <label for="formFile" class="form-label">Acta de visita:</label>
                        <input class="form-control" type="file" id="acta_visita" name="VIS_acta">
                        <small class="text-danger error" id="VIS_acta_err"></small>
                        <input type="hidden" name="EST_id" value="{{ $formularios[0]->EST_id }}">
                        <input type="hidden" name="VIS_id" value="{{$VIS_id}}">

                    </div>
                </form>
            @endif

        </div>
        @if ( !$ulrActa )
            <div class="modal-footer">
            <button type="button" class="btn btn-danger text-white text-shadow box-shadow" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" id="btn_guarda_acta" class="btn btn-success text-white text-shadow box-shadow">Guardar archivo</button>
            <button class="btn btn-primary d-none box-shadow text-shadow text-light" type="button" disabled id="btn_cargando">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Cargando...
            </button>
            </div>
        @endif
      </div>
    </div>
</div> --}}

    @php
        // dump( $urlActa );
        // exit;
    @endphp


    <div class="container p-4">
        <div class="card">
            <h5 class="card-header">Acta de visita no anunciada</h5>
            @if ( $urlActa )
                <div class="card-body">
                    <object data='{{Request::root().'/'.$urlActa}}'
                    type='application/pdf'
                    width='100%'
                    height='400px'>
                </div>
            @else
            <div class="car-body">
                <form method="post" id="form_guarda_acta" action="/visita/guardarActaVisita">
                    @csrf
                    <div class="mb-3 p-3" >
                        <label for="formFile" class="form-label">Seleccione un archivo:</label>
                        <input class="form-control" type="file" id="acta_visita" name="VIS_acta">
                        <input type="hidden" name="EST_id" value="{{ $estId }}">
                        <input type="hidden" name="VIS_id" value="{{ $VIS_id }}">

                        @error('VIS_acta')
                            <small class="text-danger error">*{{$message}}</small>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="card-footer bg-transparent border-success">

                <span role="button" id="btn_guarda_acta" class="btn btn-success text-white text-shadow box-shadow">Guardar archivo</span>
                <span class="btn btn-primary d-none box-shadow text-shadow text-light" role="button" disabled id="btn_cargando">
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                    Cargando...
                </span>
            </div>
            @endif

        </div>
    </div>

    <script>
        $(document).on('click', '#btn_guarda_acta', function(e){
            $('#form_guarda_acta').submit();

            // e.preventDefault();
            // let formData = new FormData($('#form_guarda_acta')[0]);
            // $.ajax({
            //     async: true,
            //     headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            //     url: "/visita/guardarActaVisita",
            //     type: 'POST',
            //     data: formData,//$('#recomendaciones_form').serialize(),
            //     contentType: false,
            //     processData: false,
            //     beforeSend: function () {},
            //     success: function (data, response) {

            //         $('small.error').empty();
            //         console.log(data.errors);
            //         jQuery.each(data.errors, function(key, value){
            //             $('#'+key+'_err').append( '<p>'+value+'</p>' );
            //         });

            //         if(!data.errors){
            //             $('#btn_cargando').removeClass('d-none');
            //             $('#btn_guarda_acta').addClass('d-none');

            //             // Swal.fire({
            //             //     icon: 'success',
            //             //     title: data.message,
            //             //     showConfirmButton: false,
            //             // });

            //             // setTimeout(function(){ location.reload() }, 2000);
            //         }
            //     },
            //     error: function(response){  }
            // });
        });
    </script>

@endsection
