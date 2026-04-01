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

    /* Tabs Styling */
    .nav-tabs .nav-link { border: none; color: #666; font-weight: 600; padding: 1rem; }
    .nav-tabs .nav-link.active { color: var(--guinda); border-bottom: 3px solid var(--guinda); background: transparent; }
    .border-bottom-light { border-bottom: 1px solid #f1f1f1; }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-7">
            <h2 class="fw-bold text-guinda mb-0">Expediente Digital</h2>
            <p class="text-muted">Gestión de Fases y Validación de Documentos</p>
        </div>
        <div class="col-md-5 text-end">
            <button onclick="location.reload()" class="btn btn-guinda shadow-sm"><i class="fas fa-sync-alt me-2"></i>Sincronizar</button>
            <button onclick="confirmarSalida()" class="btn btn-danger rounded-3 ms-2"><i class="fas fa-power-off"></i></button>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-secondary"><h6 class="text-muted small mb-1">EMPADRONADOS</h6><h3 class="fw-bold mb-0" id="kpi-total">0</h3></div></div>
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-info"><h6 class="text-muted small mb-1">VALIDACIÓN DOCS</h6><h3 class="fw-bold mb-0" id="kpi-pendientes">0</h3></div></div>
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-warning"><h6 class="text-muted small mb-1">EN REVISIÓN</h6><h3 class="fw-bold mb-0" id="kpi-revision">0</h3></div></div>
        <div class="col-md-3"><div class="card p-3 border-start border-4 border-success"><h6 class="text-muted small mb-1">APROBADOS</h6><h3 class="fw-bold mb-0" id="kpi-aprobados">0</h3></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-list me-2"></i>Control de Expedientes</h6>
            <input type="text" id="tablaSearch" class="form-control form-control-sm w-25" placeholder="Buscar por folio o nombre...">
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaCaptura">
                    <thead>
                        <tr>
                            <th class="ps-3">Folio</th>
                            <th>Productor</th>
                            <th>Fase Actual</th>
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
                <h5 class="modal-title fw-bold"><i class="fas fa-folder-open me-2"></i>VALIDACIÓN: <span id="spanFolio"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <ul class="nav nav-tabs nav-fill bg-white border-bottom" id="tabExpediente">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-datos"><i class="fas fa-info-circle me-1"></i> DATOS CAPTURADOS</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-extra"><i class="fas fa-edit me-1"></i> CAPTURA EXTRA</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-docs"><i class="fas fa-file-upload me-1"></i> DOCUMENTACIÓN</a></li>
            </ul>

            <div class="modal-body bg-light">
                <form id="formCaptura">
                    <input type="hidden" id="reg_id" name="id">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-datos">
                            <div class="row g-3" id="resumenCaptura"></div>
                        </div>

                        <div class="tab-pane fade" id="tab-extra">
                            <div class="card shadow-sm border-0 p-3">
                                <label class="fw-bold text-guinda mb-2">Observaciones de Validación</label>
                                <textarea class="form-control mb-3" name="observaciones_capturista" rows="4"></textarea>
                                <label class="fw-bold text-danger mb-2">Promover a Fase:</label>
                                <select class="form-select fw-bold border-danger" name="fase_proceso" id="in_fase">
                                    <option value="EMPADRONADO">1. EMPADRONADO</option>
                                    <option value="VALIDACION_DOCS">2. VALIDACIÓN DE DOCS</option>
                                    <option value="EN_REVISION">3. EN REVISIÓN TÉCNICA</option>
                                    <option value="APROBADO">4. APROBADO</option>
                                </select>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-docs">
                            <div class="list-group">
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-check-circle text-success me-2"></i> Identificación Oficial (INE)</span>
                                    <input type="checkbox" name="check_ine" class="form-check-input">
                                </label>
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-check-circle text-success me-2"></i> Comprobante de Domicilio</span>
                                    <input type="checkbox" name="check_dom" class="form-check-input">
                                </label>
                                <label class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-check-circle text-success me-2"></i> CURP Certificada</span>
                                    <input type="checkbox" name="check_curp" class="form-check-input">
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" onclick="confirmarGuardado()" class="btn btn-guinda px-4 shadow">GUARDAR CAMBIOS</button>
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

    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            rawData = data.maestro || [];
            filteredData = [...rawData];
            actualizarKPIs(rawData);
            renderTable(1);
        });

    function actualizarKPIs(data) {
        $("#kpi-total").text(data.length);
        $("#kpi-pendientes").text(data.filter(i => i.fase_proceso === 'VALIDACION_DOCS').length);
        $("#kpi-revision").text(data.filter(i => i.fase_proceso === 'EN_REVISION').length);
        $("#kpi-aprobados").text(data.filter(i => i.fase_proceso === 'APROBADO').length);
    }

    function renderTable(page) {
        currentPage = page;
        const start = (page - 1) * pageSize;
        const items = filteredData.slice(start, start + pageSize);
        const tbody = $("#tablaCaptura tbody").empty();

        items.forEach(e => {
            const faseLimpia = (e.fase_proceso || 'EMPADRONADO').replace('_', ' ');
            tbody.append(`
                <tr>
                    <td class="ps-3 fw-bold text-guinda">${e.folio}</td>
                    <td class="small fw-bold">${e.nombre} ${e.paterno}</td>
                    <td><span class="badge badge-fase fase-${e.fase_proceso || 'EMPADRONADO'}">${faseLimpia}</span></td>
                    <td class="text-center fw-bold">${parseFloat(e.superficie_total).toFixed(2)}</td>
                    <td class="text-center">
                        <button onclick="abrirEdicion(${e.id})" class="btn btn-sm btn-guinda rounded-circle shadow-sm">
                            <i class="fas fa-user-edit"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
        renderPaginationUI();
    }

    function renderPaginationUI() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        const container = $("#paginationControls").empty();
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link shadow-sm" href="#" data-page="${i}">${i}</a></li>`);
        }
        container.find('a').on('click', function(e) {
            e.preventDefault();
            renderTable(parseInt($(this).attr('data-page')));
        });
    }

    window.abrirEdicion = function(id) {
        const reg = rawData.find(i => i.id == id);
        const json = JSON.parse(reg.respuestas_json || '{}');
        
        $("#reg_id").val(reg.id);
        $("#spanFolio").text(reg.folio);
        $("#in_fase").val(reg.fase_proceso || 'EMPADRONADO');

        const $resumen = $("#resumenCaptura").empty();
        const grupos = {
            "Identidad": ["curp", "nombre_productor", "sexo", "estado_civil"],
            "Ubicación": ["cp", "pueblo_colonia", "situacion_unidad"],
            "Producción": ["tipo_produccion", "superficie_prod", "volumen_prod", "unidad_medida"]
        };

        for (const [titulo, campos] of Object.entries(grupos)) {
            let html = `<div class="col-md-6"><div class="card h-100 border-0 shadow-sm"><div class="card-header py-2 bg-white fw-bold small text-guinda">${titulo}</div><div class="card-body p-0"><table class="table table-sm mb-0">`;
            campos.forEach(c => {
                let valor = extraerValor(json, c);
                html += `<tr class="border-bottom-light"><td class="ps-3 text-muted py-2 small" width="40%">${c.toUpperCase()}</td><td class="fw-bold py-2 small">${valor || '---'}</td></tr>`;
            });
            html += `</table></div></div></div>`;
            $resumen.append(html);
        }

        $("#modalEdicion").modal('show');
    };

    function extraerValor(json, campo) {
        for (let sec in json) {
            if (Array.isArray(json[sec])) {
                let found = json[sec].find(i => i.name === campo || i.name === campo + '[]');
                if (found) return found.value;
            }
        }
        return '';
    }

    $("#tablaSearch").on("keyup", function() {
        const val = $(this).val().toLowerCase();
        filteredData = rawData.filter(e => (e.folio || "").toLowerCase().includes(val) || (e.nombre || "").toLowerCase().includes(val));
        renderTable(1);
    });
});

function confirmarGuardado() {
    const formData = new FormData(document.getElementById('formCaptura'));
    fetch('<?php echo URLROOT; ?>/Captura/actualizar', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') Swal.fire('¡Éxito!', data.msg, 'success').then(() => location.reload());
    });
}
</script>