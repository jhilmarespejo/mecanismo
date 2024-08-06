
@if ($EST_departamento)
    <h4 class="text-center text-primary" >
        {{$EST_departamento}}
    </h4>
    <h5 class="text-center text-primary" >
        {{$TES_tipo}}
    </h5>
@endif


    <ul class="list-group" style="height: 360px; overflow-y: auto;">
        @foreach ($establecimientos as $establecimiento)
            <li class="list-group-item">
                <p class="m-0 p-0" ><a class="text-decoration-none" href="/visita/historial/{{$establecimiento->EST_id}}">{{$establecimiento->EST_nombre}}</a></p>
                <p class="m-0 p-0 ps-2" ><small class="text-muted">{{($EST_departamento)? "":$establecimiento->EST_departamento }} | Mun: {{$establecimiento->EST_municipio}} | Visitas: {{$establecimiento->cantidad_visitas}}</small></p>
            </li>

        @endforeach
    </ul>




