<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">

<style>
    :root { --guinda: #773357; --guinda-light: #fdf2f7; --guinda-hover: #5a2642; --gris-fondo: #f4f6f9; }
    body { background-color: var(--gris-fondo); font-family: 'Montserrat', sans-serif; }
    
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
    .card-header { background-color: white !important; border-bottom: 1px solid var(--guinda-light); padding: 1.25rem; border-radius: 15px 15px 0 0 !important; }
    .text-guinda { color: var(--guinda); }
    .btn-guinda { background-color: var(--guinda); color: white; border-radius: 10px; font-weight: 600; padding: 8px 18px; border: none; }
    
    /* Tablas y Fases */
    .table thead th { background-color: var(--guinda) !important; color: white !important; text-transform: uppercase; font-size: 0.7rem; padding: 12px; }
    .badge-fase { border-radius: 50px; padding: 6px 12px; font-weight: 700; font-size: 0.65rem; text-transform: uppercase; }
    .fase-EMPADRONADO { background-color: #6c757d; color: white; }
    .fase-VALIDACION_DOCS { background-color: #17a2b8; color: white; }
    .fase-EN_REVISION { background-color: #ffc107; color: #333; }
    .fase-APROBADO { background-color: #28a745; color: white; }
    .fase-RECHAZADO { background-color: #dc3545; color: white; }

    /* Estilo de Pestañas (Tabs) */
    .nav-tabs .nav-link { border: none; color: #666; font-weight: 600; padding: 1rem; transition: 0.3s; }
    .nav-tabs .nav-link.active { color: var(--guinda); border-bottom: 3px solid var(--guinda); background: transparent; }
    .nav-tabs .nav-link:hover { color: var(--guinda); background: var(--guinda-light); }
    
    .border-bottom-light { border-bottom: 1px solid #f1f1f1; }
    .pagination .page-link { color: var(--guinda); border: none; margin: 0 3px; border-radius: 8px !important; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--guinda) !important; color: white !important; }
    .bg-guinda-light { background-color: #fdf2f7 !important; }
    .list-group-item { transition: background 0.2s; }
    .list-group-item:hover { background-color: #fafafa; }
    .border-bottom-light { border-bottom: 1px solid #f1f1f1; }
    
    /* Animación suave al abrir tarjetas */
    #resumenCaptura .card {
        animation: fadeIn 0.4s ease-in-out;
    }
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
                        <option value="OTRO">Otro</option>
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
                    <select class="form-select form-select-sm" name="estado_civil" id="in_estado_civil"></select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Grado de Estudios</label>
                    <select class="form-select form-select-sm" name="grado_estudios" id="in_estudios"></select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Ocupación</label>
                    <input type="text" class="form-control form-control-sm" name="ocupacion" id="in_ocupacion">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">¿Alguna Discapacidad?</label>
                    <select class="form-select form-select-sm" name="tiene_discapacidad" id="in_tiene_discap">
                        <option value="NO">NO</option>
                        <option value="SI">SÍ</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Cual discapacidad?</label>
                    <input type="text" class="form-control form-control-sm" name="cual_discapacidad" id="in_cual_discap">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Grupo Étnico</label>
                    <input type="text" class="form-control form-control-sm" name="grupo_etnico" id="in_grupo_etnico_edit">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Cual grupo étnico?</label>
                    <input type="text" class="form-control form-control-sm" name="grupo_etnico_cual" id="in_grupo_cual">
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
                    <label class="small fw-bold">Línea de ayuda solicitada</label>
                    <input type="text" class="form-control form-control-sm" name="linea_ayuda" id="in_linea_ayuda">
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
                    <label class="small fw-bold">Documento de Propiedad</label>
                    <select class="form-select form-select-sm" name="tipo_documento_prop" id="in_tipo_doc">
                        <option value="TITULO_PROPIEDAD">Título de Propiedad</option>
                        <option value="CERTIFICADO_PARCELARIO">Certificado Parcelario</option>
                        <option value="CONSTANCIA_POSESION">Constancia de Posesión</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Poblado/Colonia de la U.P.</label>
                    <input type="text" class="form-control form-control-sm" name="pueblo_colonia_up" id="in_pueblo_up">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Parajes</label>
                    <input type="text" class="form-control form-control-sm" name="parajes" id="in_parajes">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Tenencia de la Tierra</label>
                    <select class="form-select form-select-sm" name="tenencia_tierra" id="in_tenencia">
                        <option value="COMUNAL">Comunal</option>
                        <option value="EJIDAL">Ejidal</option>
                        <option value="PRIVADA">Propiedad Privada</option>
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
                            <div class="card shadow-sm border-0">
                                <div class="list-group list-group-flush">
                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span><i class="fas fa-id-card text-guinda me-3"></i> <b>Identificación Oficial (INE/Pasaporte)</b></span>
                                        <input type="checkbox" name="check_ine" class="form-check-input h5 mb-0">
                                    </label>
                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span><i class="fas fa-home text-guinda me-3"></i> <b>Comprobante de Domicilio (Luz/Agua/Predial)</b></span>
                                        <input type="checkbox" name="check_dom" class="form-check-input h5 mb-0">
                                    </label>
                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span><i class="fas fa-fingerprint text-guinda me-3"></i> <b>CURP Certificada (Actualizada)</b></span>
                                        <input type="checkbox" name="check_curp" class="form-check-input h5 mb-0">
                                    </label>
                                    <label class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span><i class="fas fa-map-marked text-guinda me-3"></i> <b>Certificado de Producción / Tierra</b></span>
                                        <input type="checkbox" name="check_tierra" class="form-check-input h5 mb-0">
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" onclick="confirmarGuardado()" class="btn btn-guinda px-5 shadow"><i class="fas fa-save me-2"></i>GUARDAR EXPEDIENTE</button>
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
    // 1. CARGA INICIAL Y KPIs
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
    // 2. EXTRACTOR DE DATOS MULTINIVEL (EL CEREBRO)
    // ==========================================
    function getDatoFinal(reg, campoBuscado, json) {
        // A. Prioridad 1: Datos directos de la base de datos (Raíz del objeto)
        const mapaFisico = {
            "folio": reg.folio,
            "curp": reg.curp,
            "nombre_productor": `${reg.nombre || ''} ${reg.paterno || ''} ${reg.materno || ''}`.trim(),
            "pueblo_colonia": reg.colonia_nombre,
            "superficie_prod": reg.superficie_total,
            "fase_proceso": reg.fase_proceso
        };

        if (mapaFisico[campoBuscado] !== undefined && mapaFisico[campoBuscado] !== null && mapaFisico[campoBuscado] !== "") {
            return mapaFisico[campoBuscado];
        }

        if (!json) return '';

        // B. Prioridad 2: Buscar en el JSON (Secciones 1 a 50)
        let resultados = [];
        for (let seccion in json) {
            let contenido = json[seccion];

            // Caso Especial: Coordenadas (Sección 6)
            if (seccion === "6" && contenido[campoBuscado]) return contenido[campoBuscado];

            // Caso General: Arreglos de {name, value}
            if (Array.isArray(contenido)) {
                contenido.forEach(item => {
                    if (item.name === campoBuscado || item.name === campoBuscado + '[]') {
                        if (item.value) resultados.push(item.value);
                    }
                });
            } 
            // Caso: Valores directos (Secciones 14, 16, etc. que son solo "SI/NO")
            else if (seccion === campoBuscado && typeof contenido === 'string') {
                return contenido;
            }
        }

        // Devolver string plano (si es multiselección, los junta con comas)
        return resultados.length > 0 ? resultados.join(', ').replace(/_/g, ' ') : '';
    }

    // ==========================================
    // 3. RENDERIZADO DE TABLA Y PAGINACIÓN
    // ==========================================
    function renderTable(page) {
        currentPage = page;
        const start = (page - 1) * pageSize;
        const items = filteredData.slice(start, start + pageSize);
        const tbody = $("#tablaCaptura tbody").empty();

        if (items.length === 0) {
            tbody.append('<tr><td colspan="5" class="text-center py-4 text-muted">No se encontraron registros</td></tr>');
            return;
        }

        items.forEach(e => {
            const faseLimpia = (e.fase_proceso || 'EMPADRONADO').replace(/_/g, ' ');
            tbody.append(`
                <tr>
                    <td class="ps-3 fw-bold text-guinda">${e.folio || 'S/F'}</td>
                    <td class="small">
                        <div class="fw-bold text-dark">${e.nombre || ''} ${e.paterno || ''} ${e.materno || ''}</div>
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
            container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link shadow-sm" href="#" data-page="${i}">${i}</a></li>`);
        }
        container.find('a').on('click', function(e) { e.preventDefault(); renderTable(parseInt($(this).attr('data-page'))); });
    }

    // ==========================================
    // 4. PESTAÑA 1: RESUMEN VISUAL (UI/UX)
    // ==========================================
    function renderTabResumen(reg, json) {
        const $resumen = $("#resumenCaptura").empty();
        const config = {
            "Identidad y Registro": ["folio", "curp", "nombre_productor", "sexo", "fecha_nacimiento", "estado_civil", "ocupacion"],
            "Ubicación Predio": ["cp", "pueblo_colonia", "calle_numero", "latitud", "longitud"],
            "Producción y Técnica": ["situacion_unidad", "tipo_produccion", "cats_agricola", "superficie_prod", "volumen_prod", "unidad_medida", "insumos_agricolas", "problema_principal"],
            "Social y Apoyos": ["grado_estudios", "tipo_apoyo", "capacitaciones_deseadas", "participacion_mujeres", "observaciones"]
        };

        for (const [titulo, campos] of Object.entries(config)) {
            let filas = "";
            campos.forEach(c => {
                let val = getDatoFinal(reg, c, json);
                filas += `<tr><td class="ps-3 text-muted py-2" width="40%">${c.replace(/_/g, ' ').toUpperCase()}</td><td class="fw-bold py-2 text-dark">${val || '---'}</td></tr>`;
            });
            $resumen.append(`
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="card-header py-2 bg-white border-bottom text-guinda fw-bold small"><i class="fas fa-caret-right me-2 text-warning"></i>${titulo}</div>
                        <div class="card-body p-0"><table class="table table-sm table-hover mb-0" style="font-size:0.75rem;"><tbody>${filas}</tbody></table></div>
                    </div>
                </div>
            `);
        }
    }

    // ==========================================
    // 5. FUNCIÓN MAESTRA: ABRIR MODAL (DATA BINDING)
    // ==========================================
    window.abrirEdicion = function(id) {
        const reg = rawData.find(i => i.id == id);
        if (!reg) return;
        const json = reg.respuestas_json ? JSON.parse(reg.respuestas_json) : {};

        // A. Reset Form y Header
        $("#formCaptura")[0].reset();
        $("#reg_id").val(reg.id);
        $("#spanFolio").text(reg.folio || 'S/F');

        // B. Llenado Dinámico de Inputs (Pestaña 2 y 3)
        $("#formCaptura input, #formCaptura select, #formCaptura textarea").each(function() {
            const el = $(this);
            const name = el.attr('name');
            if (!name || name === 'id') return;

            const cleanName = name.replace('[]', '');
            const valor = getDatoFinal(reg, cleanName, json);

            if (el.is(':checkbox')) {
                // Checkboxes de documentos o multiselección
                const vals = valor.split(', ');
                el.prop('checked', vals.includes(el.val()) || valor === 'SI');
            } 
            else if (el.is('select')) {
                // Poblado dinámico si la opción no existe
                if (valor && el.find(`option[value="${valor}"]`).length === 0) {
                    el.append(`<option value="${valor}">${valor}</option>`);
                }
                el.val(valor);
            } 
            else {
                // Texto, Date, Email, Textarea
                el.val(valor);
                // UX: Si es readonly en PHP, aplicar fondo gris
                if (el.prop('readonly')) el.addClass('bg-light');
            }
        });

        // C. Renderizar Resumen y Mostrar
        renderTabResumen(reg, json);
        bootstrap.Tab.getOrCreateInstance(document.querySelector('#tabExpediente li:first-child a')).show();
        $("#modalEdicion").modal('show');
    };

    // ==========================================
    // 6. BUSCADOR Y ACCIONES FINALES
    // ==========================================
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

function confirmarGuardado() {
    const formData = new FormData(document.getElementById('formCaptura'));
    Swal.fire({
        title: '¿Confirmar cambios?',
        text: "Se actualizará la fase y la información del expediente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#773357',
        confirmButtonText: 'Sí, guardar'
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