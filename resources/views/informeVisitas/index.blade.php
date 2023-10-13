{{-- categorias/index --}}
@extends('layouts.app')
@section('title', 'Informe de visitas')

@section('content')
<form class="row g-3 p-4">
    <div class="text-center"><h3>Informe de visitas</h3></div>
    <div class="col-md-6">
      <label for="inputEmail4" class="form-label">Departamento</label>
      <select id="inputState" class="form-select">
        <option selected>Seleccione...</option>
        <option>La Paz</option>
        <option>Oruro</option>
        <option>Potosí</option>
        <option>Cochabamba</option>
        <option>Chuquisaca</option>
        <option>Tarija</option>
        <option>Pando</option>
        <option>Beni</option>
        <option>Santa Cruz</option>
      </select>
    </div>
    <div class="col-md-6">
      <label for="inputPassword4" class="form-label">Municipio</label>
      <select id="inputState" class="form-select">
        <option selected>Seleccione...</option>
      </select>
    </div>
    <div class="col-6">
      <label for="inputAddress" class="form-label">Lugar Visitado</label>
      <select id="inputState" class="form-select">
        <option selected>Seleccione...</option>
        <option>Centro Penitenciario</option>
        <option>Celdas Policiales</option>
        <option>Cuartel	Centro de Formación Pol/Mil</option>
        <option>Asilo Tecera Edad</option>
        <option>Cuartel</option>
        <option>Asilo Tecera Edad</option>
        <option>Centro de Reintegracion Social</option>
      </select>
    </div>
    <div class="col-6">
      <label for="inputAddress2" class="form-label">Nombre del Centro </label>
      <input type="text" class="form-control" id="inputAddress2" placeholder="">
    </div>
    <div class="col-md-6">
      <label for="inputCity" class="form-label">Fechas de la visita</label>
      <input type="text" class="form-control" id="inputCity">
    </div>
    <div class="col-md-6">
        <label for="inputCity" class="form-label">Equipo que participó en la visita</label>
        <input type="text" class="form-control" id="inputCity">
    </div>
    <div class="col-md-6">
      <label for="inputState" class="form-label">Tipo de visita</label>
      <select id="inputState" class="form-select">
        <option selected>Seleccione...</option>
        <option>Profundidad</option>
        <option>Tematica</option>
        <option>Seguimiento</option>
        <option>Ad -doc</option>
        <option>Reactiva</option>
      </select>
    </div>
    <div class="col-md-6">
        <label for="inputState" class="form-label">Instrumentos utilizados en la visita</label>
        <select id="inputState" class="form-select">
          <option selected>Seleccione...</option>
          <option>Entrevista Director</option>
          <option>Verificación de las instalaciones</option>
          <option>Entrevistas a PPLs/Usuarios</option>
          <option>Posible hecho de Tortura</option>
        </select>
      </div>
    <div class="col-md-12">
      <label for="inputZip" class="form-label">Principales observaciones en la visita</label>
      <textarea class="form-control" id="" cols="30" rows="7"></textarea>
    </div>
    <div class="col-md-6">
        <label for="inputCity" class="form-label">Adjuntar foto del Acta de Visita
        </label>
        <input type="file" class="form-control" id="inputCity">
    </div>
    <div class="col-md-6">
        <label for="inputCity" class="form-label">Adjuntar fotos de las vista de las principales observaciones
        </label>
        <input type="file" class="form-control" id="inputCity">
    </div>
    <div class="col-md-6">
        <label for="inputCity" class="form-label">Nombre del responsable del informe.</label>
        <input type="text" class="form-control" id="inputCity">
    </div>


    <div class="col-12">
      <button type="submit" class="btn btn-primary">Enviar datos</button>
    </div>
  </form>
<script>
  </script>

@endsection

