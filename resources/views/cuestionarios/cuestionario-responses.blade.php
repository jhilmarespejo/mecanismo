{{-- @php
    dump($recomendaciones);
@endphp --}}

@if ( count($recomendaciones) )
    @php $recId = ''; @endphp
    @foreach ($recomendaciones as $key => $recomendacion)
        <div class="col border p-2">
            @if ($recId != $recomendacion->REC_id)
                <div class="row px-3"> <strong>{{ $recomendacion->REC_recomendacion }}</strong> </div>
            @endif
            <div class="col text-center border-0">
                <span class="col">
                    <img src="/{{ $recomendacion->ARC_ruta}}" class="img-thumbnail border-0 w-75" alt="">
                </span>
            </div>
        </div>
        @php $recId = $recomendacion->REC_id @endphp
    @endforeach
@else
    <div class="alert alert-warning" role="alert">
        Sin datos
    </div>
@endif


