
@extends('layouts.app')
@section('title', 'Panel de datos - indicadores')

@section('content')

<div class="container mt-3 p-4 bg-white">

    {{-- <a href="{{ route('indicadores.create') }}" class="btn btn-primary mb-3">Crear Nuevo Indicador</a> --}}
    @include('layouts.breadcrumbs', $breadcrumbs)
    <h1 class="text-center">Módulo de Indicadores</h1>
    <h3 class="text-center">Panel de datos</h3>
    <div class="row m-4 p-3 " style="background-color: #cfe2ff;">
        <label for="colFormLabelLg" class="col-sm-8 col-form-label col-form-label-lg">Gestión:</label>
        <div class="col-sm-4 text-start">
            <select class=" form-select form-select-lg" id="anyo_consulta" name="anyo_consulta">
                <option value="2024" {{ ( /*$infoAdicional['EINF_gestion'] == '2024' ||*/ $gestion == '2024') ? 'selected' : '' }}>2024</option>
                <option value="2025" {{ ( /*$infoAdicional['EINF_gestion'] == '2025' ||*/ $gestion == '2025') ? 'selected' : '' }}>2025</option>
                <option value="2026" {{ ( /*$infoAdicional['EINF_gestion'] == '2026' ||*/ $gestion == '2026') ? 'selected' : '' }}>2026</option>
                <option value="2027" {{ ( /*$infoAdicional['EINF_gestion'] == '2027' ||*/ $gestion == '2027') ? 'selected' : '' }}>2027</option>
                <option value="2028" {{ ( /*$infoAdicional['EINF_gestion'] == '2028' ||*/ $gestion == '2028') ? 'selected' : '' }}>2028</option>
            </select>
        </div>
    </div>
    @if (count($categorias) == 0)
        <div class="alert alert-warning text-center" role="alert">
            <i class="bi bi-info-circle"></i> Sin resultados
        </div>
    @endif
    
    <div class="d-flex align-items-start border">
        <div class="col-4 border-end overflow-auto" id="v-pills-tab" style="max-height: 550px; direction: rtl;">
            <div class="nav flex-column nav-pills me-3" role="tablist" aria-orientation="vertical">
                <h3 class="text-center p-2 text-primary my-2">Categorías</h3>
                @php $i=0; @endphp
                @foreach ($categorias as $indic => $indicador)
                    <button class="border-bottom border-top border-start text-start nav-link mb-1 box-shadow {{ $loop->first ? 'active' : '' }}"  id="v-pills-{{$loop->index}}-tab"  data-bs-toggle="pill"  data-bs-target="#v-pills-{{$loop->index}}"  type="button"  role="tab"  aria-controls="v-pills-{{$loop->index}}"  aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <span class="ms-3">{{$indic}} </span>
                    </button>
                @php $i++; @endphp
                @endforeach
            </div>
        </div>
        
        <div class="col-8 p-0 d-flex ">
           
            <div class="tab-content" id="v-pills-tabContent">
                
                @php $j=0; @endphp
                @foreach ($categorias as $indic => $indicadores)

                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                        id="v-pills-{{$loop->index}}" 
                        role="tabpanel" 
                        aria-labelledby="v-pills-{{$loop->index}}-tab">
                        <h3 class="text-center p-2 text-primary">Indicadores</h3>
                        

                        <div class="accordion" id="accordionIndicadores">
                            @php $a=0; @endphp
                            @foreach ($indicadores as $index => $indicador)

                                <h2 class="accordion-header mt-1 rounded" id="heading_{{$a}}_{{$j}}">
                                    <button class="accordion-button bg-info bg-gradient text-dark border p-2 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$a}}_{{$j}}" aria-expanded="false" aria-controls="collapse_{{$a}}_{{$j}}">
                                        <span class="col-1 resultado bg-light rounded-circle p-2 box-shadow text-center" data-indicador="{{$indicador[0]['IND_numero']}}" style="width: 60px;">
                                        </span>
                                        <span class="ms-3"><b>{{$indicador[0]['IND_numero']}}</b>. {{$index}}</span>
                                    </button>
                                </h2>
                                <div id="collapse_{{$a}}_{{$j}}" class="accordion-collapse collapse bg-light border-start border-top" aria-labelledby="heading_{{$a}}_{{$j}}" data-bs-parent="#accordionIndicadores">
                                    <div class="accordion-body">

                                        {{-- mensaje --}}
                                        <div class="alert alert-info box-shadow d-flex align-items-center">
                                            <span class="rounded rounded-circle bg-light p-0 m-1 me-3"><i class="bi bi-chat-left-text fs-3"></i></span>
                                            <small class="text-muted mx-2"><i>Fuente:</i></small>{{$indicador[0]['IND_fuente_informacion']}}
                                        </div>
                                        <div class="ms-3 p-1 border-start">
                                            @php
                                                $total = count($indicador);
                                                $contadorSi = 0;
                                                $contadorNo = 0;
                                            @endphp
                                            @foreach ($indicador as $p => $pregunta)
                                                <div class="mb-3 ">
                                                    @php
                                                        if ($pregunta['HIN_respuesta'] == null) {
                                                            echo '
                                                                <div class=" p-2 rounded">
                                                                    '.$pregunta['IND_parametro'].'
                                                                </div>
                                                            ';
                                                        }
                                                        // dump($pregunta['HIN_respuesta']);
                                                        if ($pregunta['HIN_respuesta'] == 'Si') {
                                                            echo '
                                                                <div class="alert-success p-2 rounded">
                                                                    <i class="bi bi-check-circle-fill fs-4"></i> '.$pregunta['IND_parametro'].'
                                                                </div>
                                                            ';
                                                            $contadorSi++;

                                                        }
                                                        if ($pregunta['HIN_respuesta'] == 'No') {
                                                            echo '
                                                                <div class="alert-danger p-2 rounded">
                                                                    <i class="bi-x-circle-fill fs-4"></i> '.$pregunta['IND_parametro'].'
                                                                </div>
                                                            ';
                                                            $contadorNo++;
                                                        }
                                                    @endphp
                                                    {{-- <label class="form-label"><b class="text-muted">{{$pregunta['IND_codigo_pregunta']}}</b>.
                                                        {{$pregunta['IND_parametro']}}</label> --}}
                                                        @if ($pregunta['HIN_informacion_complementaria'] != null || $pregunta['HIN_informacion_complementaria'] != '')
                                                            <div class=" mt-2 ms-4 bg-light">
                                                                <i class="bi bi-chat-dots"></i>
                                                                    Información complementaria:
                                                                <i>{{$pregunta['HIN_informacion_complementaria']}}</i>
                                                            </div>
                                                        @endif
                                                    @php
                                                        $opciones = json_decode($pregunta['IND_opciones'], true);
                                                        if ( $pregunta['IND_tipo_repuesta'] == 'Lista desplegable'){
                                                            
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
                                                    @endphp
                                                    <hr>
                                                </div>

                                            @endforeach
                                            @php
                                                $resultado = round((($contadorSi/$total)*100), 0);
                                                // dump($resultado);
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
                       
                    </div>
                @php $j++; @endphp
                @endforeach
            </div>
        </div>
    
    </div>
    
    <script>
        //document ready 

        $(document).ready(function() {
            // Evento para cambiar dinámicamente el estado del botón
            $('#anyo_consulta').change(function() {
                let selectedYear = $(this).val();
                // toggleEditButton(selectedYear);
                // Redirige solo si el año cambia
                window.location.href = "{{ route('indicadores.panel') }}" + "?gestion=" + selectedYear;
            });
        
        });
        
        
        
    </script>


@endsection

