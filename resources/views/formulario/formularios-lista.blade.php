{{-- LISTA de los formularios aplicados a la visita seleccionada --}}



<a class="text-decoration-none" role="button" data-bs-toggle="modal" data-bs-target="#modalListaFormularios">
    <i class="bi bi-database"></i> Información recolectada
</a>
{{-- <a class="text-decoration-none" href=""><i class="bi bi-file-ruled"></i> Recomendaciones</a> --}}
  <!-- Modal -->
  <div class="modal fade" id="modalListaFormularios" tabindex="-1" aria-labelledby="modalListaFormulariosLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalListaFormulariosLabel">Formularios aplicados en esta visita</h5>
          <span role="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></span>
        </div>
        <div class="modal-body px-4 py-2">
            <dl class="">
                @foreach ($formularios as $key=>$establecimiento)
                    <dt class="mt-3 ">
                        <span class="badge bg-primary rounded-pill text-shadow p-1">
                            {{ count($formularios)-$key }}
                        </span>
                        <span class="badge bg-primary rounded-pill text-shadow p-1">
                            {{ \Carbon\Carbon::parse($establecimiento->FRM_fecha)->format('d-m-Y') }}
                        </span>
                        <a href="http://"><i class="bi bi-eye-fill px-2 text-success fs-5"></i></a>
                        <a href="/cuestionario/imprimir/{{$establecimiento->FRM_id}}"><i class="bi bi-printer-fill px-2 text-success fs-5"></i></a>
                        <a href="/cuestionario/responder/{{$establecimiento->FRM_id}}"><i class="bi bi-pencil-square px-2 text-success fs-5"></i></a>
                    </dt>
                    <dd class="ps-3 border-bottom mb-1 table-hover">{{$establecimiento->FRM_titulo}}</dd>
                @endforeach
            </dl>
        </div>
        <div class="modal-footer">
          <span role="button" class="btn btn-success text-white text-shadow box-shadow" data-bs-dismiss="modal">OK</span>
        </div>
      </div>
    </div>
  </div>
{{-- <h3 class="text-center">{{ ( $formularios[0]->EST_poblacion == 'Privados privadas de libertad')? 'Centro penitenciario '. $formularios[0]->EST_nombre : $formularios[0]->EST_nombre }}</h3> --}}

