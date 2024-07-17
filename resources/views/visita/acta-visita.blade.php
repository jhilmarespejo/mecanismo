@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')


<div class="container p-4">

    <h4 class="text-center">Acta de visita</h4>
{{-- @php
    dump();exit;
@endphp --}}
    @if ( !empty($visita) )
    <div class="card mb-3">
        <div class="card-body">
            <div>
                <object data='{{Request::root().'/'.$visita[0]['ARC_ruta']}}'
                type=''
                width='100%'
                height='400px'
                class="object-fit-contain border rounded"></object>
            </div>
        </div>
    </div>
    @else
    <div class="card mb-3">
        <div class="card-body">
            <form method="Post" action="/visita/guardarActaVisita" id="form_guarda_acta" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="formFile" class="form-label">Seleccione un archivo:</label>
                    <input class="form-control" type="file" accept="image/*" capture="camera" name="VIS_acta" id="acta_visita" >
                    <small class="text-danger error" id="VIS_acta_err"></small>
                    <input type="hidden" name="VIS_id" value="{{$VIS_id}}">
                    @error('VIS_acta')
                        <small class="text-danger error">*{{$message}}</small>
                    @enderror
                </div>

                <div class="XXcard-footer bg-transparent ">
                    <button type="submit" id="btn_guarda_archivo" class="btn btn-success box-shadow text-shadow text-white"> Guardar archivo</button>

                    <span class="d-none btn btn-primary box-shadow text-shadow text-white" role="button" id="btn_cargando" disabled>
                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                        Cargando...
                    </span>
                </div>
            </form>
        </div>
    </div>
    @endif



</div>

@endsection

@section('js')


    <script>
        $('#btn_guarda_archivo').click(function (e) {
            $(this).addClass('d-none');
            $('#btn_cargando').removeClass('d-none');
        });
    </script>
    @if (Session::has('success'))
        <script>
            Swal.fire(
                '{{Session::get('success') }}',
            )
        </script>
    @endif
    @if(Session::has('warning'))
        <script>
            Swal.fire(
                '{{Session::get('warning') }}',
            )
        </script>

    @endif

    <script>
        $(document).on('click', '.nuevo-archivo', function(){
        //let id = $(this).attr('id').replace(/[^0-9]/g,'');
        $(".archivos").append(`
        <div class="input-group input-group-sm p-1 adjunto" id="adjunto_`+j+`">
            <input type="file" class="form-control input-archivo" id="archivo_`+j+`" name="REC_archivo[]" accept="image/*, video/*,.pdf,.mp3,.ogg,.acc,.flac,.wav,.xls,.xlsx,.ppt,.pptx,.doc,.docx" capture />
            <span class="input-group-text">Descripción:</span>
            <input type="text" class="form-control" id="descripcion_`+j+`" name="ARC_descripcion[]">
            <span class="input-group-text btn-danger text-light rounded remover-adjunto" id="remover_adjunto_`+j+`">
            <i class="text-dark bi bi-trash"></i> </span>
            <div class="container row">
                <small class="text-danger col" id="archivo_`+j+`_err"></small>
                <small class="text-danger col" id="descripcion_`+j+`_err"></small>
            </div>
        </div>`);
        ++j;
    });
    </script>
@endsection
