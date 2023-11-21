@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')


<div class="container p-4">


        <div class="card  mb-3" >
            <div class="card-header bg-transparent ">Acta de visita
                <p><strong>Adicione la imagen o pdf del Acta de Visita</strong></p>
            </div>

            @if ( $visita[0]['VIS_urlActa']  )
                <div class="card-body">
                    <object data='{{Request::root().'/'.$visita[0]['VIS_urlActa']}}'
                    type='application/pdf'
                    width='100%'
                    height='400px'
                    class="object-fit-contain border rounded">
                </div>
            @else
                <form method="Post" action="/visita/guardarActaVisita" id="form_guarda_acta" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            {{-- {{$VIS_id}} --}}
                            <label for="formFile" class="form-label">Seleccione un archivo:</label>
                            <input class="form-control" type="file" id="acta_visita" name="VIS_acta">
                            <small class="text-danger error" id="VIS_acta_err"></small>
                            {{-- <input type="hidden" name="EST_id" value="{{ $formularios[0]->EST_id }}">--}}
                            <input type="hidden" name="VIS_id" value="{{$VIS_id}}">
                            @error('VIS_acta')
                                <small class="text-danger error">*{{$message}}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer bg-transparent ">
                        <button type="submit" id="btn_guarda_archivo" class="btn btn-success box-shadow text-shadow text-white"> Guardar archivo</button>

                        <span class="d-none btn btn-primary box-shadow text-shadow text-white" role="button" id="btn_cargando" disabled>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            Cargando...
                        </span>
                    </div>
                </form>
            @endif

        </div>

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
@endsection
