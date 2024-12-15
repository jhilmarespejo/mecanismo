<!-- resources/views/educacion/edit.blade.php -->

@extends('layouts.app')
@section('title', 'Módulo Educativo')
@section('content')
<div class="container mt-3 p-4 bg-white">
    <h1 class="text-primary fs-2 text-center">Edición de registro</h1>
    @include('layouts.breadcrumbs', $breadcrumbs)
    <form action="{{ route('educacion.update', $educacion->EDU_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="tema" class="form-label">Tema</label>
            <input type="text" class="form-control" name="edu_tema" id="tema" value="{{ $educacion->EDU_tema }}">
            @error('edu_tema')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edu_beneficiarios" class="form-label">Beneficiarios</label>
            <select name="edu_beneficiarios" id="edu_beneficiarios" class="form-control">
                <option value="">Selecciona una opción</option>
                <option value="Abogados defensores" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Abogados defensores' ? 'selected' : '' }}>Abogados defensores</option>
                <option value="Autoridades locales y civiles" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Autoridades locales y civiles' ? 'selected' : '' }}>Autoridades locales y civiles</option>
                <option value="Autoridades penitenciarias" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Autoridades penitenciarias' ? 'selected' : '' }}>Autoridades penitenciarias</option>
                <option value="Defensores del pueblo y fiscales" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Defensores del pueblo y fiscales' ? 'selected' : '' }}>Defensores del pueblo y fiscales</option>
                <option value="Defensores del pueblo y organizaciones civiles" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Defensores del pueblo y organizaciones civiles' ? 'selected' : '' }}>Defensores del pueblo y organizaciones civiles</option>
                <option value="Fiscales y jueces" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Fiscales y jueces' ? 'selected' : '' }}>Fiscales y jueces</option>
                <option value="Funcionarios judiciales y abogados" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Funcionarios judiciales y abogados' ? 'selected' : '' }}>Funcionarios judiciales y abogados</option>
                <option value="Funcionarios penitenciarios" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Funcionarios penitenciarios' ? 'selected' : '' }}>Funcionarios penitenciarios</option>
                <option value="Investigadores y fiscales" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Investigadores y fiscales' ? 'selected' : '' }}>Investigadores y fiscales</option>
                <option value="Mediadores y trabajadores sociales" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Mediadores y trabajadores sociales' ? 'selected' : '' }}>Mediadores y trabajadores sociales</option>
                <option value="Médicos y enfermeras" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Médicos y enfermeras' ? 'selected' : '' }}>Médicos y enfermeras</option>
                <option value="Organizaciones de derechos humanos" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Organizaciones de derechos humanos' ? 'selected' : '' }}>Organizaciones de derechos humanos</option>
                <option value="Periodistas" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Periodistas' ? 'selected' : '' }}>Periodistas</option>
                <option value="Personal de la defensoría del pueblo" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Personal de la defensoría del pueblo' ? 'selected' : '' }}>Personal de la defensoría del pueblo</option>
                <option value="Personal de seguridad" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Personal de seguridad' ? 'selected' : '' }}>Personal de seguridad</option>
                <option value="Personal médico y psicológico" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Personal médico y psicológico' ? 'selected' : '' }}>Personal médico y psicológico</option>
                <option value="Policías y fuerzas de seguridad" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Policías y fuerzas de seguridad' ? 'selected' : '' }}>Policías y fuerzas de seguridad</option>
                <option value="Supervisores de centros penitenciarios" {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) === 'Supervisores de centros penitenciarios' ? 'selected' : '' }}>Supervisores de centros penitenciarios</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="cantidad_beneficiarios" class="form-label">Cantidad de Beneficiarios</label>
            <input type="number" class="form-control" name="edu_cantidad_beneficiarios" id="cantidad_beneficiarios" value="{{ $educacion->EDU_cantidad_beneficiarios }}">
            @error('edu_cantidad_beneficiarios')
                <small class="text-danger">{{ $message }}</small>
            @enderror            
        </div>
        <div class="mb-3">
            <label for="medio_verificacion" class="form-label">Medio de Verificación</label>
            <textarea class="form-control" name="edu_medio_verificacion" id="medio_verificacion">{{ $educacion->EDU_medio_verificacion }}</textarea>
            @error('edu_medio_verificacion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edu_ciudad" class="form-label">Selecciona una ciudad</label>
            <select name="edu_ciudad" id="edu_ciudad" class="form-select">
                <option value="">Selecciona una ciudad</option>
                <option value="La Paz" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'La Paz' ? 'selected' : '' }}>La Paz</option>
                <option value="Oruro" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Oruro' ? 'selected' : '' }}>Oruro</option>
                <option value="Potosí" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Potosí' ? 'selected' : '' }}>Potosí</option>
                <option value="Cochabamba" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                <option value="Chuquisaca" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                <option value="Tarija" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Tarija' ? 'selected' : '' }}>Tarija</option>
                <option value="Pando" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Pando' ? 'selected' : '' }}>Pando</option>
                <option value="Beni" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Beni' ? 'selected' : '' }}>Beni</option>
                <option value="Santa Cruz" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                <option value="Otra ciudad" {{ old('edu_ciudad', $educacion->EDU_ciudad) === 'Otra ciudad' ? 'selected' : '' }}>Otra ciudad</option>
            </select>

        </div>
        @error('edu_ciudad')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="mb-3">
            <label for="gestion" class="form-label">Gestión</label>
            <input type="number" class="form-control" max="2030" min="2023" name="edu_gestion" id="gestion" value="{{ $educacion->EDU_gestion }}">
            @error('edu_gestion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3 container">
            @if ($educacion->EDU_imagen_medio_verificacion)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal">
                    Ver archivo adjunto
                </button>

                <!-- Modal de Bootstrap -->
                <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="documentModalLabel">Archivo adjunto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $filePath = storage_path('app/public/' . $educacion->EDU_imagen_medio_verificacion);
                                    $fileExists = file_exists($filePath);
                                    $fileMimeType = $fileExists ? mime_content_type($filePath) : null;
                                @endphp

                                @if (!$fileExists)
                                    <p>El archivo no se encuentra disponible.</p>
                                @elseif (Str::startsWith($fileMimeType, 'image/'))
                                    <!-- Imagen -->
                                    <img src="{{ asset('storage/' . $educacion->EDU_imagen_medio_verificacion) }}" class="img-fluid" alt="Imagen Verificación">
                                @elseif ($fileMimeType === 'application/pdf')
                                    <!-- PDF -->
                                    <embed src="{{ asset('storage/' . $educacion->EDU_imagen_medio_verificacion) }}" type="application/pdf" width="100%" height="500px" />
                                @else
                                    <!-- Enlace de descarga para otros documentos -->
                                    <a href="{{ asset('storage/' . $educacion->EDU_imagen_medio_verificacion) }}" target="_blank" class="btn btn-secondary">
                                        Descargar {{ pathinfo($educacion->EDU_imagen_medio_verificacion, PATHINFO_BASENAME) }}
                                    </a>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        

        <div class="mb-3">
            <label for="edu_imagen_medio_verificacion" class="form-label">Imagen del Medio de Verificación</label>
            <input type="file" class="form-control" name="edu_imagen_medio_verificacion" id="edu_imagen_medio_verificacion">
            @error('edu_imagen_medio_verificacion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
    </form>
</div>


  
@endsection
