@extends('layouts.app')
@section('title', 'Módulo Educativo')
@section('content')
<div class="container mt-3 p-4 bg-white">
    <h1 class="text-primary fs-2 text-center">Crear Nueva Actividad Educativa</h1>
    @include('layouts.breadcrumbs', $breadcrumbs)
    
    <form id="formEducacion" action="{{ route('educacion.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="edu_tema" class="form-label">Tema <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="edu_tema" id="edu_tema" rows="3" placeholder="Descripción del tema de la actividad">{{ old('edu_tema') }}</textarea>
                    @error('edu_tema')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_beneficiarios" class="form-label">Beneficiarios <span class="text-danger">*</span></label>
                    <select name="edu_beneficiarios" id="edu_beneficiarios" class="form-select">
                        <option value="">Selecciona una opción</option>
                        <option value="Abogados defensores" {{ old('edu_beneficiarios') == 'Abogados defensores' ? 'selected' : '' }}>Abogados defensores</option>
                        <option value="Abogados del Estado" {{ old('edu_beneficiarios') == 'Abogados del Estado' ? 'selected' : '' }}>Abogados del Estado</option>
                        <option value="Comisiones de derechos humanos" {{ old('edu_beneficiarios') == 'Comisiones de derechos humanos' ? 'selected' : '' }}>Comisiones de derechos humanos</option>
                        <option value="Directores de cárceles" {{ old('edu_beneficiarios') == 'Directores de cárceles' ? 'selected' : '' }}>Directores de cárceles</option>
                        <option value="Fiscales y jueces" {{ old('edu_beneficiarios') == 'Fiscales y jueces' ? 'selected' : '' }}>Fiscales y jueces</option>
                        <option value="Guardias Penitenciarios" {{ old('edu_beneficiarios') == 'Guardias Penitenciarios' ? 'selected' : '' }}>Guardias Penitenciarios</option>
                        <option value="Militares" {{ old('edu_beneficiarios') == 'Militares' ? 'selected' : '' }}>Militares</option>
                        <option value="Médicos forenses" {{ old('edu_beneficiarios') == 'Médicos forenses' ? 'selected' : '' }}>Médicos forenses</option>
                        <option value="Periodistas" {{ old('edu_beneficiarios') == 'Periodistas' ? 'selected' : '' }}>Periodistas</option>
                        <option value="Personal administrativo de cárceles" {{ old('edu_beneficiarios') == 'Personal administrativo de cárceles' ? 'selected' : '' }}>Personal administrativo de cárceles</option>
                        <option value="Personal de salud en centros de detención" {{ old('edu_beneficiarios') == 'Personal de salud en centros de detención' ? 'selected' : '' }}>Personal de salud en centros de detención</option>
                        <option value="Personal penitenciario" {{ old('edu_beneficiarios') == 'Personal penitenciario' ? 'selected' : '' }}>Personal penitenciario</option>
                        <option value="Policías y Militares" {{ old('edu_beneficiarios') == 'Policías y Militares' ? 'selected' : '' }}>Policías y Militares</option>
                        <option value="Personal Centro para Adultos Mayores" {{ old('edu_beneficiarios') == 'Personal Centro para Adultos Mayores' ? 'selected' : '' }}>Personal Centro para Adultos Mayores</option>
                    </select>
                    @error('edu_beneficiarios')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_cantidad_beneficiarios" class="form-label">Cantidad de Beneficiarios <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="edu_cantidad_beneficiarios" id="edu_cantidad_beneficiarios" 
                           value="{{ old('edu_cantidad_beneficiarios') }}" min="1" placeholder="Número de personas beneficiadas">
                    @error('edu_cantidad_beneficiarios')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                    <select name="edu_ciudad" id="edu_ciudad" class="form-select">
                        <option value="">Selecciona una ciudad</option>
                        <option value="La Paz" {{ old('edu_ciudad') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                        <option value="Oruro" {{ old('edu_ciudad') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                        <option value="Potosí" {{ old('edu_ciudad') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                        <option value="Cochabamba" {{ old('edu_ciudad') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                        <option value="Chuquisaca" {{ old('edu_ciudad') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                        <option value="Tarija" {{ old('edu_ciudad') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                        <option value="Pando" {{ old('edu_ciudad') == 'Pando' ? 'selected' : '' }}>Pando</option>
                        <option value="Beni" {{ old('edu_ciudad') == 'Beni' ? 'selected' : '' }}>Beni</option>
                        <option value="Santa Cruz" {{ old('edu_ciudad') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                        <option value="Otra ciudad" {{ old('edu_ciudad') == 'Otra ciudad' ? 'selected' : '' }}>Otra ciudad</option>
                    </select>
                    @error('edu_ciudad')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edu_fecha_inicio" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="edu_fecha_inicio" id="edu_fecha_inicio" 
                                   value="{{ old('edu_fecha_inicio') }}">
                            @error('edu_fecha_inicio')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edu_fecha_fin" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="edu_fecha_fin" id="edu_fecha_fin" 
                                   value="{{ old('edu_fecha_fin') }}">
                            @error('edu_fecha_fin')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="edu_gestion" class="form-label">Gestión <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" max="2030" min="2020" name="edu_gestion" 
                           id="edu_gestion" value="{{ old('edu_gestion', date('Y')) }}">
                    @error('edu_gestion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_medio_verificacion" class="form-label">Medio de Verificación <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="edu_medio_verificacion" id="edu_medio_verificacion" rows="3" 
                              placeholder="Descripción del medio de verificación">{{ old('edu_medio_verificacion') }}</textarea>
                    @error('edu_medio_verificacion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_imagen_medio_verificacion" class="form-label">Archivos de Respaldo</label>
                    <input type="file" class="form-control" name="edu_imagen_medio_verificacion[]" 
                           id="edu_imagen_medio_verificacion" multiple 
                           accept=".jpg,.jpeg,.png,.pdf,.mp4,.mov,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                    <div class="form-text">
                        Puede seleccionar múltiples archivos. Formatos permitidos: imágenes, documentos, videos, audio. 
                        Tamaño máximo: 30MB por archivo.
                    </div>
                    @error('edu_imagen_medio_verificacion.*')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" id="btnGuardar" class="btn btn-success btn-lg me-3">
                <i class="bi bi-check-circle me-2"></i>Crear Actividad
            </button>
            <a href="{{ route('educacion.index') }}" class="btn btn-secondary btn-lg">
                <i class="bi bi-x-circle me-2"></i>Cancelar
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de fechas
    document.getElementById('edu_fecha_inicio').addEventListener('change', function() {
        const fechaInicio = this.value;
        const fechaFin = document.getElementById('edu_fecha_fin');
        if (fechaInicio) {
            fechaFin.min = fechaInicio;
            if (fechaFin.value && fechaFin.value < fechaInicio) {
                fechaFin.value = fechaInicio;
            }
        }
    });

    document.getElementById('edu_fecha_fin').addEventListener('change', function() {
        const fechaFin = this.value;
        const fechaInicio = document.getElementById('edu_fecha_inicio');
        if (fechaFin) {
            fechaInicio.max = fechaFin;
            if (fechaInicio.value && fechaInicio.value > fechaFin) {
                fechaInicio.value = fechaFin;
            }
        }
    });

    // Confirmación con SweetAlert
    document.getElementById('btnGuardar').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Validación básica
        const tema = document.getElementById('edu_tema').value.trim();
        const beneficiarios = document.getElementById('edu_beneficiarios').value;
        const cantidad = document.getElementById('edu_cantidad_beneficiarios').value;
        const ciudad = document.getElementById('edu_ciudad').value;
        const fechaInicio = document.getElementById('edu_fecha_inicio').value;
        const fechaFin = document.getElementById('edu_fecha_fin').value;
        const gestion = document.getElementById('edu_gestion').value;
        const medioVerificacion = document.getElementById('edu_medio_verificacion').value.trim();
        
        if (!tema || !beneficiarios || !cantidad || !ciudad || !fechaInicio || !fechaFin || !gestion || !medioVerificacion) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor, complete todos los campos requeridos.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Formatear fechas
        const fechaInicioFormat = new Date(fechaInicio).toLocaleDateString('es-ES');
        const fechaFinFormat = new Date(fechaFin).toLocaleDateString('es-ES');
        
        Swal.fire({
            title: '¿Crear actividad educativa?',
            html: `
                <div class="text-start">
                    <p><strong>Tema:</strong> ${tema.substring(0, 50)}${tema.length > 50 ? '...' : ''}</p>
                    <p><strong>Beneficiarios:</strong> ${beneficiarios}</p>
                    <p><strong>Cantidad:</strong> ${cantidad} personas</p>
                    <p><strong>Ciudad:</strong> ${ciudad}</p>
                    <p><strong>Fechas:</strong> ${fechaInicioFormat} - ${fechaFinFormat}</p>
                    <p><strong>Gestión:</strong> ${gestion}</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, crear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                document.getElementById('formEducacion').submit();
            }
        });
    });

    // Mostrar mensajes de sesión
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            timer: 3000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
    @endif
});
</script>

<style>
.text-danger {
    font-weight: bold;
}
.form-label {
    font-weight: 600;
    color: #495057;
}
</style>
@endsection