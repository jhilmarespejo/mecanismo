
@extends('layouts.app')
@section('title', 'Educativo')

@section('content')

<div class="container">

    {{-- <a href="{{ route('indicadores.create') }}" class="btn btn-primary mb-3">Crear Nuevo Indicador</a> --}}
    <h1>Módulo Educativo</h1>
    {{-- <div class="container mt-5">
        <div class="card">
          <div class="card-header">
            Formulario de registro de datos
          </div>
          <div class="card-body">
            <form>
              <div class="mb-3">
                <label for="tema" class="form-label">Tema del taller o capacitación:</label>
                <input type="text" class="form-control" id="tema" name="tema" placeholder="Ingrese el tema">
              </div>
              <div class="mb-3">
                <label for="beneficiarios" class="form-label">Beneficiarios:</label>
                <input type="text" class="form-control" id="beneficiarios" name="beneficiarios" placeholder="Ingrese los beneficiarios">
              </div>
              <div class="mb-3">
                <label for="cantidadBeneficiarios" class="form-label">Cantidad de Beneficiarios:</label>
                <input type="number" class="form-control" id="cantidadBeneficiarios" name="cantidadBeneficiarios" placeholder="Ingrese la cantidad de beneficiarios">
              </div>
              <div class="mb-3">
                <label for="medioVerificacion" class="form-label">Medio de Verificación:</label>
                <input type="text" class="form-control" id="medioVerificacion" name="medioVerificacion" placeholder="Ingrese el medio de verificación">
              </div>
              <div class="mb-3">
                <label for="medioVerificacion" class="form-label">Imagen del medio de verificación</label>
                <input type="file" class="form-control" id="medioVerificacion" name="medioVerificacion" placeholder="Ingrese el medio de verificación">
              </div>
              <button type="submit" class="btn btn-primary">Guardar registro</button>
            </form>
          </div>
        </div>
    </div> --}}


<div class="container mt-5">
    <h1 class="mb-4">Reportes de activiades educativas</h1>

    <!-- Reporte de Cantidad de Talleres por Tema -->
    <div>
      <h2>Reporte de Cantidad de capacitacione por Tema</h2>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Tema de la capacitación</th>
            <th>Cantidad de capacitaciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Introducción a los Derechos Humanos y la Prevención de la Tortura</td>
            <td>500 policías</td>
          </tr>
          <tr>
            <td>Marco Legal Nacional e Internacional en la Lucha contra la Tortura</td>
            <td>300 policias</td>
          </tr>
          <tr>
            <td>Protocolos y Procedimientos de Prevención y Denuncia de la Tortura</td>
            <td>1000 soldados</td>
          </tr>
          <tr>
            <td>Ética Profesional y Conducta Ética en el Trato a Personas Detenidas</td>
            <td>100 policias</td>
          </tr>
          <!-- Agregar más filas según sea necesario -->
        </tbody>
      </table>
    </div>

    <!-- Otros Reportes -->
    <!-- Agregar aquí otros reportes según sea necesario -->

  </div>

@endsection

@section('js')
    <script>

    </script>

@endsection
