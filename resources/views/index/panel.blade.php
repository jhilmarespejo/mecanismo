@extends('layouts.app')
@section('title', 'Panel')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
        height: 75vh; /* Ajuste de altura para pantallas grandes */
        min-height: 400px; /* Altura mínima para pantallas pequeñas */
    }

    li.tipos_e:hover {
        background-color: rgb(138, 196, 215);
        color: white;
        font-size: 105%;
        cursor: pointer;
    }

    .info-div {
        width: 150px !important;
    }

    /* Ajuste adicional para pantallas grandes */
    @media (min-width: 1200px) {
        #map {
            height: 85vh; /* Más altura en pantallas extra grandes */
        }

        .col-md-3 {
            height: 85vh; /* Asegura que la columna izquierda use el mismo espacio */
            overflow-y: auto; /* Habilita el scroll si el contenido es mayor */
        }
    }

    .badge-ribbon {
    top: -10px; /* Ajusta la distancia desde la parte superior */
    right: 10px; /* Ajusta la distancia desde el lado derecho */
    padding: 10px 20px;
    /* transform: rotate(45deg); Crea el efecto inclinado de cinta */
    z-index: 1000; /* Asegura que esté por encima del mapa */
    font-size: 1rem; /* Tamaño del texto */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombras para mayor estilo */
    position: absolute;
}



</style>

<div class="container-fluid p-3">
    <div class="row gx-3 gy-3">
        <!-- Columna Izquierda -->
        <div class="col-lg-3 col-md-4 col-sm-12 border border-2">
            <div class="container p-0">
                <div class="buscador">
                    <label for="input_establecimiento" class="form-label">Lugares de detención:</label>
                    <input type="text" id="input_establecimiento" class="form-control" placeholder="Buscar">
                </div>
                <div class="spinner-border text-primary text-center d-none mt-3" role="status" id="spiner-estab"></div>
                <div id="establecimientos" class="mt-3"></div>
            </div>
        </div>
        
        <!-- Columna Derecha -->
        <div class="col-lg-9 col-md-8 col-sm-12 border border-2 position-relative">
            <div id="map" class=""></div>
            <span class="position-absolute badge-ribbon bg-danger text-white box-shadow text-shadow rounded-bottom" >
                {{ $totalEstablecimientos }} Lugares de detención en total 
            </span>
           
        </div>
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
        console.log("entro");
        if ( $(this).val().length > 2 ) {
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
