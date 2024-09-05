
@extends('layouts.app')
@section('title', 'Mód Educativo')
@section('content')
<div class="container mt-3 p-4 bg-white">
    <h1 class="text-primary fs-2 text-center">Crear Nuevo Registro de Educación</h1>
    @include('layouts.breadcrumbs', $breadcrumbs)
    <form action="{{ route('educacion.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="tema" class="form-label">Tema</label>
            <input type="text" class="form-control" name="edu_tema" id="edu_tema" value="{{ old('edu_tema') }}">
            @error('edu_tema')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="beneficiarios" class="form-label">Beneficiarios</label>
            <select name="edu_beneficiarios" id="edu_beneficiarios" class="form-control">
                <option value="">Selecciona una opción</option>
                <option value="Abogados defensores">Abogados defensores</option>
                <option value="Abogados del Estado">Abogados del Estado</option>
                <option value="Comisiones de derechos humanos">Comisiones de derechos humanos</option>
                <option value="Directores de cárceles">Directores de cárceles</option>
                <option value="Fiscales y jueces">Fiscales y jueces</option>
                <option value="Guardias Penitenciarios">Guardias Penitenciarios</option>
                <option value="Militares">Militares</option>
                <option value="Médicos forenses">Médicos forenses</option>
                <option value="Periodistas">Periodistas</option>
                <option value="Personal administrativo de cárceles">Personal administrativo de cárceles</option>
                <option value="Personal de salud en centros de detención">Personal de salud en centros de detención</option>
                <option value="Personal penitenciario">Personal penitenciario</option>
                <option value="Policías y Militares">Policías y Militares</option>
                <option value="Personal Centro para Adultos Mayores">Personal Centro para Adultos Mayores</option>
                <option value="Personal Hospital Psiquiátrico">Personal Hospital Psiquiátrico</option>
                <option value="Personal Centro de Formacion Militar y Policial">Personal Centro de Formacion Militar y Policial</option>
                <option value="Personal Centro para migrantes">Personal Centro para migrantes</option>
                <option value="Personal Centro Penitenciario">Personal Centro Penitenciario</option>
                <option value="Personal Celda Policial">Personal Celda Policial</option>
                <option value="Personal Cuarteles">Personal Cuarteles</option>
                <option value="Personal Centros de Acogimiento y Reintegración">Personal Centros de Acogimiento y Reintegración</option>
            </select>
            @error('edu_beneficiarios')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="cantidad_beneficiarios" class="form-label">Cantidad de Beneficiarios</label>
            <input type="number" class="form-control" name="edu_cantidad_beneficiarios" id="cantidad_beneficiarios" value="{{ old('edu_cantidad_beneficiarios') }}" >
            @error('edu_cantidad_beneficiarios')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edu_medio_verificacion" class="form-label">Medio de Verificación</label>
            <textarea class="form-control" name="edu_medio_verificacion" id="medio_verificacion">{{ old('edu_medio_verificacion') }}</textarea>
            @error('edu_medio_verificacion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edu_ciudad" class="form-label">Selecciona una ciudad</label>
            <select name="edu_ciudad" id="edu_ciudad" class="form-select">
                <option value="">Selecciona una ciudad</option>
                <option value="La Paz">La Paz</option>
                <option value="Oruro">Oruro</option>
                <option value="Potosí">Potosí</option>
                <option value="Cochabamba">Cochabamba</option>
                <option value="Chuquisaca">Chuquisaca</option>
                <option value="Tarija">Tarija</option>
                <option value="Pando">Pando</option>
                <option value="Beni">Beni</option>
                <option value="Santa Cruz">Santa Cruz</option>
                <option value="Otra ciudad">Otra ciudad</option>
            </select>
            @error('edu_ciudad')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="gestion" class="form-label">Gestión</label>
            <input type="number" class="form-control" max="2030" min="2023" name="edu_gestion" id="gestion" value="{{ old('edu_gestion') }}" >
            @error('edu_gestion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="edu_imagen_medio_verificacion" class="form-label">Imagen del Medio de Verificación</label>
            <input type="file" class="form-control" name="edu_imagen_medio_verificacion" id="edu_imagen_medio_verificacion">
            @error('edu_imagen_medio_verificacion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
    </form>
</div>
@endsection
