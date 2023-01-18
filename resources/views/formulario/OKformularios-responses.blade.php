{{-- <div class="align-items-end"> --}}

{{-- </div> --}}


    @isset($success)
        <div class="card border-success w-25 position-absolute top-0 end-0" id="msg" style="margin-top:130px; margin-right:80px;">
            <div class="card-header text-light bg-success d-flex p-0">
                <div class="me-auto p-2 "><i class="bi bi-info-circle"> </i> MNP</div>
                <div class="p-2 btn btn-xs text-light"><i class="bi bi-x-lg"></i></div>
            </div>
            <div class="card-body p-2">
                <h5 class="card-text text-success">!Dato agregado con éxito¡</h5>
            </div>
        </div>
    @endif


    @if($response == 'index')
        <table class="table" id="table_formularios">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre del formulario</th>
                <th scope="col">Fecha de creación</th>
                <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($formularios as $formulario)
                    <tr>
                        <td>{{ $formulario->FRM_id }}</td>
                        <td>{{ $formulario->FRM_titulo }}</td>
                        <td>{{ $formulario->FRM_fecha }}</td>
                        <td class="">
                            <a href="/cuestionario/{{ $formulario->FRM_id }}">
                            <span class="btn btn-success shadow">
                                <i class="bi bi-wrench-adjustable fs-4 text-light shadow-sm"></i>
                            </span>
                            </a>
                            <a href="/cuestionario/show/{{ $formulario->FRM_id }}">
                                <span class="btn btn-primary shadow">
                                    <i class="bi bi-eye fs-4 text-light shadow-sm"></i>
                                </span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <script type="text/javascript">
            $(document).ready( function () {
                $("#msg").fadeOut(8500);
                $(".btn").click(function(){
                    $("#msg").css("display","none");
                })

                $('#table_formularios').DataTable({
                    "order": [[ 0, 'desc' ]],
                    "columnDefs": [ {
                        "targets": [ 3 ],
                        "orderable": false
                        } ],
                });
            })
        </script>
    @endif
    @if($response == 'establecimientos')
    {{-- {{$establecimientos}} --}}

    <datalist id="establecimientos">
        @foreach ($establecimientos as $establecimiento)
            <option value="{{$establecimiento->EST_nombre}} [{{$establecimiento->Ciudad}}]"></option>
        @endforeach
    </datalist>

    @endif


