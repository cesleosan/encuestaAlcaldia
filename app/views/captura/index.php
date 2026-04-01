<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">

<style>
    :root { 
        --guinda: #773357; 
        --guinda-light: #fdf2f7; 
        --guinda-hover: #5a2642;
        --gris-fondo: #f4f6f9;
    }
    body { background-color: var(--gris-fondo); font-family: 'Montserrat', sans-serif; }
    
    /* UI Premium */
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
    .card-header { background-color: white !important; border-bottom: 1px solid var(--guinda-light); padding: 1.25rem; border-radius: 15px 15px 0 0 !important; }
    .text-guinda { color: var(--guinda); }
    .btn-guinda { background-color: var(--guinda); color: white; border-radius: 10px; font-weight: 600; padding: 8px 18px; border: none; }
    .btn-guinda:hover { background-color: var(--guinda-hover); color: white; }

    /* Tablas Estilizadas */
    .table thead th { 
        background-color: var(--guinda) !important; 
        color: white !important; 
        text-transform: uppercase; 
        font-size: 0.7rem; 
        font-weight: 700;
        padding: 12px;
        border: none;
    }
    .table-hover tbody tr:hover { background-color: var(--guinda-light) !important; }
    .table td { vertical-align: middle; }

    /* Badges de Fases de Proceso */
    .badge-fase { border-radius: 50px; padding: 6px 12px; font-weight: 700; font-size: 0.65rem; text-transform: uppercase; }
    .fase-EMPADRONADO { background-color: #6c757d; color: white; }
    .fase-VALIDACION_DOCS { background-color: #17a2b8; color: white; }
    .fase-EN_REVISION { background-color: #ffc107; color: #333; }
    .fase-APROBADO { background-color: #28a745; color: white; }
    .fase-RECHAZADO { background-color: #dc3545; color: white; }

    .pagination .page-link { color: var(--guinda); border: none; margin: 0 3px; border-radius: 8px !important; font-weight: 600; }
    .pagination .page-item.active .page-link { background-color: var(--guinda) !important; color: white !important; }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-7">
            <h2 class="fw-bold text-guinda mb-0">Módulo de Validación y Captura</h2>
            <p class="text-muted">Complemento de información y trazabilidad de expedientes</p>
        </div>
        <div class="col-md-5 text-end">
            <button onclick="location.reload()" class="btn btn-guinda shadow-sm"><i class="fas fa-sync-alt me-2"></i>Actualizar Listado</button>
            <button onclick="confirmarSalida()" class="btn btn-danger rounded-3 ms-2"><i class="fas fa-power-off"></i></button>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-secondary">
                <h6 class="text-muted small mb-1">TOTAL REGISTROS</h6>
                <h3 class="fw-bold mb-0" id="kpi-total">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-info">
                <h6 class="text-muted small mb-1">PENDIENTES DOCS</h6>
                <h3 class="fw-bold mb-0" id="kpi-pendientes">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-warning">
                <h6 class="text-muted small mb-1">EN REVISIÓN TÉCNICA</h6>
                <h3 class="fw-bold mb-0" id="kpi-revision">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-success">
                <h6 class="text-muted small mb-1">TOTAL APROBADOS</h6>
                <h3 class="fw-bold mb-0" id="kpi-aprobados">0</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-folder-open me-2"></i>Expedientes por Validar</h6>
                    <input type="text" id="tablaSearch" class="form-control form-control-sm w-25 shadow-sm" placeholder="Buscar por folio, nombre o CURP...">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaCaptura">
                            <thead>
                                <tr>
                                    <th class="ps-3">Folio</th>
                                    <th>Productor</th>
                                    <th>CURP</th>
                                    <th>Fase Actual</th>
                                    <th class="text-center">Último Cambio</th>
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
    </div>
</div>

<div class="modal fade" id="modalEdicion" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-guinda text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Validación de Expediente: <span id="spanFolio"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <form id="formCaptura">
                    <input type="hidden" id="reg_id" name="id">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card h-100 border-top border-guinda border-3">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-id-card me-2"></i>Datos Generales</h6>
                                    <div class="mb-2">
                                        <label class="small fw-bold text-muted">Nombre Completo</label>
                                        <input type="text" class="form-control" name="nombre" id="in_nombre">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold text-muted">CURP</label>
                                        <input type="text" class="form-control" name="curp" id="in_curp">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold text-muted">Teléfono</label>
                                        <input type="text" class="form-control" name="telefono" id="in_tel">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100 border-top border-guinda border-3">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-map-marker-alt me-2"></i>Ubicación de la Unidad</h6>
                                    <div class="mb-2">
                                        <label class="small fw-bold text-muted">Calle y Número</label>
                                        <input type="text" class="form-control" name="calle" id="in_calle">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold text-muted">Colonia / Pueblo</label>
                                        <input type="text" class="form-control" name="colonia" id="in_colonia">
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold text-muted">CP</label>
                                        <input type="text" class="form-control" name="cp" id="in_cp">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card h-100 border-top border-warning border-3">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-tasks me-2"></i>Control de Proceso</h6>
                                    <div class="mb-3">
                                        <label class="small fw-bold text-danger">FASE ACTUAL DEL PROCESO</label>
                                        <select class="form-select fw-bold bg-white" name="fase_proceso" id="in_fase">
                                            <option value="EMPADRONADO">1. EMPADRONADO</option>
                                            <option value="VALIDACION_DOCS">2. VALIDACIÓN DE DOCS</option>
                                            <option value="EN_REVISION">3. EN REVISIÓN TÉCNICA</option>
                                            <option value="APROBADO">4. APROBADO / ACREEDOR</option>
                                            <option value="RECHAZADO">5. RECHAZADO</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small fw-bold text-muted">Superficie Final (ha)</label>
                                        <input type="number" step="0.01" class="form-control" name="superficie" id="in_sup">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" onclick="confirmarGuardado()" class="btn btn-guinda px-4 shadow-sm"><i class="fas fa-save me-2"></i>Guardar y Promover</button>
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

    // 1. Cargar Datos (Reutilizamos getEstadisticas o creamos uno nuevo)
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            rawData = data.maestro;
            filteredData = [...rawData];
            actualizarKPIs(data.maestro);
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
            tbody.append(`
                <tr>
                    <td class="ps-3 fw-bold text-guinda">${e.folio}</td>
                    <td class="small fw-bold">${e.nombre} ${e.paterno}</td>
                    <td class="small font-monospace">${e.curp}</td>
                    <td><span class="badge badge-fase fase-${e.fase_proceso}">${e.fase_proceso.replace('_', ' ')}</span></td>
                    <td class="text-center small text-muted">${e.fecha_inicio.substring(0,10)}</td>
                    <td class="text-center">
                        <button onclick="abrirEdicion(${e.id})" class="btn btn-sm btn-guinda rounded-circle shadow-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $("#tableInfo").html(`Mostrando <b>${items.length}</b> de <b>${filteredData.length}</b> registros`);
        renderPaginationUI();
    }

    // Funciones de Paginación y Buscador (Similares al Dashboard)
    // ... [Omitido por brevedad, usa la misma lógica de ventana de 5 botones que ya tenemos]
    
    window.abrirEdicion = function(id) {
        const reg = rawData.find(i => i.id == id);
        const json = JSON.parse(reg.respuestas_json || '{}');
        
        $("#reg_id").val(reg.id);
        $("#spanFolio").text(reg.folio);
        $("#in_nombre").val(`${reg.nombre} ${reg.paterno} ${reg.materno}`);
        $("#in_curp").val(reg.curp);
        $("#in_tel").val(extraerValor(json, 'tel_particular'));
        $("#in_calle").val(extraerValor(json, 'calle_numero'));
        $("#in_colonia").val(reg.colonia_nombre);
        $("#in_cp").val(extraerValor(json, 'cp'));
        $("#in_fase").val(reg.fase_proceso);
        $("#in_sup").val(reg.superficie_total);

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
});

function confirmarGuardado() {
    Swal.fire({
        title: '¿Guardar cambios?',
        text: "Se actualizará la fase del proceso y la información técnica.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#773357',
        confirmButtonText: 'Sí, guardar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí llamaríamos al método del controlador vía AJAX/Fetch
            const data = new FormData(document.getElementById('formCaptura'));
            // fetch('<?php echo URLROOT; ?>/Captura/actualizar', { method: 'POST', body: data })...
            Swal.fire('¡Éxito!', 'Expediente actualizado.', 'success').then(() => location.reload());
        }
    });
}
</script>