
@extends('layouts.app')
@section('title', 'indicadores')

@section('content')
{{-- <i class="bi bi-check-circle-fill"></i>
<i class="bi bi-x-circle-fill"></i>

<div class="alert-success p-2 rounded">
    <i class="bi bi-check-circle-fill"></i> DEWFEFERG RHTY TUJUYJ
</div> --}}
<div class="container">

    {{-- <a href="{{ route('indicadores.create') }}" class="btn btn-primary mb-3">Crear Nuevo Indicador</a> --}}
    <h1 class="text-center">Módulo de Indicadores</h1>
    <h3 class="text-center">Panel de datos</h3>

    <div class="d-flex align-items-start border">
        <div class="col-4 border-end overflow-auto" id="v-pills-tab" style="max-height: 550px; direction: rtl;">
            <div class="nav flex-column nav-pills me-3" role="tablist" aria-orientation="vertical">
                <h3 class="text-center p-2 text-primary my-2">Categorías</h3>
                @php $i=0; @endphp
                @foreach ($categorias as $indic => $indicador)
                    <button class="border-bottom border-top border-star text-start nav-link mb-1 box-shadow " id="v-pills-{{$i}}-tab" data-bs-toggle="pill" data-bs-target="#v-pills-{{$i}}" type="button" role="tab" aria-controls="v-pills-{{$i}}" aria-selected="false">
                        <span class="ms-3">{{$indic}} </span>

                        {{-- <span class="ms-3">{{$indic}}. {{$indicador[0]['IND_numero']}}  </span> --}}
                    </button>
                @php $i++; @endphp
                @endforeach
            </div>
        </div>

        <div class="col-8 p-0 d-flex ">

            <div class="tab-content" id="v-pills-tabContent">
                @php $j=0; @endphp
                @foreach ($categorias as $indic => $indicadores)

                    <div class="tab-pane fade " id="v-pills-{{$j}}" role="tabpanel" aria-labelledby="v-pills-{{$j}}-tab">
                        <h3 class="text-center p-2 text-primary">Indicadores</h3>
                        <div class="accordion" id="accordionIndicadores">
                            @php $a=0; @endphp
                            @foreach ($indicadores as $indic => $indicador)

                                <h2 class="accordion-header mt-1 rounded" id="heading_{{$a}}_{{$j}}">
                                    <button class="accordion-button bg-info bg-gradient text-dark border p-2 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$a}}_{{$j}}" aria-expanded="false" aria-controls="collapse_{{$a}}_{{$j}}">
                                        <span class="bg-light rounded-circle p-3 box-shadow">
                                            {{-- <i class="bi bi-bar-chart-line-fill"></i> --}}
                                            {{-- <div class="resultado_{{$indicador[0]['IND_numero']}}"></div> --}}
                                            <div class="resultado" data-indicador="{{$indicador[0]['IND_numero']}}"></div>
                                        </span>
                                        <span class="ms-3"><b>{{$indicador[0]['IND_numero']}}</b> {{$indic}}</span>
                                    </button>

                                </h2>

                                    <div id="collapse_{{$a}}_{{$j}}" class="accordion-collapse collapse bg-light border-start border-top" aria-labelledby="heading_{{$a}}_{{$j}}" data-bs-parent="#accordionIndicadores">
                                        <div class="accordion-body">

                                            {{-- mensaje --}}
                                            <div class="alert alert-info box-shadow d-flex align-items-center">
                                                <span class="rounded rounded-circle bg-light p-0 m-1 me-3"><i class="bi bi-chat-left-text fs-3"></i></span>
                                                <small class="text-muted mx-2"><i>Fuente de informacion:</i></small>{{$indicador[0]['IND_fuente_informacion']}}
                                            </div>
                                            <div class="ms-3 p-1 border-start">
                                                @php
                                                    $total = count($indicador);
                                                    $contadorSi = 0;
                                                    $contadorNo = 0;
                                                @endphp
                                                @foreach ($indicador as $p => $pregunta)
                                                {{-- @dump(count($indicador)) --}}
                                                    <div class="mb-3 ">
                                                        @php
                                                            if ($pregunta['HIN_respuesta'] == null) {
                                                                echo '
                                                                    <div class=" p-2 rounded">
                                                                        <b>'.$pregunta['IND_codigo_pregunta'].'</b>'.$pregunta['IND_pregunta'].'
                                                                    </div>
                                                               ';
                                                            }
                                                            if ($pregunta['HIN_respuesta'] == 'Si') {
                                                               echo '
                                                                    <div class="alert-success p-2 rounded">
                                                                        <i class="bi bi-check-circle-fill fs-4"></i> <b>'.$pregunta['IND_codigo_pregunta'].'</b>. '.$pregunta['IND_pregunta'].'
                                                                    </div>
                                                               ';
                                                                $contadorSi++;

                                                            }
                                                            if ($pregunta['HIN_respuesta'] == 'No') {
                                                                echo '
                                                                    <div class="alert-danger p-2 rounded">
                                                                        <i class="bi-x-circle-fill fs-4"></i> <b>'.$pregunta['IND_codigo_pregunta'].'</b>. '.$pregunta['IND_pregunta'].'
                                                                    </div>
                                                               ';
                                                               $contadorNo++;
                                                            }
                                                        @endphp
                                                        {{-- <label class="form-label"><b class="text-muted">{{$pregunta['IND_codigo_pregunta']}}</b>.
                                                            {{$pregunta['IND_pregunta']}}</label> --}}
                                                        @php
                                                            $opciones = json_decode($pregunta['IND_opciones'], true);
                                                            if ( $pregunta['IND_tipo_repuesta'] == 'Lista desplegable'){
                                                                // echo '<div class="form-check">';
                                                                // echo '<label>Opciones:</label>';
                                                                // foreach ($opciones as $key => $value) {
                                                                //     // @dump($pregunta);
                                                                //     echo '<div class="form-check">';
                                                                //     echo '<input class="form-check-input" type="radio" name="options_'.$pregunta['IND_id'].' id="option' . $key . '" value="' . $key . '">';
                                                                //     echo '<label class="form-check-label" for="option' . $key . '">' . $value . '</label>';
                                                                //     echo '</div>';
                                                                // }
                                                                // echo '</div>';
                                                            } elseif($pregunta['IND_tipo_repuesta'] == 'Casilla verificacion'){
                                                                echo '<div class="form-check">';
                                                                echo '<label class="form-check-label">Opciones:</label>';
                                                                foreach ($opciones as $key => $value) {
                                                                    echo '<div class="form-check">';
                                                                    echo '<input class="form-check-label" type="checkbox" name="options[]" id="option' . $key . '" value="' . $key . '">';
                                                                    echo '<label class="form-check-label" for="option' . $key . '">' . $value . '</label>';
                                                                    echo '</div>';
                                                                }
                                                                echo '</div>';
                                                            }

                                                            elseif($pregunta['IND_tipo_repuesta'] == 'Texto'){
                                                                echo '<input type="text" name="" class="form-control box-shadow" id="" aria-describedby="">';
                                                              }
                                                              elseif($pregunta['IND_tipo_repuesta'] == 'Numero'){
                                                                echo '<input type="number" min="0" name="" class="w-25 form-control box-shadow" id="" aria-describedby="">';
                                                              }
                                                            //   dump($pregunta['IND_tipo_repuesta']);
                                                        @endphp
                                                        <hr>
                                                        {{-- <input type="text" class="form-control" id="" aria-describedby=""> --}}
                                                    </div>

                                                @endforeach
                                                @php
                                                    $resultado = round((($contadorSi/$total)*100), 0);
                                                @endphp
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            document.querySelector('.resultado[data-indicador="{{$indicador[0]['IND_numero']}}"]').innerText = '{{$resultado}}%';
                                        });
                                    </script>
                              @php $a++; @endphp
                            @endforeach
                        </div>
                        {{-- @foreach ($indicadores as $ind => $indicador)
                            @dump(($ind))
                            aqui los accordion
                                preguntas
                        @endforeach --}}
                        {{-- @dump(array_keys($indicadores)) --}}
                        {{-- <div class="alert alert-info box-shadow d-flex align-items-center">
                            <span class="rounded rounded-circle bg-light p-4 me-3"><i class="bi bi-chat-left-text fs-1"></i></span>
                            @dump($indicadores)
                            <small class="text-muted mx-2"><i>Fuente de informacion:</i></small>{{$indicadores[0]['IND_fuente_informacion']}}
                        </div>
                        @foreach ($indicadores as $pregunta)
                            <div class="mb-3 p-2 mt-3">
                                <label for="exampleInputEmail1" class="form-label">{{$pregunta['IND_pregunta']}}</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                            </div>
                        @endforeach --}}
                    </div>
                @php $j++; @endphp
                @endforeach
            </div>
        </div>

    </div>


@endsection

@section('js')
    <script>

    </script>

@endsection
