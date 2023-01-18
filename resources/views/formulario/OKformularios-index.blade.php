@extends('layouts.app')
@section('title', 'Formularios')

@section('content')

{{-- <div class="position-relative">
    @if(Session::has('success'))
        <div class="col-3 alert alert-success alert-dismissible notification" role="alert">
            <strong>{{Session::get('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(Session::has('warning'))
        <div class="col-3 alert alert-warning alert-dismissible notification" role="alert">
            <strong>{{Session::get('warning') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div> --}}

<div class="my-5 p-5 bg-withe" >

    <div class="text-center" >
        <h2 class="text-primary fs-2">Formularios</h2>
    </div>

    <div class="col text-center" id="div_formulario_nuevo">
          <button type="button" class="btn btn-success " data-bs-toggle="modal" data-bs-target="#nuevoFormulario" id="btn_formulario_nuevo">
            Nuevo Formulario
          </button>
    </div>
    <div id="formularios">
        @include('formulario.formularios-responses')
    </div>
  </div>
        @include('formulario.formulario-nuevo')
    <script type="text/javascript">
        $(document).ready( function () {
            $('#btn_formulario_nuevo').click(function (){
                $("#frm-formulario-nuevo")[0].reset();
            });

            $('#formularios #table_formularios_wrapper .row:first-child').append($('#div_formulario_nuevo'))
        })
    </script>

@endsection
