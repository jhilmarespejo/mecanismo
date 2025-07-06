@extends('layouts.app')
@section('title', 'Editar Actividad Educativa')
@section('content')
<div class="container mt-3 p-4 bg-white">
    <h1 class="text-primary fs-2 text-center">Editar Actividad Educativa</h1>
    @include('layouts.breadcrumbs', $breadcrumbs)
    
    <form id="formEducacionEdit" action="{{ route('educacion.update', $educacion->EDU_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="edu_tema" class="form-label">Tema <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="edu_tema" id="edu_tema" rows="3" 
                              placeholder="Descripción del tema de la actividad">{{ old('edu_tema', $educacion->EDU_tema) }}</textarea>
                    @error('edu_tema')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_beneficiarios" class="form-label">Beneficiarios <span class="text-danger">*</span></label>
                    <select name="edu_beneficiarios" id="edu_beneficiarios" class="form-select">
                        <option value="">Selecciona una opción</option>
                        @php
                            $beneficiarios = [
                                'Abogados defensores', 'Abogados del Estado', 'Comisiones de derechos humanos',
                                'Directores de cárceles', 'Fiscales y jueces', 'Guardias Penitenciarios',
                                'Militares', 'Médicos forenses', 'Periodistas', 'Personal administrativo de cárceles',
                                'Personal de salud en centros de detención', 'Personal penitenciario',
                                'Policías y Militares', 'Personal Centro para Adultos Mayores', 'Personal Hospital Psiquiátrico',
                                'Personal Centro de Formacion Militar y Policial', 'Personal Centro para migrantes',
                                'Personal Centro Penitenciario', 'Personal Celda Policial', 'Personal Cuarteles',
                                'Personal Centros de Acogimiento y Reintegración'
                            ];
                        @endphp
                        @foreach($beneficiarios as $beneficiario)
                            <option value="{{ $beneficiario }}" 
                                {{ old('edu_beneficiarios', $educacion->EDU_beneficiarios) == $beneficiario ? 'selected' : '' }}>
                                {{ $beneficiario }}
                            </option>
                        @endforeach
                    </select>
                    @error('edu_beneficiarios')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_cantidad_beneficiarios" class="form-label">Cantidad de Beneficiarios <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="edu_cantidad_beneficiarios" 
                           id="edu_cantidad_beneficiarios" value="{{ old('edu_cantidad_beneficiarios', $educacion->EDU_cantidad_beneficiarios) }}" 
                           min="1" placeholder="Número de personas beneficiadas">
                    @error('edu_cantidad_beneficiarios')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror            
                </div>

                <div class="mb-3">
                    <label for="edu_ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                    <select name="edu_ciudad" id="edu_ciudad" class="form-select">
                        <option value="">Selecciona una ciudad</option>
                        @php
                            $ciudades = ['La Paz', 'Oruro', 'Potosí', 'Cochabamba', 'Chuquisaca', 'Tarija', 'Pando', 'Beni', 'Santa Cruz', 'Otra ciudad'];
                        @endphp
                        @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad }}" 
                                {{ old('edu_ciudad', $educacion->EDU_ciudad) == $ciudad ? 'selected' : '' }}>
                                {{ $ciudad }}
                            </option>
                        @endforeach
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
                                   value="{{ old('edu_fecha_inicio', $educacion->EDU_fecha_inicio) }}">
                            @error('edu_fecha_inicio')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edu_fecha_fin" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="edu_fecha_fin" id="edu_fecha_fin" 
                                   value="{{ old('edu_fecha_fin', $educacion->EDU_fecha_fin) }}">
                            @error('edu_fecha_fin')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="edu_gestion" class="form-label">Gestión <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" max="2030" min="2020" name="edu_gestion" 
                           id="edu_gestion" value="{{ old('edu_gestion', $educacion->EDU_gestion) }}">
                    @error('edu_gestion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="edu_medio_verificacion" class="form-label">Medio de Verificación <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="edu_medio_verificacion" id="edu_medio_verificacion" rows="3" 
                              placeholder="Descripción del medio de verificación">{{ old('edu_medio_verificacion', $educacion->EDU_medio_verificacion) }}</textarea>
                    @error('edu_medio_verificacion')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Archivos actuales -->
        @if($educacion->EDU_imagen_medio_verificacion)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Archivos Actuales</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $archivos = json_decode($educacion->EDU_imagen_medio_verificacion, true);
                                if (!is_array($archivos)) {
                                    $archivos = [$educacion->EDU_imagen_medio_verificacion];
                                }
                            @endphp

                            <div class="row">
                                @foreach($archivos as $index => $archivo)
                                    @php
                                        $rutaCompleta = storage_path('app/public/' . $archivo);
                                        $rutaPublica = asset('storage/' . $archivo);
                                        $nombreArchivo = basename($archivo);
                                        $extension = pathinfo($archivo, PATHINFO_EXTENSION);
                                        $esImagen = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $esPDF = strtolower($extension) == 'pdf';
                                    @endphp

                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                @if($esImagen && file_exists($rutaCompleta))
                                                    <img src="{{ $rutaPublica }}" class="img-fluid mb-2" 
                                                         style="max-height: 100px; cursor: pointer;" 
                                                         onclick="verImagen('{{ $rutaPublica }}', '{{ $nombreArchivo }}')"
                                                         alt="Imagen actual">
                                                @elseif($esPDF)
                                                    <i class="bi bi-file-pdf text-danger" style="font-size: 2rem;"></i>
                                                @else
                                                    <i class="bi bi-file-earmark text-secondary" style="font-size: 2rem;"></i>
                                                @endif
                                                
                                                <h6 class="card-title mt-2">{{ Str::limit($nombreArchivo, 15) }}</h6>
                                                <small class="text-muted">{{ strtoupper($extension) }}</small>
                                                
                                                <div class="mt-2">
                                                    @if($esImagen)
                                                        <button type="button" class="btn btn-sm btn-primary" 
                                                                onclick="verImagen('{{ $rutaPublica }}', '{{ $nombreArchivo }}')">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @elseif($esPDF)
                                                        <button type="button" class="btn btn-sm btn-primary" 
                                                                onclick="verPDF('{{ $rutaPublica }}', '{{ $nombreArchivo }}')">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <a href="{{ $rutaPublica }}" download class="btn btn-sm btn-success">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Nuevos archivos -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="edu_imagen_medio_verificacion" class="form-label">
                        Nuevos Archivos de Respaldo 
                        <small class="text-muted">(Reemplazarán los actuales si se seleccionan)</small>
                    </label>
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
            <button type="button" id="btnActualizar" class="btn btn-success btn-lg me-3">
                <i class="bi bi-check-circle me-2"></i>Actualizar Actividad
            </button>
            <a href="{{ route('educacion.show', $educacion->EDU_id) }}" class="btn btn-info btn-lg me-2">
                <i class="bi bi-eye me-2"></i>Ver Detalles
            </a>
            <a href="{{ route('educacion.index') }}" class="btn btn-secondary btn-lg">
                <i class="bi bi-x-circle me-2"></i>Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Modales para ver archivos (igual que en show.blade.php) -->
<div class="modal fade" id="modalImagen" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitle">Ver Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagenSrc" src="" class="img-fluid" alt="Imagen">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPDF" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPDFTitle">Ver PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="modalPDFSrc" src="" width="100%" height="600px"></iframe>
            </div>
        </div>
    </div>
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
    document.getElementById('btnActualizar').addEventListener('click', function(e) {
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
            title: '¿Actualizar actividad?',
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
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Actualizando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                document.getElementById('formEducacionEdit').submit();
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

// Funciones para ver archivos
function verImagen(src, nombre) {
    document.getElementById('modalImagenSrc').src = src;
    document.getElementById('modalImagenTitle').textContent = nombre;
    new bootstrap.Modal(document.getElementById('modalImagen')).show();
}

function verPDF(src, nombre) {
    document.getElementById('modalPDFSrc').src = src;
    document.getElementById('modalPDFTitle').textContent = nombre;
    new bootstrap.Modal(document.getElementById('modalPDF')).show();
}
</script>

<style>
.text-danger {
    font-weight: bold;
}
.form-label {
    font-weight: 600;
    color: #495057;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection