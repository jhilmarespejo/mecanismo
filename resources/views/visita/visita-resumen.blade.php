@extends('layouts.app')
@section('title', 'Visitas')

@section('content')



<div class="container">
    <!-- Select box para Filtrar por el año -->
    <div class="row m-4 p-3 " style="background-color: #cfe2ff;">
        <form action="{{ route('visita.resumen') }}" method="GET" class="mb-3">
            <label for="anio_actual" class="col-sm-8 col-form-label col-form-label-lg">Filtrar por año:</label>
            <select name="anio_actual" id="anio_actual" class="form-select form-select-lg" onchange="this.form.submit()">
                <option value="">Seleccionar año</option>
                <option value="2024" {{ $anioActual == '2024' ? 'selected' : '' }}>2024</option>
                <option value="2025" {{ $anioActual == '2025' ? 'selected' : '' }}>2025</option>
                <option value="2026" {{ $anioActual == '2026' ? 'selected' : '' }}>2026</option>
                <option value="2027" {{ $anioActual == '2027' ? 'selected' : '' }}>2027</option>
                <option value="2028" {{ $anioActual == '2028' ? 'selected' : '' }}>2028</option>
            </select>
        </form>
    </div>

    @if ($totalVisitas["total_geneal"] == 0)
        <p class="alert alert-warning fs-5">
            Aún no se registraron visitas para este año
        </p>
    @else
        {{-- <div class="text-center ">
            <span class="button btn-lg btn-success text-shadow box-shadow p-3 fs-3" >
                <img src="/img/TotalVisitas.png" class="img-fluid px-2" style="max-width: 80px;" alt="Total de visitas">
                Total de visitas {{$totalVisitas['total_geneal']}}</span>
        </div> --}}
        <div class="accordion mt-5" id="accordionTipoEstablecimientos">
            @php $tipoE=0; @endphp
            @foreach ($totalVisitas['resultado'] as $tipoEstablecimiento => $tipos)
            {{-- @dump($tipos) --}}
            <div class="accordion-item mt-2 box-shadow">
                    <h2 class="accordion-header" id="{{$tipoEstablecimiento}}">
                        <button class="accordion-button bg-secondary text-white p-2 d-flex justify-content-between align-items-start" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{$tipoE}}" aria-expanded="true" aria-controls="collapse_{{$tipoE}}">
                            <div class="d-flex align-items-center col">
                                <img src="/img/LD.png" class="img-fluid px-2" style="max-width: 80px;" alt="{{$tipoEstablecimiento}}">
                                <span class="fw-bold fs-6 ms-2">{{$tipoEstablecimiento}}:</span>
                            </div>
                            <div class="d-flex text-end col fs-4">

                                <span class="badge bg-light text-secondary">  <small class="text-muted">Total de visitas: </small> {{  $tipos['total_tipo_establecimiento'] }}</span>
                            </div>
                        </button>
                    </h2>

                <div id="collapse_{{$tipoE}}" class="accordion-collapse collapse ms-2 " aria-labelledby="{{$tipoEstablecimiento}}" data-bs-parent="#accordionTipoEstablecimientos">
                    <div class="accordion-body ">

                        <div class="accordion" id="accordionEstablecimientos">
                            @php $e=0; @endphp
                            @foreach ($tipos['establecimientos'] as $nombres => $establecimientos)
                                {{-- @dump($tipos['establecimientos']) --}}
                                <div class="accordion-item mt-1">
                                    <h2 class="accordion-header" id="heading-{{$e}}-{{$tipoE}}">
                                        <button class="accordion-button collapsed p-2 align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$e}}-{{$tipoE}}" aria-expanded="false" aria-controls="collapse{{$e}}-{{$tipoE}}">
                                            <div class="col">
                                                <span class="fw-bold fs-6 ms-2">{{$nombres}}:</span>
                                            </div>
                                            <div class="d-flex text-end col">
                                                <span class="badge bg-light text-dark p-2">
                                                    <small class="tewxt-muted">Visitas</small>
                                                    <span class="fs-6" >{{$establecimientos['total_establecimiento']}}</span>
                                                </span>
                                            </div>
                                        </button>

                                    </h2>
                                    <div id="collapse{{$e}}-{{$tipoE}}" class="accordion-collapse collapse" aria-labelledby="heading-{{$e}}-{{$tipoE}}" data-bs-parent="#accordionEstablecimientos">
                                        <div class="accordion-body">
                                            @foreach ($establecimientos['visitas'] as $visitas)

                                                <li class="ms-3 list-group-item hover d-flex justify-content-between align-items-center">
                                                    <div class="col">

                                                        <a href="historial/{{$establecimientos['EST_id']}}" target="_blank" class="link-success"  > <span class="fw-bold fs-6 ms-2">{{$visitas['VIS_tipo']}}:</span> </a>
                                                    </div>
                                                    <div class="d-flex text-start col">
                                                        <span class="badge bg-light text-dark p-2">
                                                            <small class="tewxt-muted">Visitas</small>
                                                            <span class="fs-6" >{{$visitas['total_tipo_visitas']}}</span>
                                                            <span class="class  fst-italic text-muted" style="font-size: 10px"> |  {{$visitas['VIS_fechas']}}</span>
                                                        </span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @php $e++; @endphp
                            @endforeach
                    </div>


                        {{-- @foreach ($visita as $estab => $establecimiento)
                                <li class="list-group-item">{{$estab}}</li> --}}
                                {{-- <p> @dump($estab)</p> --}}
                                {{-- <p> @dump($establecimiento[0]->EST_nombre)</p> --}}
                                {{-- @endforeach --}}


                    </div>
                </div>
            </div>


                @php $tipoE++; @endphp
            @endforeach
        </div>
        
    @endif
    
</div>





@endsection
