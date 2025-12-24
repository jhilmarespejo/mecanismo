{{-- resources/views/includes/modal-editar-recomendacion-estatal.blade.php --}}
<div class="modal fade" id="modalEditarRecomendacion" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Editar Recomendación Estatal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formEditarRecomendacion" method="POST" enctype="multipart/form-data" action="{{ route('recomendaciones.actualizar-estatal') }}">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="REC_id" id="edit_REC_id">
                
                <div class="modal-body">
                    <!-- Recomendación -->
                    <div class="mb-3">
                        <label for="edit_REC_recomendacion" class="form-label fw-bold">Recomendación <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control @error('REC_recomendacion') is-invalid @enderror" 
                            id="edit_REC_recomendacion" 
                            name="REC_recomendacion" 
                            rows="4" 
                            placeholder="Ingrese la recomendación..." 
                            required
                        ></textarea>
                        <div class="invalid-feedback" id="edit_REC_recomendacion_error"></div>
                    </div>
                    
                    <!-- Fecha de Recomendación -->
                    <div class="mb-3">
                        <label for="edit_REC_fechaRecomendacion" class="form-label fw-bold">Fecha de Recomendación <span class="text-danger">*</span></label>
                        <input 
                            type="date" 
                            class="form-control @error('REC_fechaRecomendacion') is-invalid @enderror" 
                            id="edit_REC_fechaRecomendacion" 
                            name="REC_fechaRecomendacion" 
                            required
                        >
                        <div class="invalid-feedback" id="edit_REC_fechaRecomendacion_error"></div>
                    </div>
                    
                    <!-- Nivel de Cumplimiento -->
                    <div class="mb-3">
                        <label for="edit_REC_cumplimiento" class="form-label fw-bold">Nivel de Cumplimiento</label>
                        <select 
                            class="form-select @error('REC_cumplimiento') is-invalid @enderror" 
                            id="edit_REC_cumplimiento" 
                            name="REC_cumplimiento"
                        >
                            <option value="">Seleccionar...</option>
                            <option value="0">Recomendación No Cumplida</option>
                            <option value="1">Recomendación Cumplida</option>
                            <option value="2">Recomendación Parcialmente Cumplida</option>
                        </select>
                        <div class="invalid-feedback" id="edit_REC_cumplimiento_error"></div>
                    </div>
                    
                    <!-- Autoridad Competente -->
                    <div class="mb-3">
                        <label for="edit_REC_autoridad_competente" class="form-label fw-bold">Autoridad Competente <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('REC_autoridad_competente') is-invalid @enderror" 
                            id="edit_REC_autoridad_competente" 
                            name="REC_autoridad_competente" 
                            placeholder="Ingrese la autoridad competente..." 
                            required
                        >
                        <div class="invalid-feedback" id="edit_REC_autoridad_competente_error"></div>
                    </div>
                    
                    <!-- Archivos existentes -->
                    <div class="mb-3" id="archivosExistentesContainer">
                        <label class="form-label fw-bold">Archivos Adjuntos Existentes</label>
                        <div class="alert alert-info p-2" id="sinArchivosExistentes">
                            <i class="bi bi-info-circle"></i> No hay archivos adjuntos:
                        </div>
                        <div id="listaArchivosExistentes" class="d-none">
                            <!-- Los archivos existentes se cargarán aquí dinámicamente -->
                            
                        </div>
                    </div>
                    
                    <!-- Nuevos archivos -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Agregar Nuevos Archivos</label>
                        <small class="text-muted d-block mb-2">Formatos permitidos: imágenes, documentos, PDF, videos, audio (máx. 30MB cada uno)</small>
                        
                        <div id="nuevosArchivosContainer">
                            <!-- Los inputs de nuevos archivos se agregarán aquí dinámicamente -->
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btnAgregarArchivo">
                            <i class="bi bi-plus-circle"></i> Agregar Archivo
                        </button>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarCambios">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerGuardar"></span>
                        <span id="textoGuardar">Guardar Cambios</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template para nuevo archivo -->
<template id="templateArchivoNuevo">
    <div class="archivo-item border rounded p-3 mb-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold">Archivo <span class="numero-archivo">1</span></span>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar-archivo">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="mb-2">
            <input type="file" 
                   class="form-control form-control-sm" 
                   name="ARC_archivo_nuevo[]" 
                   accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp3,.wav,.ogg"
                   data-max-size="31457280">
            <small class="text-danger error-archivo d-none"></small>
        </div>
        <div>
            <input type="text" 
                   class="form-control form-control-sm" 
                   name="ARC_descripcion_nuevo[]" 
                   placeholder="Descripción del archivo...">
            <small class="text-danger error-descripcion d-none"></small>
        </div>
    </div>
</template>

<!-- Template para archivo existente -->
<template id="templateArchivoExistente">
    <div class="archivo-existente border rounded p-2 mb-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-paperclip me-2"></i>
                <span class="nombre-archivo">archivo.pdf</span>
                <small class="text-muted ms-2 descripcion-archivo">- Sin descripción</small>
            </div>
            <div>
                <a href="#" class="btn btn-sm btn-outline-info btn-ver-archivo" target="_blank" title="Ver archivo">
                    <i class="bi bi-eye"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-existente" title="Eliminar archivo">
                    <i class="bi bi-trash"></i>
                </button>
                <input type="hidden" name="archivos_eliminados[]" class="input-eliminado" value="">
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalEditarRecomendacion');
    const form = document.getElementById('formEditarRecomendacion');
    const btnAgregarArchivo = document.getElementById('btnAgregarArchivo');
    const nuevosArchivosContainer = document.getElementById('nuevosArchivosContainer');
    const listaArchivosExistentes = document.getElementById('listaArchivosExistentes');
    const sinArchivosExistentes = document.getElementById('sinArchivosExistentes');
    const templateArchivoNuevo = document.getElementById('templateArchivoNuevo');
    const templateArchivoExistente = document.getElementById('templateArchivoExistente');
    
    let contadorArchivos = 0;
    let archivosExistentes = [];
    
    // Inicializar modal
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const recomendacionId = button.getAttribute('data-id');
        const recomendacionTexto = button.getAttribute('data-texto');
        const fechaRecomendacion = button.getAttribute('data-fecha');
        const cumplimiento = button.getAttribute('data-cumplimiento');
        const autoridad = button.getAttribute('data-autoridad');
        const archivosData = button.getAttribute('data-archivos');
        
        // Limpiar formulario
        form.reset();
        nuevosArchivosContainer.innerHTML = '';
        contadorArchivos = 0;
        archivosExistentes = [];
        
        // Llenar datos del formulario
        document.getElementById('edit_REC_id').value = recomendacionId;
        document.getElementById('edit_REC_recomendacion').value = recomendacionTexto || '';
        document.getElementById('edit_REC_fechaRecomendacion').value = fechaRecomendacion || '';
        document.getElementById('edit_REC_cumplimiento').value = cumplimiento || '';
        document.getElementById('edit_REC_autoridad_competente').value = autoridad || '';
        
        // Procesar archivos existentes
        if (archivosData) {
            try {
                archivosExistentes = JSON.parse(archivosData);
                mostrarArchivosExistentes(archivosExistentes);
            } catch (e) {
                console.error('Error al parsear archivos:', e);
                mostrarSinArchivos();
            }
        } else {
            mostrarSinArchivos();
        }
        
        // Quitar clases de validación
        limpiarErroresValidacion();
    });
    
    // Función para mostrar archivos existentes
    function mostrarArchivosExistentes(archivos) {
        if (archivos.length === 0) {
            mostrarSinArchivos();
            return;
        }
        
        listaArchivosExistentes.innerHTML = '';
        listaArchivosExistentes.classList.remove('d-none');
        sinArchivosExistentes.classList.add('d-none');
        
        archivos.forEach((archivo, index) => {
            const clone = templateArchivoExistente.content.cloneNode(true);
            const nombreElement = clone.querySelector('.nombre-archivo');
            const descripcionElement = clone.querySelector('.descripcion-archivo');
            const btnVer = clone.querySelector('.btn-ver-archivo');
            const btnEliminar = clone.querySelector('.btn-eliminar-existente');
            const inputEliminado = clone.querySelector('.input-eliminado');
            
            nombreElement.textContent = archivo.ARC_NombreOriginal || 'archivo';
            descripcionElement.textContent = archivo.ARC_descripcion ? `- ${archivo.ARC_descripcion}` : '- Sin descripción';
            
            if (archivo.ARC_ruta) {
                btnVer.href = "{{ url('') }}/" + archivo.ARC_ruta.replace('public/', 'storage/');
            } else {
                btnVer.style.display = 'none';
            }
            
            btnEliminar.addEventListener('click', function() {
                inputEliminado.value = archivo.ARC_id;
                this.closest('.archivo-existente').classList.add('opacity-50', 'bg-light');
                btnVer.classList.add('disabled');
            });
            
            inputEliminado.value = '';
            
            listaArchivosExistentes.appendChild(clone);
        });
    }
    
    function mostrarSinArchivos() {
        listaArchivosExistentes.classList.add('d-none');
        sinArchivosExistentes.classList.remove('d-none');
    }
    
    // Agregar nuevo archivo
    btnAgregarArchivo.addEventListener('click', function() {
        contadorArchivos++;
        const clone = templateArchivoNuevo.content.cloneNode(true);
        const archivoItem = clone.querySelector('.archivo-item');
        const numeroArchivo = clone.querySelector('.numero-archivo');
        const btnEliminar = clone.querySelector('.btn-eliminar-archivo');
        const inputFile = clone.querySelector('input[type="file"]');
        
        numeroArchivo.textContent = contadorArchivos;
        
        // Validar tamaño de archivo
        inputFile.addEventListener('change', function() {
            const maxSize = parseInt(this.getAttribute('data-max-size'));
            if (this.files[0] && this.files[0].size > maxSize) {
                mostrarErrorArchivo(this, 'El archivo debe ser menor a 30MB');
                this.value = '';
            } else {
                limpiarErrorArchivo(this);
            }
        });
        
        // Eliminar este archivo
        btnEliminar.addEventListener('click', function() {
            archivoItem.remove();
        });
        
        nuevosArchivosContainer.appendChild(clone);
    });
    
    // Validación del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return;
        }
        
        // Mostrar spinner
        const spinner = document.getElementById('spinnerGuardar');
        const textoGuardar = document.getElementById('textoGuardar');
        const btnGuardar = document.getElementById('btnGuardarCambios');
        
        spinner.classList.remove('d-none');
        textoGuardar.textContent = 'Guardando...';
        btnGuardar.disabled = true;
        
        // Enviar formulario con AJAX
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            spinner.classList.add('d-none');
            textoGuardar.textContent = 'Guardar Cambios';
            btnGuardar.disabled = false;
            
            if (data.errors) {
                mostrarErroresValidacion(data.errors);
            } else if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.success,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                    location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            spinner.classList.add('d-none');
            textoGuardar.textContent = 'Guardar Cambios';
            btnGuardar.disabled = false;
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al guardar los cambios',
                confirmButtonText: 'Aceptar'
            });
        });
    });
    
    // Funciones auxiliares
    function validarFormulario() {
        let valido = true;
        const camposRequeridos = [
            'edit_REC_recomendacion',
            'edit_REC_fechaRecomendacion',
            'edit_REC_autoridad_competente'
        ];
        
        camposRequeridos.forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (!campo.value.trim()) {
                mostrarError(campo, 'Este campo es requerido');
                valido = false;
            } else {
                limpiarError(campo);
            }
        });
        
        return valido;
    }
    
    function mostrarError(elemento, mensaje) {
        elemento.classList.add('is-invalid');
        const errorElement = document.getElementById(elemento.id + '_error');
        if (errorElement) {
            errorElement.textContent = mensaje;
        }
    }
    
    function limpiarError(elemento) {
        elemento.classList.remove('is-invalid');
        const errorElement = document.getElementById(elemento.id + '_error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
    
    function limpiarErroresValidacion() {
        const elementos = form.querySelectorAll('.is-invalid');
        elementos.forEach(el => el.classList.remove('is-invalid'));
        
        const errores = form.querySelectorAll('.invalid-feedback');
        errores.forEach(el => el.textContent = '');
    }
    
    function mostrarErroresValidacion(errors) {
        limpiarErroresValidacion();
        
        Object.keys(errors).forEach(key => {
            const campoId = 'edit_' + key;
            const campo = document.getElementById(campoId);
            const errorElement = document.getElementById(campoId + '_error');
            
            if (campo && errorElement) {
                campo.classList.add('is-invalid');
                errorElement.textContent = errors[key][0];
            }
        });
    }
    
    function mostrarErrorArchivo(input, mensaje) {
        const errorElement = input.closest('.archivo-item').querySelector('.error-archivo');
        errorElement.textContent = mensaje;
        errorElement.classList.remove('d-none');
        input.classList.add('is-invalid');
    }
    
    function limpiarErrorArchivo(input) {
        const errorElement = input.closest('.archivo-item').querySelector('.error-archivo');
        errorElement.textContent = '';
        errorElement.classList.add('d-none');
        input.classList.remove('is-invalid');
    }
});
</script>

<style>
.archivo-item {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.archivo-item:hover {
    background-color: #e9ecef;
}

.archivo-existente {
    background-color: #f0f7ff;
    transition: all 0.3s ease;
}

.archivo-existente:hover {
    background-color: #e3f2fd;
}

.btn-eliminar-archivo, .btn-eliminar-existente {
    padding: 0.15rem 0.5rem;
    font-size: 0.875rem;
}

.nombre-archivo {
    font-weight: 500;
    color: #0d6efd;
}

.descripcion-archivo {
    font-size: 0.875rem;
}
</style>