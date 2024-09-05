<!-- resources/views/educacion/edit.blade.php -->

@extends('layouts.app')
@section('title', 'Módulo Educativo')
@section('content')
<div class="container mt-3 p-4 bg-white">
    <h1 class="text-primary fs-2 text-center">Edición de registro</h1>
    @include('layouts.breadcrumbs', $breadcrumbs)
    <form action="{{ route('educacion.update', $educacion->edu_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="tema" class="form-label">Tema</label>
            <input type="text" class="form-control" name="edu_tema" id="tema" value="{{ $educacion->edu_tema }}">
            @error('edu_tema')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="beneficiarios" class="form-label">Beneficiarios</label>
            <select name="edu_beneficiarios" id="edu_beneficiarios" class="form-control">
                <option value="">Selecciona una opción</option>
                <option value="Abogados defensores" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Abogados defensores' ? 'selected' : '' }}>Abogados defensores</option>
                <option value="Abogados del Estado" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Abogados del Estado' ? 'selected' : '' }}>Abogados del Estado</option>
                <option value="Comisiones de derechos humanos" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Comisiones de derechos humanos' ? 'selected' : '' }}>Comisiones de derechos humanos</option>
                <option value="Directores de cárceles" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Directores de cárceles' ? 'selected' : '' }}>Directores de cárceles</option>
                <option value="Fiscales y jueces" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Fiscales y jueces' ? 'selected' : '' }}>Fiscales y jueces</option>
                <option value="Guardias Penitenciarios" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Guardias Penitenciarios' ? 'selected' : '' }}>Guardias Penitenciarios</option>
                <option value="Militares" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Militares' ? 'selected' : '' }}>Militares</option>
                <option value="Médicos forenses" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Médicos forenses' ? 'selected' : '' }}>Médicos forenses</option>
                <option value="Periodistas" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Periodistas' ? 'selected' : '' }}>Periodistas</option>
                <option value="Personal administrativo de cárceles" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal administrativo de cárceles' ? 'selected' : '' }}>Personal administrativo de cárceles</option>
                <option value="Personal de salud en centros de detención" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal de salud en centros de detención' ? 'selected' : '' }}>Personal de salud en centros de detención</option>
                <option value="Personal penitenciario" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal penitenciario' ? 'selected' : '' }}>Personal penitenciario</option>
                <option value="Policías y Militares" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Policías y Militares' ? 'selected' : '' }}>Policías y Militares</option>
                <option value="Personal Centro para Adultos Mayores" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Centro para Adultos Mayores' ? 'selected' : '' }}>Personal Centro para Adultos Mayores</option>
                <option value="Personal Hospital Psiquiátrico" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Hospital Psiquiátrico' ? 'selected' : '' }}>Personal Hospital Psiquiátrico</option>
                <option value="Personal Centro de Formacion Militar y Policial" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Centro de Formacion Militar y Policial' ? 'selected' : '' }}>Personal Centro de Formacion Militar y Policial</option>
                <option value="Personal Centro para migrantes" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Centro para migrantes' ? 'selected' : '' }}>Personal Centro para migrantes</option>
                <option value="Personal Centro Penitenciario" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Centro Penitenciario' ? 'selected' : '' }}>Personal Centro Penitenciario</option>
                <option value="Personal Celda Policial" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Celda Policial' ? 'selected' : '' }}>Personal Celda Policial</option>
                <option value="Personal Cuarteles" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Cuarteles' ? 'selected' : '' }}>Personal Cuarteles</option>
                <option value="Personal Centros de Acogimiento y Reintegración" {{ old('edu_beneficiarios', $educacion->edu_beneficiarios) === 'Personal Centros de Acogimiento y Reintegración' ? 'selected' : '' }}>Personal Centros de Acogimiento y Reintegración</option>
            </select>

        </div>
        <div class="mb-3">
            <label for="cantidad_beneficiarios" class="form-label">Cantidad de Beneficiarios</label>
            <input type="number" class="form-control" name="edu_cantidad_beneficiarios" id="cantidad_beneficiarios" value="{{ $educacion->edu_cantidad_beneficiarios }}">
            @error('edu_cantidad_beneficiarios')
                <small class="text-danger">{{ $message }}</small>
            @enderror            
        </div>
        <div class="mb-3">
            <label for="medio_verificacion" class="form-label">Medio de Verificación</label>
            <textarea class="form-control" name="edu_medio_verificacion" id="medio_verificacion">{{ $educacion->edu_medio_verificacion }}</textarea>
            @error('edu_medio_verificacion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edu_ciudad" class="form-label">Selecciona una ciudad</label>
            <select name="edu_ciudad" id="edu_ciudad" class="form-select">
                <option value="">Selecciona una ciudad</option>
                <option value="La Paz" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'La Paz' ? 'selected' : '' }}>La Paz</option>
                <option value="Oruro" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Oruro' ? 'selected' : '' }}>Oruro</option>
                <option value="Potosí" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Potosí' ? 'selected' : '' }}>Potosí</option>
                <option value="Cochabamba" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                <option value="Chuquisaca" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                <option value="Tarija" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Tarija' ? 'selected' : '' }}>Tarija</option>
                <option value="Pando" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Pando' ? 'selected' : '' }}>Pando</option>
                <option value="Beni" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Beni' ? 'selected' : '' }}>Beni</option>
                <option value="Santa Cruz" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                <option value="Otra ciudad" {{ old('edu_ciudad', $educacion->edu_ciudad) === 'Otra ciudad' ? 'selected' : '' }}>Otra ciudad</option>
            </select>

        </div>
        @error('edu_ciudad')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div class="mb-3">
            <label for="gestion" class="form-label">Gestión</label>
            <input type="number" class="form-control" max="2030" min="2023" name="edu_gestion" id="gestion" value="{{ $educacion->edu_gestion }}">
            @error('edu_gestion')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3 container">
            @if ($educacion->edu_imagen_medio_verificacion)
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
                                    $filePath = storage_path('app/public/' . $educacion->edu_imagen_medio_verificacion);
                                    $fileExists = file_exists($filePath);
                                    $fileMimeType = $fileExists ? mime_content_type($filePath) : null;
                                @endphp

                                @if (!$fileExists)
                                    <p>El archivo no se encuentra disponible.</p>
                                @elseif (Str::startsWith($fileMimeType, 'image/'))
                                    <!-- Imagen -->
                                    <img src="{{ asset('storage/' . $educacion->edu_imagen_medio_verificacion) }}" class="img-fluid" alt="Imagen Verificación">
                                @elseif ($fileMimeType === 'application/pdf')
                                    <!-- PDF -->
                                    <embed src="{{ asset('storage/' . $educacion->edu_imagen_medio_verificacion) }}" type="application/pdf" width="100%" height="500px" />
                                @else
                                    <!-- Enlace de descarga para otros documentos -->
                                    <a href="{{ asset('storage/' . $educacion->edu_imagen_medio_verificacion) }}" target="_blank" class="btn btn-secondary">
                                        Descargar {{ pathinfo($educacion->edu_imagen_medio_verificacion, PATHINFO_BASENAME) }}
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
