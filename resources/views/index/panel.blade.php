@extends('layouts.app')
@section('title', 'Panel')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
      height: 600px;
    }
    li.tipos_e:hover{
        background-color: rgb(138, 196, 215);
        color: white;
        font-size: 105%;
        cursor: pointer;
    }
    .info-div { width: 150px !important;}
</style>

<div class="row">
    <div class="col-md-3 col-sm-12 order-md-1 order-2 border border-2" style="height: 603px;">
        {{-- <div class="btn btn-primary btn-sm box-shadow mt-3 text-center">
            <a href="/uploads/MNP-Bolivia.apk" style="text-decoration:none" class="text-light text-shadow">Descargar App Movil</a>
        </div> --}} 
        <div class="row p-3 m-1 border-bottom text-center">
            <h5> Lugares de deteción en total: </h5>
            <h3 class="bg-info p-4 rounded text-shadow"> {{ $totalEstablecimientos }} </h3>
        </div>
        <div class="container p-0" >
            <div class="buscador">
                <label for="input_establecimiento" class="form-label">Lugares de detención:</label>
                <input type="text" id="input_establecimiento" class="form-control" placeholder="Buscar">
            </div>
            {{-- @include('establecimientos.establecimientos-nuevo') --}}
            <div class="spinner-border text-primary text-center d-none" role="status" id="spiner-estab"> </div>
            <div id="establecimientos" class="mt-3"></div>
        </div>
    </div>
    <div class="col-md-9 col-sm-12 order-md-2 order-1 border border-2">
        <div id="map" style="height: 600px;"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // Crear un mapa centrado en Bolivia
        var cantidades = <?php echo json_encode($establecimientosPorDepartamento); ?>;
        var map = L.map('map').setView([-16.2902, -63.5887], 6);

        // Agregar una capa de mapa base de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Cargar el archivo GeoJSON de los departamentos de Bolivia
        fetch('js/bo.json')
        .then(response => response.json())
        .then(data => {
            // Añadir la capa GeoJSON al mapa
            var geojsonLayer = L.geoJSON(data, {
                onEachFeature: function(feature, layer) {
                    var departamento = feature.properties.name;

                    // Obtener información del departamento
                    var departamentoInfo = `<b>${departamento}</b>`;

                    // Obtener los tipos y cantidades para este departamento
                    var tiposEstablecimientos = cantidades[departamento] ? cantidades[departamento]['tipos'] : [];

                    departamentoInfo += '<p class="m-0 p-0">Seleccione una opción:</p><ul class="p-1">';
                    tiposEstablecimientos.forEach(function(establecimiento) {
                        departamentoInfo += `<li class="tipos_e text-primary" onclick="mostrarContenido(${establecimiento.TES_id}, '${departamento}', '${establecimiento.TES_tipo}')" >${establecimiento.TES_tipo} (${establecimiento.cantidad})</li>`;
                    });
                    departamentoInfo += '</ul>';

                    layer.bindPopup(departamentoInfo);

                    // Obtener coordenadas del departamento
                    var coords = layer.getBounds().getCenter();

                    // Obtener el total de establecimientos en el departamento
                    var totalEstablecimientos = cantidades[departamento] ? cantidades[departamento]['total'] : 0;

                    var icon = L.divIcon({
                        className: 'info-div',
                        html: `<div class="border-start border-5 border-primary" style="height:30px" >
                                    <p class="m-0 p-0 bg-light box-shadow rounded-end">Lugares de Detención: <b>${totalEstablecimientos}</b></p>
                                </div>`,
                        iconAnchor: [0, 0],
                        popupAnchor: [0, 0]
                    });

                    L.marker([coords.lat, coords.lng - 0.5], {icon: icon}).addTo(map);

                    layer.on('mouseover', function() {
                        layer.setStyle({
                            fillColor: 'lightblue' // Cambia el color de relleno del departamento al pasar el mouse
                        });
                    });

                    // Al sacar el mouse del departamento, vuelve al color original
                    layer.on('mouseout', function() {
                        layer.setStyle({
                            fillColor: 'blue' // Restaura el color de relleno del departamento al sacar el mouse
                        });
                    });
                }
            }).addTo(map);
        });
    });

    //funcion ajax para buscar y mostrar los establecimientos que pertenecen al TES_id enviado desde el MAPA
    function mostrarContenido(TES_id, EST_departamento, TES_tipo ) {
        // Limpiar el input de búsqueda
        $('#input_establecimiento').val('');
        $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            url: "/establecimientos/listarSegunTipo",
            type: 'post',
            data: {TES_id, EST_departamento,TES_tipo},
            beforeSend: function () {
                $('#spiner-estab').removeClass('d-none');
            },
            success: function (data, response) {
                $('#establecimientos').html(data);
                $('#spiner-estab').addClass('d-none');
            },
            error: function(response){ console.log(response) }
        });
    }

    //Ajax para buscar establecimientos con el input
    $('#input_establecimiento').on('input', function() {
        if ($(this).val().length > 3) {
            $.ajax({
                async: true,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                url: "/establecimientos/listarSegunTipo",
                type: 'post',
                data: { EST_nombre: $(this).val() },
                beforeSend: function () {
                    $('#spiner-estab').removeClass('d-none');
                },
                success: function (data, response) {
                    $('#establecimientos').html(data);
                    $('#spiner-estab').addClass('d-none');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    });
</script>

@endsection
