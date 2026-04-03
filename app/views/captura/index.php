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
        z-index: 1055; /* Asegura que esté sobre el contenido */
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
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-secondary"><h6 class="text-muted small mb-1">TOTAL REGISTROS</h6><h3 class="fw-bold mb-0" id="kpi-total">0</h3></div></div>
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-info"><h6 class="text-muted small mb-1">VALIDACIÓN DOCS</h6><h3 class="fw-bold mb-0" id="kpi-pendientes">0</h3></div></div>
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-warning"><h6 class="text-muted small mb-1">EN REVISIÓN</h6><h3 class="fw-bold mb-0" id="kpi-revision">0</h3></div></div>
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-success"><h6 class="text-muted small mb-1">TOTAL APROBADOS</h6><h3 class="fw-bold mb-0" id="kpi-aprobados">0</h3></div></div>
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
                                <label class="small fw-bold text-muted">Tipo de Producción (Origen)</label>
                                <input type="text" class="form-control form-control-sm bg-light" name="tipo_produccion" readonly>
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
                                    <option value="VALIDACION_DOCS">2. VALIDACIÓN DE DOCS</option>
                                    <option value="EN_REVISION">3. EN REVISIÓN TÉCNICA</option>
                                    <option value="APROBADO">4. APROBADO</option>
                                    <option value="RECHAZADO">5. RECHAZADO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                       <div class="tab-pane fade" id="tab-docs">
                            <div class="alert alert-info border-0 shadow-sm small mb-3">
                                <i class="fas fa-file-invoice me-2"></i> <b>Cotejo de Documentos:</b> Marque los documentos entregados que cumplen con los requisitos.
                            </div>

                            <div class="card shadow-sm border-0">
                                <div class="list-group list-group-flush" id="checkListDocs">
                                    
                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-file-signature text-guinda me-3"></i> <b>Formato de solicitud (Firmado)</b><br>
                                        <small class="text-muted ms-5">Ante la J.U.D. de Desarrollo Rural</small></span>
                                        <input type="checkbox" name="check_solicitud" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-id-card text-guinda me-3"></i> <b>Acreditación de identidad vigente</b><br>
                                        <small class="text-muted ms-5">INE, Pasaporte, Cédula o Cartilla Militar</small></span>
                                        <input type="checkbox" name="check_identidad" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-home text-guinda me-3"></i> <b>Comprobante de Domicilio</b><br>
                                        <small class="text-muted ms-5">No mayor a 3 meses de antigüedad</small></span>
                                        <input type="checkbox" name="check_domicilio" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-fingerprint text-guinda me-3"></i> <b>Copia de la CURP</b><br>
                                        <small class="text-muted ms-5">Clave Única de Registro de Población actualizada</small></span>
                                        <input type="checkbox" name="check_curp_doc" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-university text-guinda me-3"></i> <b>R.F.C. (Si aplica)</b><br>
                                        <small class="text-muted ms-5">Registro Federal de Contribuyentes</small></span>
                                        <input type="checkbox" name="check_rfc_doc" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-gavel text-guinda me-3"></i> <b>Manifiesto Bajo Protesta de decir verdad</b><br>
                                        <small class="text-muted ms-5">No desempeñar cargo en la Alcaldía Tlalpan</small></span>
                                        <input type="checkbox" name="check_manifiesto" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-map-marked-alt text-guinda me-3"></i> <b>Acreditación de propiedad o posesión legal</b><br>
                                        <small class="text-muted ms-5">Documento técnico-legal del predio</small></span>
                                        <input type="checkbox" name="check_propiedad" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-file-contract text-guinda me-3"></i> <b>Carta Finiquito</b><br>
                                        <small class="text-muted ms-5">Para ex-beneficiarios de programas anteriores</small></span>
                                        <input type="checkbox" name="check_finiquito" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3 doc-row">
                                        <span><i class="fas fa-cow text-guinda me-3"></i> <b>Registro SINIIGA (Si aplica)</b><br>
                                        <small class="text-muted ms-5">Únicamente para Unidades Pecuarias</small></span>
                                        <input type="checkbox" name="check_siniiga_doc" value="1" class="form-check-input h5 mb-0 doc-check">
                                    </label>

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
    // 1. CARGA INICIAL
    // ==========================================
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            rawData = data.maestro || [];
            filteredData = [...rawData];
            actualizarKPIs(rawData);
            renderTable(1);
        })
        .catch(err => console.error("Error al obtener datos:", err));

    function actualizarKPIs(data) {
        $("#kpi-total").text(data.length);
        $("#kpi-pendientes").text(data.filter(i => i.fase_proceso === 'VALIDACION_DOCS').length);
        $("#kpi-revision").text(data.filter(i => i.fase_proceso === 'EN_REVISION').length);
        $("#kpi-aprobados").text(data.filter(i => i.fase_proceso === 'APROBADO').length);
    }

    // ==========================================
    // 2. UTILIDADES DE PROCESAMIENTO (RESTAURADO)
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
        // CORRECCIÓN QUIRÚRGICA: Mapeo exacto con nombres de columnas de tu MariaDB
        const nombreFisico = `${reg.nombre || ''} ${reg.apellido_paterno || ''} ${reg.apellido_materno || ''}`.trim();
        
        const mapaFisico = {
            "folio": reg.folio,
            "curp": reg.curp,
            "rfc": reg.rfc,
            "nombre_productor": nombreFisico, // Ahora sí construye el nombre completo
            "pueblo_colonia": reg.colonia_nombre,
            "superficie_prod": reg.superficie_total,
            "fase_proceso": reg.fase_proceso,
            "tipo_produccion": reg.actividad_principal,
            "grado_estudios": reg.escolaridad,
            "ocupacion": reg.ocupacion,
            "estado_civil": reg.estado_civil,
            "calle_numero": reg.calle,
            "cp": reg.codigo_postal
        };

        if (mapaFisico[campoBuscado] !== undefined && mapaFisico[campoBuscado] !== null && mapaFisico[campoBuscado] !== "") {
            return mapaFisico[campoBuscado];
        }

        if (!json) return '';

        // Búsqueda en el JSON si no está en la tabla física
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
    // 3. RENDERIZADO DE TABLA (CORREGIDO)
    // ==========================================
    function renderTable(page) {
        currentPage = page;
        const start = (page - 1) * pageSize;
        const items = filteredData.slice(start, start + pageSize);
        const tbody = $("#tablaCaptura tbody").empty();

        items.forEach(e => {
            const faseLimpia = (e.fase_proceso || 'EMPADRONADO').replace(/_/g, ' ');
            // CORRECCIÓN: Acceso correcto a apellido_paterno y apellido_materno
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
                        <button onclick="abrirEdicion(${e.id})" class="btn btn-sm btn-guinda rounded-circle shadow-sm"><i class="fas fa-user-edit"></i></button>
                    </td>
                </tr>
            `);
        });
        $("#tableInfo").html(`Mostrando <b>${items.length}</b> de <b>${filteredData.length}</b> registros`);
        renderPaginationUI();
    }

    function renderPaginationUI() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        const container = $("#paginationControls").empty();
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }
        container.find('a').on('click', function(e) { e.preventDefault(); renderTable(parseInt($(this).attr('data-page'))); });
    }

    function renderTabResumen(reg, json) {
        const $resumen = $("#resumenCaptura").empty();
        const config = {
            "1. Identidad": ["folio", "curp", "rfc", "nombre_productor", "sexo", "fecha_nacimiento", "tecnico_nombre"],
            "2. Ubicación": ["cp", "pueblo_colonia", "calle_numero", "latitud", "longitud"],
            "3. Perfil": ["grado_estudios", "ocupacion", "estado_civil", "ingreso_mensual", "servicios_salud"],
            "4. Técnica": ["situacion_unidad", "tipo_produccion", "cats_agricola", "detalle_hortalizas", "superficie_prod", "volumen_prod", "unidad_medida"],
            "5. Economía": ["insumos_agricolas", "maquinaria", "problema_principal", "destino_produccion", "financiamiento"],
            "6. Cierre": ["participacion_mujeres", "nuevas_generaciones", "capacitaciones_deseadas", "observaciones"]
        };

        for (const [titulo, campos] of Object.entries(config)) {
            let filas = "";
            campos.forEach(c => {
                let val = getDatoFinal(reg, c, json);
                let displayVal = (val && val !== "") ? val.toString().replace(/_/g, ' ') : '---';
                filas += `<tr><td class="ps-3 text-muted py-2" width="45%">${c.replace(/_/g, ' ').toUpperCase()}</td><td class="fw-bold py-2 text-dark">${displayVal}</td></tr>`;
            });
            $resumen.append(`
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header py-2 bg-white border-bottom text-guinda fw-bold small"><i class="fas fa-caret-right me-2 text-warning"></i>${titulo}</div>
                        <div class="card-body p-0"><table class="table table-sm mb-0" style="font-size:0.75rem;"><tbody>${filas}</tbody></table></div>
                    </div>
                </div>
            `);
        }
    }

    // ==========================================
    // 4. LÓGICA DEL MODAL
    // ==========================================
    window.controlarDependencias = function() {
        if ($("#in_tiene_discap").val() === "SI") {
            $("#in_cual_discap").prop("disabled", false).removeClass("bg-light");
        } else {
            $("#in_cual_discap").val("NA").prop("disabled", true).addClass("bg-light");
        }
    };

    $(document).on("change", "#in_tiene_discap", window.controlarDependencias);

    window.abrirEdicion = function(id) {
    const reg = rawData.find(i => i.id == id);
    if (!reg) return;
    const json = reg.respuestas_json ? JSON.parse(reg.respuestas_json) : {};

    $("#formCaptura")[0].reset();
    $("#reg_id").val(reg.id);
    $("#spanFolio").text(reg.folio || 'S/F');

    // 1. Obtener Nombre Completo Real (Priorizando el capturado originalmente)
    const fullNombre = getDatoFinal(reg, "nombre_productor", json);
    const seg = segmentarNombreCompleto(fullNombre);
    
    // Llenar campos de edición con segmentación corregida
    $("#in_nombre_productor").val(seg.nombres); 
    $("#in_paterno").val(seg.paterno);
    $("#in_materno").val(seg.materno);

    // 2. Llenado Automático Masivo
    $("#formCaptura input, #formCaptura select, #formCaptura textarea").each(function() {
        const el = $(this);
        const name = el.attr('name');
        const excluidos = ['id', 'nombre_productor', 'paterno', 'materno', 'rfc', 'curp'];
        
        if (!name || excluidos.includes(name)) return;
        const cleanName = name.replace('[]', '');
        const valor = getDatoFinal(reg, cleanName, json);

        if (valor !== undefined && valor !== "") {
            if (el.is(':checkbox')) {
                el.prop('checked', valor === 'SI' || valor === '1' || valor === 1);
            } else {
                el.val(valor);
            }
        }
    });
    const curpFinal = reg.curp || getDatoFinal(reg, "curp", json);
    $("#in_curp_edit").val(curpFinal);

    const rfcFinal = reg.rfc || getDatoFinal(reg, "rfc", json);
    $("#in_rfc").val(rfcFinal);

    // Si el RFC sigue vacío, intentamos sacarlo de los primeros 10 del CURP
    if (!$("#in_rfc").val() && curpFinal) {
        $("#in_rfc").val(curpFinal.substring(0, 10));
    }

    // 3. Mostrar Botón PDF y configurar evento de forma limpia
    if (reg.id) {
        $("#btnDescargarPDF")
            .removeClass('d-none')
            .off('click') // Prevenir múltiples eventos acumulados
            .on('click', function() {
                const url = `<?php echo URLROOT; ?>/Expediente/imprimirSolicitud/${reg.id}`;
                window.open(url, '_blank');
            });
    }

    renderTabResumen(reg, json);
    window.controlarDependencias(); 
    bootstrap.Tab.getOrCreateInstance(document.querySelector('#tabExpediente li:first-child a')).show();
    $("#modalEdicion").modal('show');
};

    $("#tablaSearch").on("keyup", function() {
        const val = $(this).val().toLowerCase();
        filteredData = rawData.filter(e => 
            (e.folio || "").toLowerCase().includes(val) || 
            (e.nombre || "").toLowerCase().includes(val) ||
            (e.curp || "").toLowerCase().includes(val)
        );
        renderTable(1);
    });
});

function confirmarSalida() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, salir'
    }).then((result) => {
        if (result.isConfirmed) window.location.href = '<?php echo URLROOT; ?>/Auth/logout';
    });
}

function confirmarGuardado() {
    const formData = new FormData(document.getElementById('formCaptura'));
    Swal.fire({
        title: '¿Guardar cambios?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#773357',
        confirmButtonText: 'Guardar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Guardando...', didOpen: () => { Swal.showLoading() } });
            fetch('<?php echo URLROOT; ?>/Captura/actualizar', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') Swal.fire('¡Éxito!', data.msg, 'success').then(() => location.reload());
                else Swal.fire('Error', data.msg, 'error');
            });
        }
    });
}
</script>