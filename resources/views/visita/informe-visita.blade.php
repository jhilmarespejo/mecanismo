@extends('layouts.app')
@section('title', 'Cuestionario')

@section('content')


<br>
<div class="container p-3 " style="">
    <div class="card mb-3 " >
        <div class="card-body elemento-word" style="font-family: Arial; font-size:0.9em">
            <div style="text-align: center; font-family: Arial; font-size:0.9em">
                <img src="/img/logoinforme.png" alt="Logo Defensoría del Pueblo">
                <div> INFORME </div>
                <div>INF/DP/CMPT/2023/...</div>
            </div>
            <br>
            <table style="font-family: Arial; font-size:0.9em" >
                <tr>
                    <td class="col-4"><strong>A</strong></td>
                    <td class="">:
                        {{-- <span class="m-0 p-0"><< NOMBRE COMPLETO >></span> --}}
                        <br>
                    </td>
                </tr>
                <tr></tr>
                <tr>
                    <td class="col-4"><strong>DE</strong></td>
                    <td class="">:
                        <span class="m-0 p-0">{{ Auth::user()->name }}</span><br>
                    </td>
                </tr>
                <tr></tr>
                <tr>
                    <td class="col-4"><strong>REFERENCIA</strong></td>
                    <td class="">:
                        <span>{{ $referencia }}</span>
                    </td>
                </tr>
                <tr></tr>
                <tr>
                    <td class="col-4"><strong>FECHA</strong></td>
                    <td class="">:
                        <span class="m-0 p-0"> {{date('d-m-Y')}} </span><br>
                    </td>
                </tr>
            </table>
            <hr>

            <ol style="font-family: Arial; font-size:0.9em">
                <li >
                    <h5 ><strong>ANTECEDENTES.</strong></h5>
                    <p>La Defensoría del Pueblo tiene un nuevo mandato como Mecanismo Nacional de Prevención de la Tortura del Estado Plurinacional de Bolivia (MNP), en cumplimiento de la Ley N° 1397 de 29 de septiembre de 2021 y el Protocolo Facultativo de la Convención Contra la Tortura y otros Tratos o Penas Crueles, Inhumanos o Degradantes, ratificado por Ley N° 3298 de 12 de diciembre de 2005. </p>

                    <p>Su función principal como MNP es visitar los lugares de privación de libertad y examinar las condiciones y el tratamiento de las personas privadas de libertad. En base a los problemas identificados en esas visitas, se realizan recomendaciones a las autoridades competentes para que se realicen acciones para solucionar problemas identificados.</p>
                </li>
                <li>
                    <h5><strong>DESARROLLO DE LA VISITA</strong></h5>
                    <p>El 3 de marzo de 2023 la Delegación Defensorial realizó un ingreso simultáneo a centros penitenciarios a nivel nacional, visitando:</p>
                    <p>* {{$datos->toArray()[0]['TES_tipo'].' de '. $datos->toArray()[0]['EST_nombre'] }}.</p>
                </li>
                {{-- @dump($preguntasAnalisis) --}}
                <li>
                    <h5 ><strong>PROBLEMAS IDENTIFICADOS Y RECOMENDACIONES</strong></h5>

                    @for ( $x = 0; $x < count($preguntasAnalisis); $x++ )
                        @php
                            $opciones = json_decode($preguntasAnalisis[$x]["RES_respuesta"], JSON_PRETTY_PRINT) ;
                        @endphp

                        <h6 style="margin-left: 5px font-family: Arial; font-size:0.9em">
                            {{ $preguntasAnalisis[$x]["BCP_pregunta"] }}:
                        </h6>
                            <p >
                                <ul >
                                    @if ($preguntasAnalisis[$x]["RES_respuesta"])
                                        @if( json_last_error() )
                                            <li>{{ $preguntasAnalisis[$x]["RES_respuesta"] }}</li>
                                        @else
                                            @if ( is_numeric($opciones) )
                                                <li>{{ $opciones }}</li>
                                            @else
                                                {{-- <ul> --}}
                                                    @for ($i = 0; $i < count($opciones); $i++)
                                                    <li>
                                                        {{ $opciones[$i] }}
                                                    </li>
                                                    @endfor
                                                {{-- </ul> --}}
                                            @endif
                                        @endif
                                    @endif
                                    @if ( $preguntasAnalisis[$x]["RES_complemento"] )
                                        <li>
                                            <small>{{ $preguntasAnalisis[$x]["RES_complemento"] }}</small>
                                        </li>
                                    @endif
                                </ul>
                            </p>
                    @endfor


                    <h6 style="font-family: Arial; font-size:0.9em">Imágenes de respaldo:</h6>
                        @foreach ( $imagenes as $imagen  )
                           <p style="padding: 2px">
                                <img src="/{{ $imagen->ARC_ruta }}" style="border: 1px solid #000; height:200px" class="img-fluid" alt="{{ $imagen->ARC_descripcion }}">
                                <br>
                                <span class="text-center" >{{ $imagen->ARC_descripcion }}</span>
                            </p>
                        @endforeach

                </li>

            </ol>
        </div>
        <div class="container text-center">
            <span role="button" id="export_to_word" class="mx-2 btn btn-primary w-50 text-white box-shadow text-shadow" >Exportar a WORD </span>
        </div>
    </div>


</div>



<script src="/wordSaver/FileSaver.js"></script>
    <script src="/wordSaver/jquery.wordexport.js"></script>

    <script >
        $("#export_to_word").click(function(e) {
            $(".elemento-word").wordExport('Informe de visita', 'docx');
        });
        $(function() {

        });
    </script>
@endsection

