

<div class="col-sm-3 d-flex align-items-center">
    <h4>Recomendaciones por establecimiento:</h4>
</div>
<div class="col-sm-9 slider" style="margin:0">
    <i class="buttons prev bi bi-caret-left-square bg-info text-light"></i>
    <div class="viewport" style="height: 200px">
        <ul class="overview">
            @php $a = '';  @endphp
            @foreach ($recomendaciones as $recomendacion)
                @php
                    $total = $recomendacion->cumplido + $recomendacion->incumplido;
                    $avance = ($recomendacion->cumplido/$total)*100;
                @endphp
            <li class="card text-white me-3 col" style="max-width: 18rem; height: auto"><a href="/recomendaciones/{{$recomendacion->EST_id}}" class="text-decoration-none link-light">
                <div class="card-header">
                </div>
                <div class="card-body bg-primary position-relative container " style="height: 100px">
                    <span class="button position-absolute top-0 translate-middle badge rounded-pill btn-success py-1 px-3 fs-5 shadow bg-danger">
                        <i class="bi bi-bookmark-star"></i> {{ $total  }}
                        <small style="font-size: 15px">Recomendaciones</small>
                    </span>
                    <p class="card-text w-100" style="margin: 0;
                    position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%)"> {{ $recomendacion->EST_nombre }} </p>
                </div>
                <div class="card-footer">
                    <small class="text-dark">Cumplimiento:</small>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: {{$avance}}%;" aria-valuenow="{{$avance}}" aria-valuemin="1" aria-valuemax="100"
                        >{{ round($avance) }}%</div>
                    </div>
                </div></a>
            </li>
            @endforeach
        </ul>
    </div>
    <i class="buttons next bi bi-caret-right-square bg-info text-light"></i>
</div>

