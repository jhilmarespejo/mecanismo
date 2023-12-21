
@extends('layouts.app')
@section('title', 'Panel')


@section('content')
{{-- <link rel="stylesheet" href="/tinycarousel/tinycarousel.css" type="text/css" media="screen"/> --}}
{{-- <script type="text/javascript" src="/tinycarousel/jquery.tinycarousel.min.js"></script> --}}

<script src="charts/highcharts.js"></script>
<script src="charts/exporting.js"></script>
<script src="charts/export-data.js"></script>
{{-- <script src="charts/variable-pie.js"></script> --}}
<script src="charts/accessibility.js"></script>

{{-- <div class="py-12 container"> --}}
{{-- <div class="mx-0 text-center p-3 container-fluid"> --}}
<div class="container">
    <h4 class="mt-4 text-center py-4">Estadísticas</h4>
    {{-- <div class="row">
        @include('index.recomendaciones')
    </div> --}}

    <div class="row sm-m-2 m-2">
        <div class="col-sm-6 border">@include('index.mdl-visitas')</div>
        <div class="col-sm-6 border">@include('index.mdl-tipos')</div>
    </div>
    <div class="row m-2">
        <div class="col-sm-6 border">@include('index.mdl-entrevistados')</div>
        <div class="col-sm-6 border">@include('index.mdl-hacinamiento')</div>
    </div>

    {{-- <div class="row mt-4">
        @include('index.formularios')
    </div> --}}

    {{-- CONSULTA DINAMICA --}}
    <div class="row mt-4">
        @include('index.dinamico')
    </div>
</div>
{{-- </div> --}}

{{-- M O D A L S --}}
<!-- Modal -->
{{-- <div class="modal fade" id="modal_1" tabindex="-1" aria-hidden="true">
    @include('index.mdl-visitas')
</div>
<div class="modal fade" id="modal_2" tabindex="-1" aria-hidden="true">
    @include('index.mdl-tipos')
</div>
<div class="modal fade" id="modal_3" tabindex="-1" aria-hidden="true">
    @include('index.mdl-entrevistados')
</div> --}}



<script type="text/javascript">
    // $(document).ready(function(){
    //     $('.slider').tinycarousel();
    // });
</script>

@endsection

{{--
    1. Cantida de lugares visitados
    2. Desagregado por tipos de lugares visitados
    3. Cantidad de entrevistados por sexo
    4. Cantidad de visitas
    5. Tipos de visitas:
        5.1. visitas en profundidad se ve todo, personal
        5.2 visitas tematicas, tema esoecifico como extorsion o ascinamiento
        5.3. Visitas de seguimiento general mente depues de las visitas en profundidad en funcion a las recomendaciones realizadas en las visitas de profundidad
        5.4. Visitas Adhoc. Visitas no planificadas cuando sucede un evento mayor como un incendio o algo fortuito, que realiza
        5.5 Visitas reactivas. Despues de una queja realizada por un ppl, familiar u ong


CANTIDAD DE VISITAS con el cuadro word
TIPOS DE VISITAS
CANTIDAD DE ENTREVISTADOS
--}}
{{-- penitenciarias y carceletas 21

celdas policiales 10 --}}
