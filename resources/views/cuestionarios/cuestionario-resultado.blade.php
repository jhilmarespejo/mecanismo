
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
                @php $c=1;  @endphp
                @foreach ($resultados as $clave => $resultado)
                    <p class="text-start"><b>{{$contadorCategoria.'. '. $clave }}</b></p>
                    <div class="container ps-5">
                        @foreach ( $resultado as $clave2 => $pregunta )
                        <table id="resultados_{{$clave2}}" class="text-start ps-5 table table-responsive table-hover">
                            @if($pregunta['BCP_tipoRespuesta'] == "Casilla verificación" || $pregunta['BCP_tipoRespuesta'] == "Lista desplegable" || $pregunta['BCP_tipoRespuesta'] == "Afirmación" )
                                <thead class="table-primary">
                                    <tr>
                                        <th class="col-8">
                                            {{$c}}.
                                            {{ $pregunta['BCP_pregunta'] }}
                                        </th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end"> % </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $respuestParcial=0; @endphp
                                    @foreach ($pregunta['respuestas'] as $clave3 => $respuestaCerrada)
                                        @php
                                            $respuestParcial = $respuestaCerrada + $respuestParcial;
                                        @endphp
                                        <tr>
                                            <td>{{ $clave3 }}</td>
                                            @if ( !is_null($respuestaCerrada) )
                                                <td class="text-end">
                                                    {{$respuestaCerrada}}
                                                </td>
                                                <td class="text-end">{{round(($respuestaCerrada/$total)*100, 0) }} %</td>
                                            @else
                                            <td class="text-end">-sin respuestas- </td>
                                                <td class="text-end">-</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    @if ( $respuestParcial < $total )
                                        <tr>
                                            <td>-- Sin respuesta -- </td>
                                            @if (!empty($pregunta['respuestas']) && !is_null($respuestaCerrada) )
                                                <td class="text-end">{{ $total- $respuestParcial }}</td>
                                                <td class="text-end">{{round((($total- $respuestParcial)/$total)*100, 0) }} %</td>
                                            @endif
                                        </tr>
                                    @endif

                                </tbody>

                                <tfoot class="table-warning border-top">
                                    <tr>
                                        @if ( !empty($pregunta['respuestas']) && !is_null($respuestaCerrada) )
                                            <td><b>TOTAL</b></td>
                                            <td class="text-end"><b>{{$total}}</b></td>
                                            <td class="text-end"> <b>100%</b></td>
                                        @endif
                                    </tr>
                                    <hr>
                                </tfoot>
                            @endif
                            @if($pregunta['BCP_tipoRespuesta'] == "Numeral" || $pregunta['BCP_tipoRespuesta'] == "Respuesta corta" || $pregunta['BCP_tipoRespuesta'] == "Respuesta larga" )
                                <thead class="table-info" style="cursor:pointer">
                                    <tr class="accordion-header" id="flush-heading-{{$pregunta['RBF_id']}}" >
                                        <th class="col-4 collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapse-{{$pregunta['RBF_id']}}"  aria-expanded="false" aria-controls="flush-collapse-{{$pregunta['RBF_id']}}" >
                                            <span class="text-start">{{$c}}. {{ $pregunta['BCP_pregunta'] }}</span>
                                            <span class="text-end"><i class="bi bi-caret-down-fill"></i></span>
                                        </th>
                                    </tr>

                                </thead>
                                <tbody>
                                    @foreach ($pregunta['respuestas'] as $clave4 => $respuestaAbierta)
                                    <tr id="flush-collapse-{{$pregunta['RBF_id']}}" class=" collapse" aria-labelledby="flush-heading-{{$pregunta['RBF_id']}}">
                                        @if ( empty($respuestaAbierta['respuesta']))
                                            <td class="ps-5">-- Sin respuestas --</td>
                                        @else
                                            <td class="ps-5"><b style="font-size: 10px">{{$clave4+1}}</b>.&nbsp;&nbsp;&nbsp;&nbsp;
                                                <a href="/cuestionario/responder/{{$FRM_id}}/{{$respuestaAbierta['FK_AGF_id']}}">{{$respuestaAbierta['respuesta']}}</a>
                                            </td>
                                        @endif
                                    </tr>
                                    @endforeach

                                </tbody>
                            @endif


                        </table>
                        @php $c++;  @endphp
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

