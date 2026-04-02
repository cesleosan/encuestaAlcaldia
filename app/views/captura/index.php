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
                            <div class="card shadow-sm border-0 p-4">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="fw-bold text-guinda mb-2">Observaciones de la Validación</label>
                                        <textarea class="form-control" name="observaciones_capturista" rows="4" placeholder="Escriba aquí los detalles encontrados durante la revisión física de documentos..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold text-danger mb-2">PROMOCIÓN DE FASE:</label>
                                        <select class="form-select fw-bold border-danger" name="fase_proceso" id="in_fase">
                                            <option value="EMPADRONADO">1. EMPADRONADO (Campo)</option>
                                            <option value="VALIDACION_DOCS">2. VALIDACIÓN DE DOCS</option>
                                            <option value="EN_REVISION">3. EN REVISIÓN TÉCNICA</option>
                                            <option value="APROBADO">4. APROBADO / ACREEDOR</option>
                                            <option value="RECHAZADO">5. RECHAZADO / INCOMPLETO</option>
                                        </select>
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

    // 1. CARGA INICIAL DE DATOS
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            rawData = data.maestro || [];
            filteredData = [...rawData];
            actualizarKPIs(rawData);
            renderTable(1);
        })
        .catch(err => console.error("Error al obtener datos:", err));

    // 2. ACTUALIZACIÓN DE INDICADORES (KPIs)
    function actualizarKPIs(data) {
        $("#kpi-total").text(data.length);
        $("#kpi-pendientes").text(data.filter(i => i.fase_proceso === 'VALIDACION_DOCS').length);
        $("#kpi-revision").text(data.filter(i => i.fase_proceso === 'EN_REVISION').length);
        $("#kpi-aprobados").text(data.filter(i => i.fase_proceso === 'APROBADO').length);
    }

    // 3. RENDERIZADO DE LA TABLA PRINCIPAL
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
                    <td>
                        <span class="badge badge-fase fase-${e.fase_proceso || 'EMPADRONADO'}">
                            ${faseLimpia}
                        </span>
                    </td>
                    <td class="text-center fw-bold text-secondary">${parseFloat(e.superficie_total || 0).toFixed(2)} ha</td>
                    <td class="text-center">
                        <button onclick="abrirEdicion(${e.id})" class="btn btn-sm btn-guinda rounded-circle shadow-sm" title="Editar Expediente">
                            <i class="fas fa-user-edit"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $("#tableInfo").html(`Mostrando <b>${items.length}</b> de <b>${filteredData.length}</b> registros`);
        renderPaginationUI();
    }

    // 4. LÓGICA DE PAGINACIÓN (WINDOWED - MÁXIMO 5 BOTONES)
    function renderPaginationUI() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        const container = $("#paginationControls").empty();
        if (totalPages <= 1) return;

        let start = Math.max(1, currentPage - 2);
        let end = Math.min(totalPages, start + 4);
        if (end - start < 4) start = Math.max(1, end - 4);

        // Botón Anterior
        if (currentPage > 1) {
            container.append(`<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}"><i class="fas fa-chevron-left"></i></a></li>`);
        }

        for (let i = start; i <= end; i++) {
            container.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link shadow-sm" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        // Botón Siguiente
        if (currentPage < totalPages) {
            container.append(`<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}"><i class="fas fa-chevron-right"></i></a></li>`);
        }

        container.find('a').on('click', function(e) {
            e.preventDefault();
            renderTable(parseInt($(this).attr('data-page')));
        });
    }

    // 5. EXTRACTOR INTELIGENTE (Crucial para llenar el modal sin "undefined")
    function obtenerDatoFinal(reg, campoBuscado, json) {
        // A. Prioridad 1: Columnas físicas de la tabla 'encuestas'
        const mapaFisico = {
            "tecnico_nombre": reg.encuestador,
            "folio": reg.folio,
            "curp": reg.curp,
            "nombre_productor": `${reg.nombre} ${reg.paterno} ${reg.materno}`.trim(),
            "pueblo_colonia": reg.colonia_nombre,
            "superficie_prod": reg.superficie_total
        };

        if (mapaFisico[campoBuscado] !== undefined && mapaFisico[campoBuscado] !== null && mapaFisico[campoBuscado] !== "") {
            return mapaFisico[campoBuscado];
        }

        // B. Prioridad 2: Buscar dentro del respuestas_json
        if (!json) return '---';

        // Caso especial Sección 6 (Objeto directo de geolocalización)
        if (json["6"] && json["6"][campoBuscado]) return json["6"][campoBuscado];

        // Barrido por secciones (1, 2, 3...)
        for (let sec in json) {
            let contenido = json[sec];
            if (Array.isArray(contenido)) {
                // El JSON guarda las respuestas como {name: 'campo', value: 'valor'}
                const found = contenido.find(i => i.name === campoBuscado || i.name === campoBuscado + '[]');
                if (found) return found.value;
            } else if (typeof contenido === 'string' && sec === campoBuscado) {
                // Para campos que se guardan como llave:valor directo (ej. "14": "NO")
                return contenido;
            }
        }
        return '---';
    }

    // 6. FUNCIÓN GLOBAL: ABRIR MODAL Y LLENAR EXPEDIENTE
    window.abrirEdicion = function(id) {
        const reg = rawData.find(i => i.id == id);
        if (!reg) return;

        const json = reg.respuestas_json ? JSON.parse(reg.respuestas_json) : {};
        
        // Cargar IDs y Estatus
        $("#reg_id").val(reg.id);
        $("#spanFolio").text(reg.folio);
        $("#in_fase").val(reg.fase_proceso || 'EMPADRONADO');

        // Pestaña 1: Resumen de Datos (Las 23 columnas agrupadas)
        const $resumen = $("#resumenCaptura").empty();
        const gruposConfig = {
            "Identidad y Registro": ["tecnico_nombre", "curp", "nombre_productor", "sexo", "estado_civil", "ocupacion"],
            "Contacto y Ubicación": ["tel_particular", "tel_recados", "email", "cp", "pueblo_colonia"],
            "Situación y Estudios": ["situacion_unidad", "grado_estudios", "tipo_agua", "financiamiento"],
            "Producción y Apoyos": ["tema_capacitacion", "tipo_apoyo", "tipo_produccion", "superficie_prod", "volumen_prod", "unidad_medida"]
        };

        for (const [titulo, campos] of Object.entries(gruposConfig)) {
            let htmlCard = `
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="card-header py-2 bg-white border-bottom text-guinda fw-bold small">
                            <i class="fas fa-caret-right me-2 text-warning"></i>${titulo.toUpperCase()}
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0" style="font-size:0.75rem;">
                                <tbody>`;
            
            campos.forEach(c => {
                let valor = obtenerDatoFinal(reg, c, json);
                htmlCard += `
                    <tr class="border-bottom-light">
                        <td class="ps-3 text-muted py-2" width="45%">${c.replace(/_/g, ' ').toUpperCase()}</td>
                        <td class="fw-bold py-2 text-dark">${valor || '---'}</td>
                    </tr>`;
            });

            htmlCard += `</tbody></table></div></div></div>`;
            $resumen.append(htmlCard);
        }

        // Siempre resetear a la primera pestaña (Datos Capturados)
        const firstTab = document.querySelector('#tabExpediente li:first-child a');
        if (firstTab) bootstrap.Tab.getOrCreateInstance(firstTab).show();
        
        $("#modalEdicion").modal('show');
    };

    // 7. BUSCADOR GLOBAL (Folio, Nombre o CURP)
    $("#tablaSearch").on("keyup", function() {
        const val = $(this).val().toLowerCase();
        filteredData = rawData.filter(e => 
            (e.folio || "").toLowerCase().includes(val) || 
            (e.nombre || "").toLowerCase().includes(val) ||
            (e.paterno || "").toLowerCase().includes(val) ||
            (e.curp || "").toLowerCase().includes(val)
        );
        renderTable(1);
    });
});

// 8. FUNCIÓN: GUARDAR CAMBIOS (AJAX AL CONTROLADOR CAPTURA)
function confirmarGuardado() {
    const formElement = document.getElementById('formCaptura');
    const formData = new FormData(formElement);
    
    Swal.fire({
        title: '¿Confirmar cambios?',
        text: "Se actualizará la fase del proceso y las observaciones del expediente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#773357',
        confirmButtonText: 'Sí, guardar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar Cargando
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

            fetch('<?php echo URLROOT; ?>/Captura/actualizar', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire('¡Éxito!', data.msg, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.msg, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            });
        }
    });
}

// 9. FUNCIÓN: CERRAR SESIÓN
function confirmarSalida() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#773357',
        confirmButtonText: 'Sí, salir',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo URLROOT; ?>/Auth/logout';
        }
    });
}
</script>