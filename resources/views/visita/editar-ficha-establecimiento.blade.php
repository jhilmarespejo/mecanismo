{{-- Archivo: resources/views/visita/editar-establecimiento.blade.php --}}
{{-- Este archivo se incluirá dentro del modal, NO es una página completa --}}

<div class="modal-header bg-primary text-white">
    <h5 class="modal-title" id="modalEditarEstablecimientoLabel">
        <i class="bi bi-pencil-square me-2"></i>
        Editar Información del Establecimiento
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-0">
    <div class="ficha-body">
        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Gestión {{ $anioActual ?? date('Y') }}:</strong> Los cambios se aplicarán para el año actual
        </div>
        
        <form id="formEditarEstablecimiento" action="{{ route('visita.actualizarFichaEstablecimiento', $establecimiento->EST_id ?? 0) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Información No Editable -->
            
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nombre del Centro</label>
                        <div class="readonly-info">
                            <i class="bi bi-building me-2"></i>
                            {{ $establecimiento->EST_nombre ?? 'No disponible' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tipo de Institución</label>
                        <div class="readonly-info">
                            <i class="bi bi-tag me-2"></i>
                            {{ $establecimiento->TES_tipo ?? 'No disponible' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información de Ubicación -->
           
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="EST_departamento" class="form-label">
                            Departamento <span class="required">*</span>
                        </label>
                        <select class="form-control" id="EST_departamento" name="EST_departamento" required>
                            <option value="">Seleccionar departamento</option>
                            <option value="La Paz" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                            <option value="Cochabamba" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                            <option value="Santa Cruz" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                            <option value="Oruro" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                            <option value="Potosí" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                            <option value="Chuquisaca" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                            <option value="Tarija" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                            <option value="Beni" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Beni' ? 'selected' : '' }}>Beni</option>
                            <option value="Pando" {{ old('EST_departamento', $establecimiento->EST_departamento ?? '') == 'Pando' ? 'selected' : '' }}>Pando</option>
                        </select>
                        <div class="invalid-feedback" id="error-EST_departamento"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="EST_municipio" class="form-label">
                            Municipio <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="EST_municipio" name="EST_municipio" 
                               value="{{ old('EST_municipio', $establecimiento->EST_municipio ?? '') }}" 
                               placeholder="Nombre del municipio" required>
                        <div class="invalid-feedback" id="error-EST_municipio"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="EST_telefono_contacto" class="form-label">Teléfono de Contacto</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" class="form-control" id="EST_telefono_contacto" name="EST_telefono_contacto" 
                                   value="{{ old('EST_telefono_contacto', $establecimiento->EST_telefono_contacto ?? '') }}" 
                                   placeholder="Número de teléfono">
                        </div>
                        <div class="invalid-feedback" id="error-EST_telefono_contacto"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="EST_direccion" class="form-label">
                            Dirección Completa <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="EST_direccion" name="EST_direccion" 
                               value="{{ old('EST_direccion', $establecimiento->EST_direccion ?? '') }}" 
                               placeholder="Calle, avenida, número, zona, etc." required>
                        <div class="invalid-feedback" id="error-EST_direccion"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="EST_anyo_funcionamiento" class="form-label">Año de Funcionamiento</label>
                        <input type="number" class="form-control" id="EST_anyo_funcionamiento" name="EST_anyo_funcionamiento" 
                               value="{{ old('EST_anyo_funcionamiento', $establecimiento->EST_anyo_funcionamiento ?? '') }}" 
                               min="1900" max="{{ date('Y') }}" placeholder="Año">
                        <div class="invalid-feedback" id="error-EST_anyo_funcionamiento"></div>
                    </div>
                </div>
            </div>
            <!-- Información Operacional -->
           
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EINF_poblacion_atendida" class="form-label">Población a la que Atiende</label>
                        <input type="text" class="form-control" id="EINF_poblacion_atendida" name="EINF_poblacion_atendida" 
                               value="{{ old('EINF_poblacion_atendida', $establecimiento->EINF_poblacion_atendida ?? '') }}" 
                               placeholder="Ej: Adultos mayores, Personas privadas de libertad, etc.">
                        <div class="invalid-feedback" id="error-EINF_poblacion_atendida"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EINF_cantidad_actual_internos" class="form-label">Cantidad Población Actual</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-check"></i></span>
                            <input type="text" class="form-control" id="EINF_cantidad_actual_internos" name="EINF_cantidad_actual_internos" 
                                   value="{{ old('EINF_cantidad_actual_internos', $establecimiento->EINF_cantidad_actual_internos ?? '') }}" 
                                   placeholder="Número actual de personas">
                        </div>
                        <div class="invalid-feedback" id="error-EINF_cantidad_actual_internos"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EST_capacidad_creacion" class="form-label">Capacidad de Alojamiento</label>
                        <input type="text" class="form-control" id="EST_capacidad_creacion" name="EST_capacidad_creacion" 
                               value="{{ old('EST_capacidad_creacion', $establecimiento->EST_capacidad_creacion ?? '') }}" 
                               placeholder="Capacidad máxima del establecimiento">
                        <div class="invalid-feedback" id="error-EST_capacidad_creacion"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EINF_derecho_propietario" class="form-label">Derecho Propietario</label>
                        <select class="form-control" id="EINF_derecho_propietario" name="EINF_derecho_propietario">
                            <option value="">Seleccionar tipo</option>
                            <option value="Propio" {{ old('EINF_derecho_propietario', $establecimiento->EINF_derecho_propietario ?? '') == 'Propio' ? 'selected' : '' }}>Propio</option>
                            <option value="Alquilado" {{ old('EINF_derecho_propietario', $establecimiento->EINF_derecho_propietario ?? '') == 'Alquilado' ? 'selected' : '' }}>Alquilado</option>
                            <option value="Cedido" {{ old('EINF_derecho_propietario', $establecimiento->EINF_derecho_propietario ?? '') == 'Cedido' ? 'selected' : '' }}>Cedido</option>
                            <option value="Comodato" {{ old('EINF_derecho_propietario', $establecimiento->EINF_derecho_propietario ?? '') == 'Comodato' ? 'selected' : '' }}>Comodato</option>
                            <option value="Otro" {{ old('EINF_derecho_propietario', $establecimiento->EINF_derecho_propietario ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        <div class="invalid-feedback" id="error-EINF_derecho_propietario"></div>
                    </div>
                </div>
            </div>
            
            <!-- Información de Infraestructura -->
            
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EINF_superficie_terreno" class="form-label">Superficie del Terreno</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="EINF_superficie_terreno" name="EINF_superficie_terreno" 
                                   value="{{ old('EINF_superficie_terreno', $establecimiento->EINF_superficie_terreno ?? '') }}" 
                                   placeholder="0.00">
                            <span class="input-group-text">m²</span>
                        </div>
                        <div class="invalid-feedback" id="error-EINF_superficie_terreno"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EINF_superficie_construida" class="form-label">Superficie Construida</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" id="EINF_superficie_construida" name="EINF_superficie_construida" 
                                   value="{{ old('EINF_superficie_construida', $establecimiento->EINF_superficie_construida ?? '') }}" 
                                   placeholder="0.00">
                            <span class="input-group-text">m²</span>
                        </div>
                        <div class="invalid-feedback" id="error-EINF_superficie_construida"></div>
                    </div>
                </div>
            </div>
            
            <!-- Información del Responsable -->
           
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EPER_nombre_responsable" class="form-label">Nombre del Responsable</label>
                        <input type="text" class="form-control" id="EPER_nombre_responsable" name="EPER_nombre_responsable" 
                               value="{{ old('EPER_nombre_responsable', $responsable->EPER_nombre_responsable ?? '') }}" 
                               placeholder="Nombre completo del responsable">
                        <div class="invalid-feedback" id="error-EPER_nombre_responsable"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EPER_grado_profesion" class="form-label">Grado/Profesión</label>
                        <input type="text" class="form-control" id="EPER_grado_profesion" name="EPER_grado_profesion" 
                               value="{{ old('EPER_grado_profesion', $responsable->EPER_grado_profesion ?? '') }}" 
                               placeholder="Ej: Mayor, Licenciado, etc.">
                        <div class="invalid-feedback" id="error-EPER_grado_profesion"></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EPER_telefono" class="form-label">Teléfono del Responsable</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                            <input type="text" class="form-control" id="EPER_telefono" name="EPER_telefono" 
                                   value="{{ old('EPER_telefono', $responsable->EPER_telefono ?? '') }}" 
                                   placeholder="Número de teléfono personal">
                        </div>
                        <div class="invalid-feedback" id="error-EPER_telefono"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="EPER_email" class="form-label">Email del Responsable</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="EPER_email" name="EPER_email" 
                                   value="{{ old('EPER_email', $responsable->EPER_email ?? '') }}" 
                                   placeholder="correo@ejemplo.com">
                        </div>
                        <div class="invalid-feedback" id="error-EPER_email"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>