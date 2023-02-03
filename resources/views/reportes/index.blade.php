{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Reportes')

@section('content')

<div class="container m-sm-3 p-sm-4 p-0" style="overflow-x:auto;">
    <h2 class="text-center text-primary py-3">Reportes</h2>

    <div data-bs-toggle="modal" data-bs-target="#resportesModal" class="row d-flex btn bg-secondary mt-2 rounded-3 reporte" id="reporte_1" >1. Cantidad de personas entrevistadas por sexo</div>
    <div data-bs-toggle="modal" data-bs-target="#resportesModal" class="row d-flex btn bg-secondary mt-2 rounded-3 reporte" id="reporte_2" >2. Cantidad de visitas realizadas en un rango de fechas</div>
    <div data-bs-toggle="modal" data-bs-target="#resportesModal" class="row d-flex btn bg-secondary mt-2 rounded-3 reporte" id="reporte_3" >3. Cantidad y tipos de establecimientos visitados</div>
    <div data-bs-toggle="modal" data-bs-target="#resportesModal" class="row d-flex btn bg-secondary mt-2 rounded-3 reporte" id="reporte_4" >4. Cantidad de formularios aplicados por establecimiento</div>


    {{-- <!-- btn Modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#resportesModal">
        Launch demo modal
    </button> --}}

    <!-- Modal -->
    <div class="modal fade" id="resportesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="resportesModalLabel">Reporte</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="/img/menatwork.jpg" alt="">
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary">Aceptar</button>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.reporte', function(e){
        let id = $(this).attr('id').replace(/[^0-9]/g,'');
        console.log(id);
    });
</script>
@endsection
