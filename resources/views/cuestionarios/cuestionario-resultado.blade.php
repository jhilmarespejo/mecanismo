
@extends('layouts.app')
@section('title', 'Cuestionario')


@section('content')

<div class="container-fluid p-sm-3 p-0 mx-0" id="cuestionario" >

        @desktop
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="nav2">
            <div class="container-fluid">
              <div class="collapse navbar-collapse border-bottom p-1" id="navbarNav">
                <ul class="navbar-nav" id="nav_2">
                    <li class="nav-item p-1 px-3">
                        <a href="javascript:history.back()" role="button" class="text-decoration-none"> <i class="bi bi-arrow-90deg-left"></i> Atrás</a>
                    </li>
                    {{-- <li class="nav-item p-1 px-3" id="btn_imprimir">
                        <a class="text-decoration-none" href="/cuestionario/imprimir/{{$FRM_id}}/{{$AGF_id}}" >
                            <i class="bi bi-printer"></i> Imprimir</span>
                        </a>
                    </li> --}}
                </ul>
              </div>
            </div>
        </nav>
        @endmobile
        {{-- Encabezado --}}
        <div class="text-center head">
            <p class="text-primary m-0 p-0 fs-3" id="titulo">{{ session('VIS_tipo') }}</p>
            <p class="m-0 p-0 fs-3" id="establecimiento">{{ session('EST_nombre') }}</p>
            <p class="text-primary m-0 p-0 fs-5" id="titulo">{{ $FRM_titulo }}</p>

            @if ($resultados == null)
            <div class="alert alert-danger" role="alert">
                No se aplicó el formulario!!
            </div>
            @else
                {{-- @dump( $resultados) --}}
                @php $contadorCategoria=1;  @endphp
                @foreach ($resultados as $clave => $resultado)
                    <p class="text-start"><b>{{$contadorCategoria.'. '. $clave }}</b></p>
                    <div class="container ps-5">
                        @foreach ( $resultado as $clave2 => $item )
                        <table id="resultados_{{$clave2}}" class="text-start ps-5 table table-responsive table-hover">
                            <thead class="table-primary">
                            <tr>
                                <th class="col-8">
                                    {{ $item['BCP_pregunta'] }}
                                </th>
                                <th>Cantidad</th>
                                <th> % </th>
                            </tr>
                            </thead>
                            <tbody>
                                @php $respuestParcial=0; @endphp
                                @foreach ($item['respuestas'] as $clave3 => $respuesta)
                                @php
                                    $respuestParcial = $respuesta + $respuestParcial;
                                @endphp
                                <tr>
                                    <td>{{ $clave3 }}</td>
                                    <td>{{ $respuesta }}</td>
                                    <td>{{round(($respuesta/$total)*100, 0) }} %</td>
                                </tr>
                                @endforeach
                                @dump($respuestParcial)
                                @if ($respuestParcial < $total)
                                <tr>
                                    <td>-- Sin respuesta -- </td>
                                    <td>{{ $total- $respuestParcial }}</td>
                                    <td>{{round((($total- $respuestParcial)/$total)*100, 0) }} %</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-warning border-top">
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td><b>{{$total}}</b></td>
                                    <td> <b>100%</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    @endforeach
                    </div>
                    @php $contadorCategoria++;  @endphp
                @endforeach

            @endif
        </div>

</div> {{-- /container --}}


<script>


$(document).ready( function () {
    // let table = new DataTable('.ttable');
    // table.on('click', 'tbody tr', function () { let data = table.row(this).data(); alert('You clicked on ' + data[0] + "'s row"); });
})
</script>

@endsection

