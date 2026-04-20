<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">

<style>
    :root { 
        --guinda: #773357; 
        --guinda-light: #fdf2f7; 
        --guinda-hover: #5a2642; 
        --gris-fondo: #f4f6f9; 
        --azul-pdf: #0d6efd;
    }
    
    body { background-color: var(--gris-fondo); font-family: 'Montserrat', sans-serif; }
    
    /* Tarjetas y Contenedores */
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
    .card-header { background-color: white !important; border-bottom: 1px solid var(--guinda-light); padding: 1.25rem; border-radius: 15px 15px 0 0 !important; }
    
    /* Tipografía y Colores */
    .text-guinda { color: var(--guinda); }
    .bg-aliceblue { background-color: #f0f8ff !important; }

    /* --- FORZAR MAYÚSCULAS EN TODO EL MODAL --- */
    #modalEdicion input, 
    #modalEdicion select, 
    #modalEdicion textarea,
    #resumenCaptura .fw-bold { 
        text-transform: uppercase; 
    }

    /* --- CORRECCIÓN DE ALINEACIÓN (Labels consistentes) --- */
    #formCaptura label {
        display: block;
        min-height: 2.2rem; /* Altura mínima para que los inputs se alineen horizontalmente */
        margin-bottom: 0.25rem;
        line-height: 1.1;
        display: flex;
        align-items: flex-end; /* Alinea el texto de la etiqueta al fondo */
    }
    
    /* Botones Profesionales */
    .btn-guinda { background-color: var(--guinda); color: white; border-radius: 10px; font-weight: 600; padding: 10px 22px; border: none; transition: 0.3s; }
    .btn-guinda:hover { background-color: var(--guinda-hover); color: white; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    
    .btn-primary-custom { background-color: var(--azul-pdf); color: white; border-radius: 10px; font-weight: 600; padding: 10px 22px; border: none; transition: 0.3s; }
    .btn-primary-custom:hover { background-color: #0b5ed7; color: white; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3); }

    /* Tablas y Fases */
    .table thead th { background-color: var(--guinda) !important; color: white !important; text-transform: uppercase; font-size: 0.7rem; padding: 12px; border: none; }
    .badge-fase { border-radius: 50px; padding: 6px 12px; font-weight: 700; font-size: 0.65rem; text-transform: uppercase; }
    .fase-EMPADRONADO { background-color: #6c757d; color: white; }
    .fase-VALIDACION_DOCS { background-color: #17a2b8; color: white; }
    .fase-EN_REVISION { background-color: #ffc107; color: #333; }
    .fase-APROBADO { background-color: #28a745; color: white; }
    .fase-RECHAZADO { background-color: #dc3545; color: white; }

    /* Modal y Footer Quirúrgico */
    .modal-content { border-radius: 20px; overflow: hidden; }
    .modal-header { border-bottom: none; padding: 1.5rem; }
    .modal-footer { 
        padding: 1.25rem 1.5rem; 
        border-top: 1px solid #eee; 
        background: #fcfcfc !important; 
        z-index: 1055; 
    }

    /* Pestañas (Tabs) */
    .nav-tabs { border-bottom: 2px solid #eee; }
    .nav-tabs .nav-link { border: none; color: #888; font-weight: 700; padding: 1.2rem; font-size: 0.85rem; border-bottom: 3px solid transparent; }
    .nav-tabs .nav-link.active { color: var(--guinda); border-bottom: 3px solid var(--guinda); background: transparent; }
    .nav-tabs .nav-link:hover { color: var(--guinda); background: var(--guinda-light); }
    
    /* Utilidades */
    .pagination .page-link { color: var(--guinda); border: none; margin: 0 3px; border-radius: 8px !important; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--guinda) !important; color: white !important; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    /* Estilos para carga de archivos UI/UX */
.doc-row {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.doc-row:hover {
    background-color: var(--guinda-light) !important;
    border-left: 4px solid var(--guinda);
}
.file-upload-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-upload {
    border: 2px dashed #ccc;
    color: #666;
    padding: 5px 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0;
}
.btn-upload:hover {
    border-color: var(--guinda);
    color: var(--guinda);
    background: white;
}
.file-preview-badge {
    background: #e9ecef;
    padding: 4px 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    max-width: 200px;
}
.file-name-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.7rem;
    font-weight: 600;
}
.remove-file {
    color: #dc3545;
    cursor: pointer;
    font-size: 0.9rem;
}
/* Agrega el color para la nueva fase */
.fase-SOLICITUD_INGRESADA { background-color: #17a2b8; color: white; } /* Azul turquesa */
/* Diseño de miniaturas de evidencia */
.foto-evidencia-wrapper {
    position: relative;
    aspect-ratio: 1/1;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    background-color: #f8f9fa;
}
.foto-evidencia-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.btn-delete-foto {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 12px;
    cursor: pointer;
    z-index: 10;
}
.btn-outline-guinda {
    border: 1px solid var(--guinda);
    color: var(--guinda);
    font-weight: 600;
}
.btn-outline-guinda:hover {
    background: var(--guinda);
    color: white;
}
</style>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-7">
            <h2 class="fw-bold text-guinda mb-0">Expediente Digital: Tlalpan</h2>
            <p class="text-muted">Validación de documentos y seguimiento de productores</p>
        </div>
        <div class="col-md-5 text-end">
            <button onclick="location.reload()" class="btn btn-guinda shadow-sm"><i class="fas fa-sync-alt me-2"></i>Sincronizar</button>
            <button onclick="confirmarSalida()" class="btn btn-danger rounded-3 ms-2"><i class="fas fa-power-off"></i></button>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-2 mb-2">
            <div class="card p-3 border-start border-4 border-secondary shadow-sm">
                <h6 class="text-muted small mb-1">TOTAL REGISTROS</h6>
                <h3 class="fw-bold mb-0" id="kpi-total">0</h3>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card p-3 border-start border-4 border-primary shadow-sm">
                <h6 class="text-muted small mb-1">SOLICITUDES INGRESADAS</h6>
                <h3 class="fw-bold mb-0" id="kpi-solicitudes">0</h3>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <div class="card p-3 border-start border-4 border-info shadow-sm">
                <h6 class="text-muted small mb-1">VALIDACIÓN DOCS</h6>
                <h3 class="fw-bold mb-0" id="kpi-pendientes">0</h3>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <div class="card p-3 border-start border-4 border-warning shadow-sm">
                <h6 class="text-muted small mb-1">EN REVISIÓN</h6>
                <h3 class="fw-bold mb-0" id="kpi-revision">0</h3>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card p-3 border-start border-4 border-success shadow-sm">
                <h6 class="text-muted small mb-1">TOTAL APROBADOS</h6>
                <h3 class="fw-bold mb-0" id="kpi-aprobados">0</h3>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-list me-2"></i>Bandeja de Entrada de Expedientes</h6>
            <input type="text" id="tablaSearch" class="form-control form-control-sm w-25 shadow-sm" placeholder="Buscar por folio o nombre...">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaCaptura">
                    <thead>
                        <tr>
                            <th class="ps-3">Folio</th>
                            <th>Productor</th>
                            <th>Fase del Proceso</th>
                            <th class="text-center">Hectáreas</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="small text-muted" id="tableInfo"></div>
                <nav><ul class="pagination pagination-sm mb-0" id="paginationControls"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdicion" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-guinda text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-folder-open me-2"></i>EXPEDIENTE: <span id="spanFolio"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <ul class="nav nav-tabs nav-fill bg-white border-bottom" id="tabExpediente">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-datos"><i class="fas fa-search me-1"></i> 1. DATOS CAPTURADOS</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-extra"><i class="fas fa-edit me-1"></i> 2. CAPTURA EXTRA</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-docs"><i class="fas fa-file-check me-1"></i> 3. DOCUMENTACIÓN</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-verificacion"><i class="fas fa-file-check me-1"></i> 3. VERIFICACIÓN</a></li>
            </ul>

            <div class="modal-body bg-light" style="max-height: 70vh; overflow-y: auto;">
                <form id="formCaptura">
                    <input type="hidden" id="reg_id" name="id">
                    <div class="tab-content">
                        
                        <div class="tab-pane fade show active" id="tab-datos">
                            <div class="row g-3" id="resumenCaptura">
                                </div>
                        </div>

            <div class="tab-pane fade" id="tab-extra">
                <div class="alert alert-warning border-0 shadow-sm small mb-3">
                    <i class="fas fa-edit me-2"></i> <b>Modo Edición:</b> Los campos en azul se recuperaron de la encuesta. Los campos en blanco son para completar el expediente oficial.
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold text-guinda"><i class="fas fa-id-card me-2"></i>1. Identidad y Registro Oficial</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="small fw-bold">Nombre del Productor</label>
                                <input type="text" class="form-control form-control-sm bg-aliceblue" name="nombre_productor" id="in_nombre_productor">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Apellido Paterno</label>
                                <input type="text" class="form-control form-control-sm" name="paterno" id="in_paterno">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Apellido Materno</label>
                                <input type="text" class="form-control form-control-sm" name="materno" id="in_materno">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-primary">CURP (Validado)</label>
                                <input type="text" class="form-control form-control-sm" name="curp" id="in_curp_edit" maxlength="18">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">RFC</label>
                                <input type="text" class="form-control form-control-sm" name="rfc" id="in_rfc" placeholder="ABCD123456XYZ">
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">Tipo de ID</label>
                                <select class="form-select form-select-sm" name="tipo_id" id="in_tipo_id">
                                    <option value="INE">INE</option>
                                    <option value="PASAPORTE">Pasaporte</option>
                                    <option value="CARTILLA_MILITAR">Cartilla Militar</option>
                                    <option value="CEDULA_PROFESIONAL">Cédula Profesional</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">Num. Identificación</label>
                                <input type="text" class="form-control form-control-sm" name="numero_id" id="in_numero_id">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold text-guinda"><i class="fas fa-users me-2"></i>2. Perfil y Vulnerabilidad</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="small fw-bold">Estado Civil</label>
                                <select class="form-select form-select-sm" name="estado_civil" id="in_estado_civil">
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="SOLTERA">Soltera (o)</option>
                                    <option value="CASADA">Casada (o)</option>
                                    <option value="DIVORCIADA">Divorciada (o)</option>
                                    <option value="VIUDA">Viuda (o)</option>
                                    <option value="UNION_LIBRE">Unión libre</option>
                                    <option value="NA">NA</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Grado de Estudios</label>
                                <select class="form-select form-select-sm" name="grado_estudios" id="in_estudios">
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="SIN_ESTUDIOS">Sin estudios</option>
                                    <option value="PRIMARIA">Primaria</option>
                                    <option value="SECUNDARIA">Secundaria</option>
                                    <option value="BACHILLERATO">Bachillerato/Preparatoria</option>
                                    <option value="CARRERA_TECNICA">Carrera Técnica</option>
                                    <option value="LICENCIATURA">Licenciatura</option>
                                    <option value="POSGRADO">Posgrado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Ocupación</label>
                                <input type="text" class="form-control form-control-sm" name="ocupacion" id="in_ocupacion">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">¿Tiene alguna discapacidad?</label>
                                <select class="form-select form-select-sm" name="tiene_discapacidad" id="in_tiene_discap">
                                    <option value="NO">NO</option>
                                    <option value="SI">SÍ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">¿Cuál discapacidad?</label>
                                <input type="text" class="form-control form-control-sm" name="cual_discapacidad" id="in_cual_discap" placeholder="Especifique o NA">
                            </div>

                            <div class="col-md-3">
                                <label class="small fw-bold">¿Pertenece a algún grupo étnico?</label>
                                <select class="form-select form-select-sm" name="grupo_etnico" id="in_grupo_etnico_edit">
                                    <option value="NO">NO</option>
                                    <option value="SI">SÍ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">¿Cuál grupo étnico?</label>
                                <input type="text" class="form-control form-control-sm" name="grupo_etnico_cual" id="in_grupo_cual" placeholder="Especifique o NA">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold text-guinda"><i class="fas fa-map-marked-alt me-2"></i>3. Domicilio y Contacto</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="small fw-bold">Calle y Número</label>
                                <input type="text" class="form-control form-control-sm" name="calle_numero" id="in_calle">
                            </div>
                            <div class="col-md-5">
                                <label class="small fw-bold">Colonia o Poblado</label>
                                <input type="text" class="form-control form-control-sm" name="pueblo_colonia" id="in_colonia_edit">
                            </div>
                            <div class="col-md-2">
                                <label class="small fw-bold">C.P.</label>
                                <input type="text" class="form-control form-control-sm" name="cp" id="in_cp_edit">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Tel. Particular (Cel)</label>
                                <input type="text" class="form-control form-control-sm" name="tel_particular" id="in_tel_part">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Tel. Casa</label>
                                <input type="text" class="form-control form-control-sm" name="tel_casa" id="in_tel_casa">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Tel. Familiar / Recados</label>
                                <input type="text" class="form-control form-control-sm" name="tel_recados" id="in_tel_fam">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4 border-start border-4 border-success">
                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold text-success"><i class="fas fa-seedling me-2"></i>4. Acreditación y Producción Técnica</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold">Línea de ayuda a la cual corresponde la solicitud</label>
                                <select class="form-select form-select-sm" name="linea_ayuda" id="in_linea_ayuda">
                                    <option value="" selected disabled>Elegir...</option>
                                    <option value="AGRICOLA">Agrícola</option>
                                    <option value="PECUARIA">Pecuaria</option>
                                    <option value="GRANJA_INTEGRAL">Granja integral</option>
                                    <option value="HUERTO_URBANO">Huerto urbano</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">¿Inscrito en SINIIGA?</label>
                                <select class="form-select form-select-sm" name="siniiga_status" id="in_siniiga">
                                    <option value="NO">NO</option>
                                    <option value="SI">SÍ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Total de Predios</label>
                                <input type="number" class="form-control form-control-sm" name="num_total_predios" id="in_total_predios">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Superficie Total (ha)</label>
                                <input type="number" step="0.0001" class="form-control form-control-sm" name="superficie_prod" id="in_sup_edit">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Tipo de documento con el que acredita la propiedad o posesión</label>
                                <input type="text" class="form-control form-control-sm" 
                                    name="tipo_documento_prop" 
                                    id="in_tipo_doc" 
                                    placeholder="Ej. Título de Propiedad, Certificado, etc.">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Poblado/Colonia de la U.P.</label>
                                <input type="text" class="form-control form-control-sm" name="pueblo_colonia_up" id="in_pueblo_up">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Parajes</label>
                                <input type="text" class="form-control form-control-sm" 
                                    name="parajes" 
                                    id="in_parajes" 
                                    placeholder="Especifique paraje o escriba NA">
                                <div class="form-text" style="font-size: 0.65rem;">Indique el nombre de la zona o paraje específico.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Tenencia de la Tierra</label>
                                <select class="form-select form-select-sm" name="tenencia_tierra" id="in_tenencia">
                                    <option value="" selected disabled>Elegir...</option>
                                    <option value="EJIDO">Ejido</option>
                                    <option value="COMUNIDAD">Comunidad</option>
                                    <option value="PEQUENA_PROPIEDAD">Pequeña propiedad</option>
                                    <option value="NA">NA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Especie o Cultivo Principal</label>
                                <input type="text" class="form-control form-control-sm" name="cultivo_principal" id="in_cultivo">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Num. Cabezas / Colmenas</label>
                                <input type="number" class="form-control form-control-sm" name="num_animales" id="in_num_animales">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-guinda">Línea de Ayuda a Procesar</label>
                                <select class="form-select form-select-sm shadow-sm" name="tipo_produccion" id="in_tipo_produccion">
                                    <option value="">-- Seleccione una --</option>
                                    <option value="AGRICOLA">AGRÍCOLA</option>
                                    <option value="PECUARIA">PECUARIA</option>
                                    <option value="GRANJA_INTEGRAL">GRANJA INTEGRAL</option>
                                    <option value="HUERTO_URBANO">HUERTO URBANO</option>
                                </select>
                                <small class="text-muted" style="font-size: 0.65rem;">Origen detectado: <span id="origen_produccion_text"></span></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4 bg-guinda-light">
                    <div class="card-body text-end">
                        <div class="row align-items-center">
                            <div class="col-md-8 text-start small">
                                <i class="fas fa-info-circle me-1"></i> Verifique que los apellidos y RFC coincidan con la identificación oficial del productor.
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-danger d-block text-start text-uppercase">Fase del proceso</label>
                                <select class="form-select fw-bold border-danger" name="fase_proceso" id="in_fase">
                                    <option value="EMPADRONADO">1. EMPADRONADO</option>
                                    <option value="SOLICITUD_INGRESADA">2. SOLICITUD INGRESADA</option>
                                    <option value="VALIDACION_DOCS">3. VALIDACIÓN DE DOCS</option>
                                    <option value="EN_REVISION">4. EN REVISIÓN TÉCNICA</option>
                                    <option value="APROBADO">5. APROBADO</option>
                                    <option value="RECHAZADO">6. RECHAZADO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-docs">
                <div class="alert alert-info border-0 shadow-sm small mb-3">
                    <i class="fas fa-file-invoice me-2"></i> <b>Expediente Digital:</b> Los documentos con fondo azul ya existen en el servidor. Puede verlos o reemplazarlos.
                </div>

                <div class="card shadow-sm border-0">
                    <div class="list-group list-group-flush" id="checkListDocs">
                        
                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_solicitud" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-file-signature text-guinda me-2"></i> <b>Formato de solicitud (Firmado)</b><br>
                                        <small class="text-muted">Ante la J.U.D. de Desarrollo Rural</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_solicitud" id="delete_solicitud" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_solicitud">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('solicitud')"></i>
                                        </div>
                                    </div>
                                    <label for="file_solicitud" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_solicitud" id="file_solicitud" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_identidad" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-id-card text-guinda me-2"></i> <b>Acreditación de identidad vigente</b><br>
                                        <small class="text-muted">INE, Pasaporte, Cédula o Cartilla Militar</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_identidad" id="delete_identidad" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_identidad">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('identidad')"></i>
                                        </div>
                                    </div>
                                    <label for="file_identidad" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_identidad" id="file_identidad" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_domicilio" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-home text-guinda me-2"></i> <b>Comprobante de Domicilio</b><br>
                                        <small class="text-muted">No mayor a 3 meses de antigüedad</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_domicilio" id="delete_domicilio" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_domicilio">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('domicilio')"></i>
                                        </div>
                                    </div>
                                    <label for="file_domicilio" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_domicilio" id="file_domicilio" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_curp_doc" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-fingerprint text-guinda me-2"></i> <b>Copia de la CURP</b><br>
                                        <small class="text-muted">Actualizada</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_curp_doc" id="delete_curp_doc" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_curp_doc">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('curp_doc')"></i>
                                        </div>
                                    </div>
                                    <label for="file_curp_doc" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_curp_doc" id="file_curp_doc" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_rfc_doc" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-university text-guinda me-2"></i> <b>R.F.C. (Si aplica)</b><br>
                                        <small class="text-muted">Registro Federal de Contribuyentes</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_rfc_doc" id="delete_rfc_doc" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_rfc_doc">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('rfc_doc')"></i>
                                        </div>
                                    </div>
                                    <label for="file_rfc_doc" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_rfc_doc" id="file_rfc_doc" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_manifiesto" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-gavel text-guinda me-2"></i> <b>Manifiesto Bajo Protesta</b><br>
                                        <small class="text-muted">No desempeñar cargo en la Alcaldía</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_manifiesto" id="delete_manifiesto" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_manifiesto">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('manifiesto')"></i>
                                        </div>
                                    </div>
                                    <label for="file_manifiesto" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_manifiesto" id="file_manifiesto" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_propiedad" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-map-marked-alt text-guinda me-2"></i> <b>Acreditación de propiedad</b><br>
                                        <small class="text-muted">Documento técnico-legal del predio</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_propiedad" id="delete_propiedad" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_propiedad">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('propiedad')"></i>
                                        </div>
                                    </div>
                                    <label for="file_propiedad" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_propiedad" id="file_propiedad" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_finiquito" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-file-contract text-guinda me-2"></i> <b>Carta Finiquito</b><br>
                                        <small class="text-muted">Ex-beneficiarios de programas anteriores</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_finiquito" id="delete_finiquito" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_finiquito">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('finiquito')"></i>
                                        </div>
                                    </div>
                                    <label for="file_finiquito" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_finiquito" id="file_finiquito" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item py-3 doc-row">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" name="check_siniiga_doc" value="1" class="form-check-input h5 mb-0 me-3 doc-check">
                                    <span>
                                        <i class="fas fa-cow text-guinda me-2"></i> <b>Registro SINIIGA</b><br>
                                        <small class="text-muted">Únicamente para Unidades Pecuarias</small>
                                    </span>
                                </div>
                                <div class="file-upload-wrapper text-end">
                                    <input type="hidden" name="delete_siniiga_doc" id="delete_siniiga_doc" value="0">
                                    <div class="file-preview-container d-none mb-1" id="preview_siniiga_doc">
                                        <div class="file-preview-badge">
                                            <i class="fas fa-paperclip text-primary me-1"></i>
                                            <span class="file-name-text small fw-bold"></span>
                                            <i class="fas fa-times-circle remove-file ms-2" onclick="eliminarArchivo('siniiga_doc')"></i>
                                        </div>
                                    </div>
                                    <label for="file_siniiga_doc" class="btn-upload"><i class="fas fa-camera me-1"></i> SUBIR/TOMAR</label>
                                    <input type="file" name="file_siniiga_doc" id="file_siniiga_doc" class="d-none file-input" accept="image/*,application/pdf,.zip,.rar" capture="environment">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
                <div class="tab-pane fade" id="tab-verificacion">
                    <div class="alert alert-success border-0 shadow-sm small mb-3">
                        <i class="fas fa-check-double me-2"></i> <b>Validación de Campo:</b> Registre las coordenadas reales y capture las fotografías de evidencia en sitio.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0 fw-bold text-guinda"><i class="fas fa-map-marker-alt me-2"></i>Coordenadas de Verificación</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted">Nueva Latitud</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-arrows-alt-v text-secondary"></i></span>
                                            <input type="text" class="form-control border-0 bg-light fw-bold" name="latitud_verif" id="in_lat_verif" placeholder="19.XXXXXX">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted">Nueva Longitud</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-arrows-alt-h text-secondary"></i></span>
                                            <input type="text" class="form-control border-0 bg-light fw-bold" name="longitud_verif" id="in_lon_verif" placeholder="-99.XXXXXX">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-guinda"><i class="fas fa-camera me-2"></i>Evidencias Fotográficas</h6>
                                    <label for="input_evidencias" class="btn btn-guinda btn-sm px-3 rounded-pill">
                                        <i class="fas fa-plus me-1"></i> AÑADIR FOTOS
                                    </label>
                                    <input type="file" id="input_evidencias" name="fotos_evidencia[]" class="d-none" accept="image/*" multiple capture="environment">
                                </div>
                                <div class="card-body">
                                    <div id="galeria_evidencias" class="row g-2 overflow-auto" style="max-height: 350px;">
                                        <div class="col-12 text-center py-5 text-muted empty-msg">
                                            <i class="fas fa-images fa-3x mb-2 opacity-25"></i>
                                            <p class="small mb-0">No hay fotos capturadas.<br>Use el botón "Añadir Fotos" para empezar.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    </div>
                </form>
            </div>
                <div class="modal-footer">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-secondary px-4 fw-bold" style="border-radius:10px;" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>CANCELAR
                        </button>

                        <div class="d-flex gap-2">
                            <button type="button" id="btnDescargarPDF" class="btn btn-primary-custom shadow-sm d-none">
                                <i class="fas fa-file-pdf me-2"></i>GENERAR SOLICITUD 2026
                            </button>

                            <button type="button" onclick="confirmarGuardado()" class="btn btn-guinda shadow">
                                <i class="fas fa-save me-2"></i>GUARDAR EXPEDIENTE
                            </button>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    let rawData = [];
    let filteredData = [];
    const pageSize = 10;
    let currentPage = 1;

    // ==========================================
    // 0. CONFIGURACIÓN DE NOTIFICACIONES
    // ==========================================
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // ==========================================
    // 1. CARGA INICIAL DE DATOS
    // ==========================================
    function cargarDatos() {
        fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
            .then(res => res.json())
            .then(data => {
                rawData = data.maestro || [];
                filteredData = [...rawData];
                actualizarKPIs(rawData);
                renderTable(1); // Empezamos siempre en la 1
            })
            .catch(err => console.error("Error al obtener datos:", err));
    }

    cargarDatos();

function actualizarKPIs(data) {
    $("#kpi-total").text(data.length);
    
    $("#kpi-solicitudes").text(data.filter(i => i.fase_proceso === 'SOLICITUD_INGRESADA').length);
    
    $("#kpi-pendientes").text(data.filter(i => i.fase_proceso === 'VALIDACION_DOCS').length);
    
    $("#kpi-revision").text(data.filter(i => i.fase_proceso === 'EN_REVISION').length);
    
    $("#kpi-aprobados").text(data.filter(i => i.fase_proceso === 'APROBADO').length);
}

    // ==========================================
    // 2. UTILIDADES DE PROCESAMIENTO
    // ==========================================
    function segmentarNombreCompleto(nombreCompleto) {
        if (!nombreCompleto) return { nombres: '', paterno: '', materno: '' };
        let palabras = nombreCompleto.trim().toUpperCase().split(/\s+/);
        let result = { nombres: '', paterno: '', materno: '' };
        if (palabras.length >= 3) {
            result.materno = palabras.pop();
            result.paterno = palabras.pop();
            result.nombres = palabras.join(' '); 
        } else if (palabras.length === 2) {
            result.nombres = palabras[0];
            result.paterno = palabras[1];
        } else {
            result.nombres = palabras[0];
        }
        return result;
    }

    function getDatoFinal(reg, campoBuscado, json) {
        const nombreFisico = `${reg.nombre || ''} ${reg.apellido_paterno || ''} ${reg.apellido_materno || ''}`.trim();
        const mapaFisico = {
            "folio": reg.folio, "curp": reg.curp, "rfc": reg.rfc, "nombre_productor": nombreFisico,
            "pueblo_colonia": reg.colonia_nombre, "superficie_prod": reg.superficie_total,
            "fase_proceso": reg.fase_proceso, "tipo_produccion": reg.linea_ayuda,
            "grado_estudios": reg.escolaridad, "ocupacion": reg.ocupacion, "estado_civil": reg.estado_civil,
            "calle_numero": reg.calle, "cp": reg.codigo_postal, "tipo_id": reg.tipo_id,
            "numero_id": reg.numero_id, "tiene_discapacidad": reg.tiene_discapacidad,
            "cual_discapacidad": reg.cual_discapacidad, "grupo_etnico": reg.grupo_etnico,
            "grupo_etnico_cual": reg.grupo_etnico_cual, "tel_particular": reg.tel_particular,
            "tel_casa": reg.tel_casa, "tel_recados": reg.tel_familiar, "linea_ayuda": reg.linea_ayuda,
            "siniiga_status": reg.registro_siniiga, "num_total_predios": reg.num_total_predios,
            "tipo_documento_prop": reg.tipo_documento_propiedad, "pueblo_colonia_up": reg.pueblo_colonia_up,
            "parajes": reg.parajes, "tenencia_tierra": reg.tenencia_tierra,
            "cultivo_principal": reg.especie_cultivo_principal, "num_animales": reg.numero_cabezas_colmenas
        };

        if (mapaFisico[campoBuscado] !== undefined && mapaFisico[campoBuscado] !== null && mapaFisico[campoBuscado] !== "") {
            return mapaFisico[campoBuscado];
        }
        if (!json) return '';
        for (let seccion in json) {
            let contenido = json[seccion];
            if (seccion === "6" && contenido[campoBuscado]) return contenido[campoBuscado];
            if (Array.isArray(contenido)) {
                let resultados = [];
                contenido.forEach(item => {
                    let clean = item.name ? item.name.replace('[]', '') : '';
                    if (clean === campoBuscado && item.value) resultados.push(item.value);
                });
                if (resultados.length > 0) return resultados.join(', ');
            } else if (seccion === campoBuscado) {
                return contenido;
            }
        }
        return '';
    }

    // ==========================================
    // 3. RENDERIZADO DE TABLA
    // ==========================================
    function renderTable(page) {
        currentPage = page;
        const start = (page - 1) * pageSize;
        const items = filteredData.slice(start, start + pageSize);
        const tbody = $("#tablaCaptura tbody").empty();

        items.forEach(e => {
            const faseLimpia = (e.fase_proceso || 'EMPADRONADO').replace(/_/g, ' ');
            const pdfLiberado = e.fase_proceso && e.fase_proceso !== 'EMPADRONADO';

            tbody.append(`
                <tr>
                    <td class="ps-3 fw-bold text-guinda">${e.folio || 'S/F'}</td>
                    <td class="small">
                        <div class="fw-bold text-dark">${e.nombre || ''} ${e.apellido_paterno || ''} ${e.apellido_materno || ''}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">${e.curp || 'SIN CURP'}</div>
                    </td>
                    <td><span class="badge badge-fase fase-${e.fase_proceso || 'EMPADRONADO'}">${faseLimpia}</span></td>
                    <td class="text-center fw-bold text-secondary">${parseFloat(e.superficie_total || 0).toFixed(2)} ha</td>
                    <td class="text-center">
                        <button onclick="abrirEdicion(${e.id})" class="btn btn-sm btn-guinda rounded-circle shadow-sm me-1" title="Editar"><i class="fas fa-user-edit"></i></button>
                        ${pdfLiberado ? 
                            `<a href="<?php echo URLROOT; ?>/Expediente/imprimirSolicitud/${e.id}" target="_blank" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" title="Descargar PDF"><i class="fas fa-file-pdf"></i></a>` : 
                            `<button class="btn btn-sm btn-light rounded-circle text-muted shadow-none" style="cursor:not-allowed;" title="Falta captura" disabled><i class="fas fa-file-pdf"></i></button>`
                        }
                    </td>
                </tr>
            `);
        });
        $("#tableInfo").html(`Mostrando <b>${items.length}</b> de <b>${filteredData.length}</b> registros`);
        renderPaginationUI();
    }
function obtenerDatoFinal(reg, campoBuscado, json) {
    // 1. Mapeo de columnas físicas reales de la tabla 'encuestas'
    const mapaFisico = {
        "tecnico_nombre": reg.encuestador,
        "folio": reg.folio,
        "curp": reg.curp,
        "rfc": reg.rfc,
        "nombre": reg.nombre,
        "apellido_paterno": reg.apellido_paterno,
        "apellido_materno": reg.apellido_materno,
        "nombre_productor": `${reg.nombre || ''} ${reg.apellido_paterno || ''} ${reg.apellido_materno || ''}`.trim(),
        "pueblo_colonia": reg.colonia_nombre,
        "superficie_prod": reg.superficie_total,
        "latitud_verif": reg.latitud_verif,
        "longitud_verif": reg.longitud_verif,
        "fase_proceso": reg.fase_proceso
    };

    // Si el campo existe en la tabla física y tiene contenido, lo devolvemos
    if (mapaFisico[campoBuscado] !== undefined && mapaFisico[campoBuscado] !== null && mapaFisico[campoBuscado] !== "") {
        return mapaFisico[campoBuscado];
    }

    // 2. Si no es físico, buscamos en el JSON
    if (!json) return limpiarResultado('', campoBuscado);
    
    // Caso especial Sección 6 (Geolocalización/Dirección en el JSON original)
    if (json["6"] && json["6"][campoBuscado]) {
        return limpiarResultado(json["6"][campoBuscado], campoBuscado);
    }

    // Barrido por secciones del JSON (1, 2, 3... hasta 50)
    for (let sec in json) {
        let contenido = json[sec];
        if (Array.isArray(contenido)) {
            // Buscamos el objeto que tenga el atributo name coincidente
            const found = contenido.find(i => i.name === campoBuscado || i.name === campoBuscado + '[]');
            if (found) return limpiarResultado(found.value, campoBuscado);
        } else if (typeof contenido === 'object' && contenido !== null) {
            if (contenido[campoBuscado]) return limpiarResultado(contenido[campoBuscado], campoBuscado);
        } else if (typeof contenido === 'string' && sec === campoBuscado) {
            return limpiarResultado(contenido, campoBuscado);
        }
    }

    return limpiarResultado('', campoBuscado);
}

/**
 * Función auxiliar para evitar que los campos numéricos lleven basura
 */
function limpiarResultado(valor, campo) {
    const camposNumericos = [
        'latitud_verif', 'longitud_verif', 'latitud', 'longitud', 
        'superficie_prod', 'volumen_prod', 'superficie_total', 
        'num_total_predios', 'num_animales', 'cabezas'
    ];

    if (camposNumericos.includes(campo)) {
        // Si es numérico y no hay valor o es el guion, devolver vacío para la BD
        return (valor === '---' || !valor) ? "" : valor;
    }

    // Para campos de texto, si no hay nada, ponemos los guiones visuales
    return (valor === undefined || valor === null || valor === "") ? "---" : valor;
}
    // ==========================================
    // 4. LÓGICA DEL MODAL
    // ==========================================
    window.abrirEdicion = function(id) {
    // 1. Localización de datos y parsing
    const reg = rawData.find(i => i.id == id);
    if (!reg) return;
    const json = reg.respuestas_json ? JSON.parse(reg.respuestas_json) : {};

    // 2. Limpieza profunda del formulario antes de cargar
    $("#formCaptura")[0].reset();
    $('input[name^="delete_"]').val('0'); 
    $(".file-preview-container").addClass('d-none');
    $(".doc-row").css('background-color', '');
    $(".btn-upload").html('<i class="fas fa-camera me-1"></i> SUBIR/TOMAR');
    
    // --- NUEVO: Limpieza específica de la Pestaña de Verificación ---
    $("#in_lat_verif").val(''); 
    $("#in_lon_verif").val('');
    $("#input_evidencias").val('');
    $("#galeria_evidencias").html(`
        <div class="col-12 text-center py-5 text-muted empty-msg">
            <i class="fas fa-images fa-3x mb-2 opacity-25"></i>
            <p class="small mb-0">No hay fotos capturadas.<br>Use el botón "Añadir Fotos" para empezar.</p>
        </div>
    `);

    // 3. Carga de metadatos básicos
    $("#reg_id").val(reg.id);
    $("#spanFolio").text(reg.folio || 'S/F');
    
    // 4. Lógica del botón PDF (Solo si no es fase EMPADRONADO)
    const pdfLiberado = reg.fase_proceso && reg.fase_proceso !== 'EMPADRONADO';
    if (pdfLiberado) {
        $("#btnDescargarPDF").removeClass("d-none").attr("onclick", `window.open('<?php echo URLROOT; ?>/Expediente/imprimirSolicitud/${id}', '_blank')`);
    } else {
        $("#btnDescargarPDF").addClass("d-none");
    }

    // 5. Carga de Identidad (Segmentación de nombre)
    const fullNombre = getDatoFinal(reg, "nombre_productor", json);
    const seg = segmentarNombreCompleto(fullNombre);
    $("#in_nombre_productor").val(reg.nombre || seg.nombres); 
    $("#in_paterno").val(reg.apellido_paterno || seg.paterno); 
    $("#in_materno").val(reg.apellido_materno || seg.materno);
    $("#in_curp_edit").val(reg.curp);
    $("#in_rfc").val(reg.rfc);

    // 6. Automatización de llenado de inputs desde el JSON
    $("#formCaptura input, #formCaptura select, #formCaptura textarea").each(function() {
        const el = $(this);
        const name = el.attr('name');
        // Saltamos campos que ya manejamos manualmente o archivos/checks
        if (!name || ['id', 'nombre_productor', 'paterno', 'materno', 'rfc', 'curp', 'tipo_produccion', 'latitud_verif', 'longitud_verif'].includes(name) || el.attr('type') === 'file' || name.startsWith('check_')) return;
        
        const valor = getDatoFinal(reg, name, json);
        if (valor !== undefined && valor !== "") { 
            el.val(valor); 
        }
    });

    // 7. Lógica especial para Línea de Ayuda (Producción)
    const valorProduccionOriginal = getDatoFinal(reg, "tipo_produccion", json);
    $("#origen_produccion_text").text(valorProduccionOriginal || 'No definido'); 
    const selectProd = $("#in_tipo_produccion");
    selectProd.removeClass("is-invalid border-danger");
    
    if (valorProduccionOriginal) {
        if (valorProduccionOriginal.includes(',')) {
            selectProd.val(""); 
            selectProd.addClass("is-invalid border-danger");
            Toast.fire({ icon: 'warning', title: 'Múltiples actividades detectadas. Elija la Línea de Ayuda principal.' });
        } else {
            const limpio = valorProduccionOriginal.trim().toUpperCase();
            selectProd.val(limpio);
            if (selectProd.val() === null || selectProd.val() === "") { 
                selectProd.addClass("is-invalid border-danger"); 
            }
        }
    }

    // 8. Carga de Checks de Cotejo (Desde columnas físicas de la BD)
    const checksCotejo = ['solicitud', 'identidad', 'domicilio', 'curp_doc', 'rfc_doc', 'manifiesto', 'propiedad', 'finiquito', 'siniiga_doc'];
    checksCotejo.forEach(c => {
        const columnaDB = 'check_' + c;
        const valorDB = reg[columnaDB];
        $(`input[name="${columnaDB}"]`).prop('checked', parseInt(valorDB) === 1);
    });

    // 9. Carga de Archivos existentes en el servidor (AJAX)
    fetch(`<?php echo URLROOT; ?>/Captura/verificarArchivos/${id}`)
        .then(res => res.json())
        .then(archivos => {
            archivos.forEach(file => {
                const partes = file.tipo.split('_');
                let tipoDoc = partes[partes.length - 1].toLowerCase();
                if (tipoDoc === 'doc') { 
                    tipoDoc = partes[partes.length - 2].toLowerCase() + '_doc'; 
                }
                const container = $(`#preview_${tipoDoc}`);
                if (container.length) {
                    container.removeClass('d-none');
                    container.find('.file-name-text').html(`<a href="${file.url}" target="_blank" class="text-primary fw-bold text-decoration-none"><i class="fas fa-eye me-1"></i> VER ARCHIVO ACTUAL</a>`);
                    $(`label[for="file_${tipoDoc}"]`).html('<i class="fas fa-sync me-1"></i> REEMPLAZAR');
                    container.closest('.doc-row').css('background-color', '#f0faff');
                }
            });
        })
        .catch(err => console.error("Error en carga de archivos:", err));

    // --- NUEVO: Carga de datos de la Pestaña 4 (Verificación) ---
    // Si ya existen coordenadas guardadas en la BD o JSON, las cargamos
// --- NUEVO: Carga de datos de la Pestaña 4 (Verificación) ---
let latPrev = getDatoFinal(reg, "latitud_verif", json);
let lonPrev = getDatoFinal(reg, "longitud_verif", json);

// Si el valor es el default '---', lo dejamos vacío para que el input esté limpio
if (latPrev === '---' || latPrev === null) latPrev = '';
if (lonPrev === '---' || lonPrev === null) lonPrev = '';

    // 10. Finalización de UI
    renderTabResumen(reg, json); // Pestaña 1
    window.controlarDependencias(); // Lógica de visibilidad de campos
    
    // Asegurar que inicie en la Pestaña 1
    const firstTabEl = document.querySelector('#tabExpediente li:first-child a');
    bootstrap.Tab.getOrCreateInstance(firstTabEl).show();
    
    // Mostrar el modal
    $("#modalEdicion").modal('show');
};
    // ==========================================
    // 5. BÚSQUEDA Y PAGINACIÓN (CORREGIDA)
    // ==========================================
    function renderPaginationUI() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        const container = $("#paginationControls").empty();
        if (totalPages <= 1) return;

        const delta = 2;
        const range = [];
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
                range.push(i);
            }
        }

        let l;
        range.forEach(i => {
            if (l) {
                if (i - l === 2) {
                    container.append(createPageItem(l + 1));
                } else if (i - l !== 1) {
                    container.append('<li class="page-item disabled"><span class="page-link border-0">...</span></li>');
                }
            }
            container.append(createPageItem(i));
            l = i;
        });

        // Evento de clic corregido para que no se pierda
        container.find('.page-link-btn').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            renderTable(page);
        });
    }

    function createPageItem(i) {
        const activeClass = (i === currentPage) ? 'active' : '';
        return `<li class="page-item ${activeClass}"><a class="page-link shadow-sm page-link-btn" href="#" data-page="${i}">${i}</a></li>`;
    }

    $("#tablaSearch").on("keyup", function() {
        const val = $(this).val().toLowerCase().trim();
        filteredData = (val === "") ? [...rawData] : rawData.filter(e => {
            const nom = `${e.nombre || ''} ${e.apellido_paterno || ''} ${e.apellido_materno || ''}`.toLowerCase();
            const fol = (e.folio || "").toLowerCase();
            const cur = (e.curp || "").toLowerCase();
            return nom.includes(val) || fol.includes(val) || cur.includes(val);
        });
        renderTable(1);
    });

    // ==========================================
    // 6. RESUMEN Y GESTIÓN DE ARCHIVOS UI
    // ==========================================
    function renderTabResumen(reg, json) {
        const $resumen = $("#resumenCaptura").empty();
        const config = { "1. Registro": ["tecnico_nombre", "folio", "fase_proceso"], "2. Identidad": ["nombre_productor", "curp", "rfc", "sexo", "grado_estudios"], "3. Ubicación": ["calle_numero", "pueblo_colonia", "cp", "tel_particular"], "4. Perfil": ["ocupacion", "grupo_etnico"], "5. Producción": ["tipo_produccion", "superficie_prod", "cultivo_principal"] };
        for (const [titulo, campos] of Object.entries(config)) {
            let filas = ""; let tieneDatos = false;
            campos.forEach(c => {
                let val = getDatoFinal(reg, c, json);
                if (val && val !== "") { tieneDatos = true; filas += `<tr><td class="ps-3 text-muted py-2" width="45%">${c.replace(/_/g, ' ').toUpperCase()}</td><td class="fw-bold py-2 text-dark small">${val.toString().toUpperCase()}</td></tr>`; }
            });
            if (tieneDatos) { $resumen.append(`<div class="col-md-6 mb-3"><div class="card h-100 border-0 shadow-sm"><div class="card-header py-2 bg-white border-bottom text-guinda fw-bold small">${titulo}</div><div class="card-body p-0"><table class="table table-sm mb-0"><tbody>${filas}</tbody></table></div></div></div>`); }
        }
    }

    $(document).on('change', '.file-input', function() {
        const input = this;
        const suffix = input.id.replace('file_', '');
        const container = $(`#preview_${suffix}`);
        if (input.files && input.files[0]) {
            $(`#delete_${suffix}`).val('0'); 
            container.find('.file-name-text').text(input.files[0].name);
            container.removeClass('d-none');
            $(input).closest('.doc-row').css('background-color', '#f0fff4');
            $(`input[name="check_${suffix}"]`).prop('checked', true);
        }
    });

    window.eliminarArchivo = function(suffix) {
        Swal.fire({ title: '¿Quitar archivo?', text: "Se marcará para eliminación física al guardar.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, quitar' }).then((result) => {
            if (result.isConfirmed) {
                $(`#delete_${suffix}`).val('1'); 
                $(`#file_${suffix}`).val(''); 
                $(`#preview_${suffix}`).addClass('d-none');
                $(`input[name="check_${suffix}"]`).prop('checked', false); 
                $(`#preview_${suffix}`).closest('.doc-row').css('background-color', '#fff5f5');
                $(`label[for="file_${suffix}"]`).html('<i class="fas fa-camera me-1"></i> SUBIR/TOMAR');
            }
        });
    };

    window.controlarDependencias = function() {
        const disc = $("#in_tiene_discap").val() === "SI";
        $("#in_cual_discap").prop("disabled", !disc).toggleClass("bg-light", !disc);
        const etnia = $("#in_grupo_etnico_edit").val() === "SI";
        $("#in_grupo_cual").prop("disabled", !etnia).toggleClass("bg-light", !etnia);
    };
});

// ==========================================
// 7. ACCIONES DE GUARDADO FINAL
// ==========================================
function confirmarGuardado() {
    // 1. Validación de Campo Crítico: Línea de Ayuda (Producción)
    const lineaAyuda = $("#in_tipo_produccion").val();
    if (!lineaAyuda || lineaAyuda === "") {
        $("#in_tipo_produccion").addClass("is-invalid border-danger").focus();
        Swal.fire({
            icon: 'error',
            title: 'Campo Obligatorio',
            text: 'Por favor, seleccione una Línea de Ayuda válida en la Pestaña 2 antes de guardar.',
            confirmButtonColor: '#773357'
        });
        return;
    }

    // 2. Estandarización: Convertir textos y áreas de texto a Mayúsculas
    $("#formCaptura input[type='text'], #formCaptura textarea").each(function() { 
        $(this).val($(this).val().toUpperCase()); 
    });

    // 3. Preparación de Datos: Habilitar campos deshabilitados para que FormData los incluya
    const inputsDisabled = $("#formCaptura").find(':disabled');
    inputsDisabled.prop('disabled', false);
    
    const formElement = document.getElementById('formCaptura');
    const formData = new FormData(formElement);
    
    // 4. Manejo Manual de Checkboxes (Cotejo de Documentos)
    // Aseguramos que envíen 1 o 0 para que la base de datos los procese correctamente
    const listadoChecks = [
        'check_solicitud', 'check_identidad', 'check_domicilio', 
        'check_curp_doc', 'check_rfc_doc', 'check_manifiesto', 
        'check_propiedad', 'check_finiquito', 'check_siniiga_doc'
    ];
    
    listadoChecks.forEach(c => {
        const el = document.getElementsByName(c)[0];
        // En lugar de enviar "on" o nada, enviamos 1 o 0
        formData.set(c, (el && el.checked) ? 1 : 0);
    });

    // 5. Verificación (GPS y Fotos)
    // Nota: 'latitud_verif', 'longitud_verif' y 'fotos_evidencia[]' 
    // se agregan automáticamente al FormData por estar dentro del formulario.

    // Restauramos el estado original de los inputs deshabilitados en la UI
    inputsDisabled.prop('disabled', true);
    
    // 6. Confirmación y Envío vía Fetch
    Swal.fire({ 
        title: '¿Guardar cambios?', 
        text: "Se actualizará el expediente oficial, los documentos cotejados y las evidencias de verificación.", 
        icon: 'warning', 
        showCancelButton: true, 
        confirmButtonColor: '#773357', 
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true 
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar estado de carga (indispensable para subida de archivos)
            Swal.fire({ 
                title: 'Sincronizando Expediente...', 
                text: 'Subiendo información y archivos de evidencia, por favor espere.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() } 
            });

            fetch('<?php echo URLROOT; ?>/Captura/actualizar', { 
                method: 'POST', 
                body: formData 
            })
            .then(res => {
                if (!res.ok) throw new Error('Error en la respuesta del servidor');
                return res.json();
            })
            .then(data => {
                if(data.status === 'success') { 
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.msg,
                        confirmButtonColor: '#773357'
                    }).then(() => location.reload()); 
                } else { 
                    Swal.fire({
                        icon: 'error',
                        title: 'Falla al guardar',
                        text: data.msg || 'El servidor rechazó la solicitud.',
                        confirmButtonColor: '#773357'
                    });
                }
            })
            .catch(err => { 
                console.error(err); 
                Swal.fire({
                    icon: 'error',
                    title: 'Falla de Comunicación',
                    text: 'No se pudo conectar con el servidor. Verifique su conexión de datos o internet.',
                    confirmButtonColor: '#773357'
                }); 
            });
        }
    });
}

function confirmarSalida() {
    Swal.fire({ title: '¿Cerrar sesión?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'SÍ, SALIR', reverseButtons: true }).then((result) => {
        if (result.isConfirmed) window.location.href = '<?php echo URLROOT; ?>/Auth/logout';
    });
}
// Variable global para almacenar los archivos seleccionados (opcional si usas FormData directo)
let archivosEvidencia = [];

$(document).on('change', '#input_evidencias', function(e) {
    const files = e.target.files;
    const galeria = $("#galeria_evidencias");
    
    // Ocultar mensaje de "No hay fotos"
    galeria.find('.empty-msg').hide();

    // Recorrer los archivos seleccionados
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Validar que sea imagen
        if (!file.type.match('image.*')) continue;

        const reader = new FileReader();
        
        reader.onload = function(event) {
            const html = `
                <div class="col-4 col-md-3 position-relative mb-2 foto-item">
                    <div class="foto-evidencia-wrapper" style="position: relative; aspect-ratio: 1/1; overflow: hidden; border-radius: 10px; border: 2px solid #fff; shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <button type="button" class="btn btn-danger btn-sm p-0 d-flex align-items-center justify-content-center" 
                                onclick="eliminarMiniatura(this)"
                                style="position: absolute; top: 5px; right: 5px; width: 22px; height: 22px; border-radius: 50%; z-index: 10; border: 1px solid white;">
                            <i class="fas fa-times" style="font-size: 10px;"></i>
                        </button>
                        <img src="${event.target.result}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                    </div>
                </div>`;
            galeria.append(html);
        };
        
        reader.readAsDataURL(file);
    }
});

// Función para eliminar la miniatura visualmente
function eliminarMiniatura(btn) {
    $(btn).closest('.foto-item').remove();
    
    // Si ya no quedan fotos, mostrar el mensaje vacío de nuevo
    if ($("#galeria_evidencias").find('.foto-item').length === 0) {
        $("#galeria_evidencias").find('.empty-msg').show();
        // Limpiar el input file para que permita subir el mismo archivo si se desea
        $("#input_evidencias").val('');
    }
}
window.confirmarSalida = confirmarSalida;
</script>