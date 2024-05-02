
@extends('layouts.app')
@section('title', 'Panel')


@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map {
      height: 600px;
    }
  </style>

<div class="container m-0 p-0">
    {{-- <h4 class="text-center py-0 m-1">Estadísticas</h4> --}}


    <div class="row">
        <div class="col-md-3 order-md-1 order-1 border border-2">
            <div class="btn btn-primary tbn-lg box-shadow">
                <a href="/uploads/MNP-Bolivia.apk" style="text-decoration:none" class="text-light text-shadow">Descargar App Movil</a>

            </div>
            <h3 class="test-center">Panel de datos estadísticos</h3>
        </div>
        <div class="col-md-9 order-md-2 order-2 border border-2"><div id="map"></div></div>
    </div>



</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script type="text/javascript">
  // Crear un mapa centrado en Bolivia
  var map = L.map('map').setView([-16.2902, -63.5887], 6);

  // Agregar una capa de mapa base de OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  // Cargar el archivo GeoJSON de los departamentos de Bolivia
  fetch('js/Departamentos_Bolivia.geojson')
    .then(response => response.json())
    .then(data => {
      // Añadir la capa GeoJSON al mapa y mostrar Popup al hacer clic
      L.geoJSON(data, {
        onEachFeature: function(feature, layer) {
          // Obtener información del departamento
          var departamentoInfo = `Departamento: ${feature.properties.NOM_DEP}`;

          // Mostrar Popup al hacer clic
          layer.bindPopup(departamentoInfo);

          // Cambiar el color al pasar el mouse sobre el departamento
          layer.on({
            mouseover: function(e) {
              e.target.setStyle({
                fillColor: '#ff0000', // Cambia el color de relleno a rojo
                fillOpacity: 0.7 // Ajusta la opacidad del relleno
              });
            },
            mouseout: function(e) {
              e.target.setStyle({
                fillColor: '#3388ff', // Restablece el color de relleno predeterminado
                fillOpacity: 0.5 // Restablece la opacidad del relleno predeterminada
              });
            }
          });
        }
      }).addTo(map);
    });
</script>

@endsection

{{--
    1. Cantida de lugares visitados
    2. Desagregado por tipos de lugares visitados
    3. Cantidad de entrevistados por sexo
    4. Cantidad de visitas
    5. Tipos de visitas:
        5.1. visitas en profundidad se ve todo, personal
        5.2 visitas tematicas, tema esoecifico como extorsion o ascinamiento
        5.3. Visitas de seguimiento general mente depues de las visitas en profundidad en funcion a las recomendaciones realizadas en las visitas de profundidad
        5.4. Visitas Adhoc. Visitas no planificadas cuando sucede un evento mayor como un incendio o algo fortuito, que realiza
        5.5 Visitas reactivas. Despues de una queja realizada por un ppl, familiar u ong


CANTIDAD DE VISITAS con el cuadro word
TIPOS DE VISITAS
CANTIDAD DE ENTREVISTADOS
--}}
{{-- penitenciarias y carceletas 21

celdas policiales 10 --}}
