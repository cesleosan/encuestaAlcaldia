<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">

<style>
    :root { 
        --guinda: #773357; 
        --guinda-light: #fdf2f7; 
        --guinda-hover: #5a2642;
        --dorado: #987b47; 
        --gris-fondo: #f4f6f9;
    }

    body { background-color: var(--gris-fondo); font-family: 'Montserrat', sans-serif; }
    
    /* Tarjetas y Sombras */
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 1.5rem; transition: transform 0.2s; }
    .card-header { background-color: white !important; border-bottom: 1px solid var(--guinda-light); padding: 1.25rem; border-radius: 15px 15px 0 0 !important; }
    
    /* Tablas Modernas */
    .table thead th { 
        background-color: var(--guinda) !important; 
        color: white !important; 
        text-transform: uppercase; 
        font-size: 0.7rem; 
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 12px;
        border: none;
    }
    .table-hover tbody tr:hover { background-color: var(--guinda-light) !important; }
    .table td { vertical-align: middle; border-color: #f1f1f1; padding: 12px; }

    /* Sticky Columns para Tabla Detallada */
    .table-sticky-columns { position: relative; }
    #tablaDetalladaFull thead th:nth-child(1), #tablaDetalladaFull thead th:nth-child(2),
    #tablaDetalladaFull tbody td:nth-child(1), #tablaDetalladaFull tbody td:nth-child(2) {
        position: sticky; background-color: white; z-index: 2; border-right: 2px solid #eee;
    }
    #tablaDetalladaFull thead th:nth-child(1), #tablaDetalladaFull thead th:nth-child(2) { 
        z-index: 3; background-color: var(--guinda) !important; 
    }
    #tablaDetalladaFull tbody td:nth-child(1) { left: 0; }
    #tablaDetalladaFull tbody td:nth-child(2) { left: 50px; } /* Ajuste según ancho ID */
    #tablaDetalladaFull thead th:nth-child(1) { left: 0; }
    #tablaDetalladaFull thead th:nth-child(2) { left: 50px; }

    /* Paginación Guinda UI/UX */
    .pagination .page-link { 
        color: var(--guinda); border: none; margin: 0 4px; border-radius: 8px !important; 
        font-weight: 600; transition: 0.3s; padding: 8px 14px;
    }
    .pagination .page-item.active .page-link { 
        background-color: var(--guinda) !important; color: white !important;
        box-shadow: 0 4px 10px rgba(119, 51, 87, 0.3);
    }
    .pagination .page-item.disabled .page-link { background-color: transparent; opacity: 0.5; }

    /* Botones y Badges */
    .btn-guinda { background-color: var(--guinda); color: white; border-radius: 10px; font-weight: 600; padding: 8px 18px; border: none; }
    .btn-guinda:hover { background-color: var(--guinda-hover); color: white; transform: translateY(-1px); }
    .badge-status { border-radius: 50px; padding: 6px 15px; font-weight: 700; text-transform: uppercase; font-size: 0.65rem; }
    
    /* Scrollbars Elegantes */
    .table-responsive::-webkit-scrollbar { height: 8px; width: 8px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: var(--guinda); }

    #mapa-dashboard { height: 450px; border-radius: 15px; z-index: 1; border: 4px solid white; }


    /* --- Módulo UI/UX: Verificación de Campo --- */
    .text-guinda { color: var(--guinda) !important; }
    .bg-guinda-light { background-color: var(--guinda-light) !important; }
    .btn-outline-guinda { border: 1px solid var(--guinda); color: var(--guinda); border-radius: 10px; font-weight: 700; }
    .btn-outline-guinda:hover, .btn-outline-guinda.active { background: var(--guinda); color: #fff; }

    .verification-kpi {
        border-radius: 14px;
        background: #fff;
        border: 1px solid #f1e4ea;
        padding: 14px;
        height: 100%;
        transition: .2s ease;
    }
    .verification-kpi:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(0,0,0,0.08); }
    .verification-kpi .icon-wrap {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: var(--guinda-light); color: var(--guinda);
    }
    .filter-chip {
        border: 1px solid #ead7e0;
        background: #fff;
        color: #666;
        border-radius: 999px;
        padding: 7px 13px;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        transition: .2s ease;
    }
    .filter-chip:hover, .filter-chip.active {
        background: var(--guinda);
        border-color: var(--guinda);
        color: #fff;
        box-shadow: 0 5px 12px rgba(119, 51, 87, 0.25);
    }
    .badge-soft {
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .66rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin: 2px;
    }
    .badge-soft-success { background: #e9f8ef; color: #198754; }
    .badge-soft-warning { background: #fff5db; color: #9a6a00; }
    .badge-soft-danger { background: #fdecee; color: #dc3545; }
    .badge-soft-info { background: #e8f6fb; color: #087990; }
    .badge-soft-secondary { background: #f1f3f5; color: #6c757d; }
    .badge-soft-guinda { background: var(--guinda-light); color: var(--guinda); }

    .comment-clamp {
        max-width: 260px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #6c757d;
        font-size: .74rem;
    }
    .modal-verificacion .modal-content { border-radius: 20px; overflow: hidden; }
    .modal-verificacion .modal-header { background: var(--guinda); color: #fff; border-bottom: none; }
    .detalle-pill {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 12px;
        height: 100%;
    }
    .detalle-pill .label { color: #8a8a8a; font-size: .68rem; font-weight: 800; text-transform: uppercase; }
    .detalle-pill .value { color: #333; font-size: .9rem; font-weight: 800; word-break: break-word; }
    .evidencia-thumb {
        position: relative;
        aspect-ratio: 1/1;
        border-radius: 12px;
        overflow: hidden;
        background: #f5f5f5;
        border: 2px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,.12);
    }
    .evidencia-thumb img { width: 100%; height: 100%; object-fit: cover; }
    #mapa-verificacion { height: 260px; border-radius: 14px; border: 4px solid #fff; z-index: 1; }

</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-7">
            <h2 class="fw-bold text-guinda mb-0">Dashboard: Tierra con Corazón</h2>
        </div>
        <div class="col-md-5 text-end">
            <button id="btnExportar" class="btn btn-outline-secondary me-2">
                <i class="fas fa-file-export"></i> Exportar Excel
            </button>
            <button onclick="location.reload()" class="btn btn-guinda"><i class="fas fa-sync-alt"></i> Sincronizar</button>
            <button onclick="confirmarSalida()" class="btn btn-danger shadow-sm">
                <i class="fas fa-sign-out-alt"></i> Salir
            </button>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-primary">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted small mb-1">ENCUESTAS TOTALES</h6>
                        <h3 class="fw-bold mb-0" id="kpi-total">0</h3>
                    </div>
                    <i class="fas fa-file-signature fa-2x text-light"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted small mb-1">HECTÁREAS TOTALES</h6>
                        <h3 class="fw-bold mb-0" id="kpi-hectareas">0.00</h3>
                    </div>
                    <i class="fas fa-seedling fa-2x text-light"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted small mb-1">TÉCNICOS ACTIVOS</h6>
                        <h3 class="fw-bold mb-0" id="kpi-tecnicos">0</h3>
                    </div>
                    <i class="fas fa-user-check fa-2x text-light"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted small mb-1">AVANCE PROMEDIO</h6>
                        <h3 class="fw-bold mb-0" id="kpi-avance">0</h3>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-light"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-map-marked-alt me-2"></i>Distribución por Coordenadas</h6>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-dashboard"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-guinda">Vocación Productiva</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <canvas id="chartActividades"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-history me-2"></i>Ritmo de Levantamiento Diario</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartTendencia" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-guinda">Problemáticas Detectadas</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartProblemas" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-city me-2"></i>Top 10 Colonias por Superficie Censada</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaColonias">
                            <thead>
                                <tr>
                                    <th class="ps-3">Colonia / Pueblo</th>
                                    <th class="text-center">Número de Productores</th>
                                    <th class="text-center">Hectáreas Totales</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- MÓDULO NUEVO: Verificación de Campo -->
    <div class="row mt-2" id="seccionVerificacionCampo">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-clipboard-check me-2"></i>Verificación de Campo</h6>
                        <small class="text-muted">Seguimiento de coordenadas verificadas, evidencias fotográficas, comentarios y producción detectada.</small>
                    </div>
                    <button id="btnExportarVerificacion" class="btn btn-success btn-sm shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> Descargar Excel Verificación
                    </button>
                </div>

                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-2 col-6">
                            <div class="verification-kpi">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon-wrap"><i class="fas fa-search-location"></i></div>
                                    <div>
                                        <div class="small text-muted fw-bold">EN REVISIÓN</div>
                                        <div class="h4 fw-bold mb-0" id="kpi-verif-revision">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="verification-kpi">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon-wrap"><i class="fas fa-camera"></i></div>
                                    <div>
                                        <div class="small text-muted fw-bold">CON FOTOS</div>
                                        <div class="h4 fw-bold mb-0" id="kpi-verif-fotos">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="verification-kpi">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon-wrap"><i class="fas fa-images"></i></div>
                                    <div>
                                        <div class="small text-muted fw-bold">SIN FOTOS</div>
                                        <div class="h4 fw-bold mb-0" id="kpi-verif-sin-fotos">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="verification-kpi">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon-wrap"><i class="fas fa-map-pin"></i></div>
                                    <div>
                                        <div class="small text-muted fw-bold">CON COORD.</div>
                                        <div class="h4 fw-bold mb-0" id="kpi-verif-coord">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="verification-kpi">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon-wrap"><i class="fas fa-seedling"></i></div>
                                    <div>
                                        <div class="small text-muted fw-bold">PRODUCCIÓN</div>
                                        <div class="h4 fw-bold mb-0" id="kpi-verif-prod">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6">
                            <div class="verification-kpi">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon-wrap"><i class="fas fa-hourglass-half"></i></div>
                                    <div>
                                        <div class="small text-muted fw-bold">PENDIENTES</div>
                                        <div class="h4 fw-bold mb-0" id="kpi-verif-pendientes">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3" id="filtrosVerificacion">
                        <button class="filter-chip active" data-filter="todos">Todos</button>
                        <button class="filter-chip" data-filter="con_fotos">Con fotos</button>
                        <button class="filter-chip" data-filter="sin_fotos">Sin fotos</button>
                        <button class="filter-chip" data-filter="con_coord">Con coordenadas</button>
                        <button class="filter-chip" data-filter="sin_coord">Sin coordenadas</button>
                        <button class="filter-chip" data-filter="maiz">Maíz</button>
                        <button class="filter-chip" data-filter="sorgo">Sorgo</button>
                        <button class="filter-chip" data-filter="frijol">Frijol</button>
                        <button class="filter-chip" data-filter="vacas">Vacas</button>
                        <button class="filter-chip" data-filter="borregos">Borregos</button>
                        <button class="filter-chip" data-filter="conejos">Conejos</button>
                    </div>

                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" id="verificacionSearch" class="form-control" placeholder="Buscar por folio, productor, colonia, comentario o producción...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="filtroFaseVerif" class="form-select form-select-sm">
                                <option value="">Todas las fases</option>
                                <option value="EMPADRONADO">Empadronado</option>
                                <option value="SOLICITUD_INGRESADA">Solicitud ingresada</option>
                                <option value="VALIDACION_DOCS">Validación docs</option>
                                <option value="EN_REVISION">En revisión técnica</option>
                                <option value="APROBADO">Aprobado</option>
                                <option value="RECHAZADO">Rechazado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaVerificacionCampo">
                        <thead>
                            <tr>
                                <th class="ps-3">Folio</th>
                                <th>Productor</th>
                                <th class="text-center">Fase</th>
                                <th class="text-center">Fotos</th>
                                <th class="text-center">Coordenadas</th>
                                <th>Producción detectada</th>
                                <th>Comentarios</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="bodyVerificacionCampo">
                            <tr><td colspan="8" class="text-center text-muted py-4">Cargando verificación de campo...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div class="small text-muted" id="infoVerificacionCampo">Mostrando 0 registros</div>
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Los conteos de fotos se activan al incluir <b>total_fotos</b> desde backend.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-table me-2"></i>Listado Maestro de Encuestas</h6>
                    <input type="text" id="tablaSearch" class="form-control form-control-sm w-25" placeholder="Buscar folio o encuestador...">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tablaEncuestas">
                            <thead>
                                <tr>
                                    <th class="ps-3">Folio</th>
                                    <th>Encuestador</th>
                                    <th>Actividad</th>
                                    <th>Pueblo / Colonia</th>
                                    <th class="text-center">Sup. (ha)</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Estatus</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                        <div class="small text-muted" id="tableInfo">Mostrando 0 de 0 registros</div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

       <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-file-csv me-2"></i>Base de Datos Detallada</h6>
                        <button id="btnExportarFull" class="btn btn-success btn-sm shadow-sm">
                            <i class="fas fa-file-excel me-1"></i> Descargar Excel Completo
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 500px; overflow: both;">
                            <table class="table table-sm table-hover table-bordered align-middle mb-0" id="tablaDetalladaFull" style="font-size: 0.7rem; white-space: nowrap;">
                                <thead class="table-dark">
                                    <tr id="headersCSV">
                                        </tr>
                                </thead>
                                <tbody id="bodyCSV">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NUEVO: Detalle de Verificación de Campo -->
<div class="modal fade modal-verificacion" id="modalVerificacionCampo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-folder-open me-2"></i>Verificación de Campo: <span id="modalVerifFolio">---</span></h5>
                    <small class="opacity-75">Detalle operativo para validar coordenadas, evidencias y comentarios.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="detalle-pill">
                            <div class="label">Productor</div>
                            <div class="value" id="modalVerifProductor">---</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detalle-pill">
                            <div class="label">Fase actual</div>
                            <div class="value" id="modalVerifFase">---</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="detalle-pill">
                            <div class="label">Colonia / Pueblo</div>
                            <div class="value" id="modalVerifColonia">---</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="detalle-pill">
                            <div class="label">Fotos</div>
                            <div class="value" id="modalVerifTotalFotos">---</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-map-marker-alt me-2"></i>Coordenadas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="detalle-pill">
                                            <div class="label">Latitud original</div>
                                            <div class="value" id="modalLatOriginal">---</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detalle-pill">
                                            <div class="label">Longitud original</div>
                                            <div class="value" id="modalLonOriginal">---</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detalle-pill bg-guinda-light">
                                            <div class="label">Latitud verificada</div>
                                            <div class="value text-guinda" id="modalLatVerif">---</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detalle-pill bg-guinda-light">
                                            <div class="label">Longitud verificada</div>
                                            <div class="value text-guinda" id="modalLonVerif">---</div>
                                        </div>
                                    </div>
                                </div>
                                <div id="mapa-verificacion"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-seedling me-2"></i>Producción detectada</h6>
                                <small class="text-muted">Detectado desde campos actuales</small>
                            </div>
                            <div class="card-body">
                                <div id="modalProduccionBadges" class="mb-3"></div>
                                <div class="detalle-pill">
                                    <div class="label">Comentarios / Observaciones</div>
                                    <div class="value fw-normal" id="modalComentarios" style="white-space:pre-wrap; font-size:.85rem;">---</div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-camera me-2"></i>Evidencias fotográficas</h6>
                                <small class="text-muted" id="modalFotosStatus">Consultando...</small>
                            </div>
                            <div class="card-body">
                                <div id="modalFotosEvidencias" class="row g-2">
                                    <div class="col-12 text-center text-muted py-4">Cargando fotos...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between">
                <small class="text-muted"><i class="fas fa-shield-alt me-1"></i>Vista de dashboard: no modifica producción hasta conectar endpoint de cambio de fase.</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger" id="btnRechazarVerificacion"><i class="fas fa-times-circle me-1"></i> Rechazar</button>
                    <button type="button" class="btn btn-guinda" id="btnValidarVerificacion"><i class="fas fa-check-circle me-1"></i> Validar</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    const palette = ['#773357', '#987b47', '#4a1e36', '#2c3e50', '#1cc88a', '#36b9cc', '#f6c23e'];
    
    // Variables de Control
    let fullMaestroData = [];
    let filteredData = [];
    let verificacionData = [];
    let verificacionFiltrada = [];
    let filtroVerificacionActivo = 'todos';
    let registroVerificacionActual = null;
    let mapaVerificacion = null;
    const pageSize = 5;
    const maxRowsVerificacion = 80;
    let currentPage = 1;

    // Configuración exacta de las 23 columnas (CSV)
    const camposCSV = [
        "tecnico_nombre", "curp", "nombre_productor", "sexo", "estado_civil", 
        "ocupacion", "tel_particular", "tel_recados", "email", "cp", 
        "pueblo_colonia", "situacion_unidad", "grado_estudios", "tipo_agua", 
        "financiamiento", "tema_capacitacion", "tipo_apoyo", "tipo_produccion", 
        "superficie_prod", "volumen_prod", "unidad_medida", "fase_proceso"
    ];

    // 1. Inicialización de Mapa
    const map = L.map('mapa-dashboard').setView([19.180, -99.160], 11);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

    // 2. Carga Maestra de Datos
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            
            // A. Llenar KPIs
            $("#kpi-total").text(data.kpis.total_encuestas || 0);
            $("#kpi-hectareas").text(parseFloat(data.kpis.total_hectareas || 0).toFixed(2));
            $("#kpi-tecnicos").text(data.kpis.tecnicos_activos || 0);
            $("#kpi-avance").text(data.tendencia.length > 0 ? Math.round(data.kpis.total_encuestas / data.tendencia.length) : 0);

            // B. Marcadores en Mapa
            if(data.puntos) {
                data.puntos.forEach(p => {
                    if(p.latitud && p.longitud) {
                        L.circleMarker([p.latitud, p.longitud], {
                            radius: 7, fillColor: "#773357", color: "#fff", weight: 2, fillOpacity: 0.9
                        }).addTo(map).bindPopup(`<b>Folio:</b> ${p.folio}<br><b>Actividad:</b> ${p.actividad_principal}`);
                    }
                });
            }

            // C. Renderizar Gráficas
            try { renderCharts(data); } catch(e) { console.error("Error Gráficas:", e); }

            // 🔥 D. TABLA TOP COLONIAS (RECUPERADA)
            const tCol = $("#tablaColonias tbody");
            tCol.empty();
            if(data.colonias) {
                data.colonias.forEach(c => {
                    tCol.append(`
                        <tr>
                            <td class="ps-3 fw-bold text-secondary">${c.colonia_nombre}</td>
                            <td class="text-center">${c.total}</td>
                            <td class="text-center fw-bold text-guunda" style="color:var(--guinda);">${parseFloat(c.hectareas).toFixed(2)} ha</td>
                        </tr>
                    `);
                });
            }

            // E. Inicializar Tablas de Datos
            fullMaestroData = data.maestro || [];
            filteredData = [...fullMaestroData];
            verificacionData = prepararDatosVerificacion(fullMaestroData);
            verificacionFiltrada = [...verificacionData];
            
            renderMasterTable(1); // Tabla de arriba
            renderVerificacionCampo(); // Módulo nuevo de verificación
            renderDetailedTable(fullMaestroData); // Tabla de abajo (JSON)

        }).catch(err => console.error("Error general de carga:", err));


    // --- FUNCIÓN: RENDER TABLA MAESTRA (ARRIBA) ---
    function renderMasterTable(page) {
        currentPage = page;
        const start = (page - 1) * pageSize;
        const items = filteredData.slice(start, start + pageSize);
        const tbody = $("#tablaEncuestas tbody");
        tbody.empty();

        items.forEach(e => {
            tbody.append(`
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-guinda border fw-bold">${e.folio}</span></td>
                    <td class="small fw-bold text-secondary">${e.encuestador || '---'}</td>
                    <td class="small text-uppercase">${e.actividad_principal || 'N/A'}</td>
                    <td class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i>${e.colonia_nombre || 'N/A'}</td>
                    <td class="text-center fw-bold text-guinda">${parseFloat(e.superficie_total || 0).toFixed(2)}</td>
                    <td class="text-center small text-secondary">${(e.fecha_inicio || '').substring(0,10)}</td>
                    <td class="text-center">
                        <span class="badge bg-success badge-status shadow-sm">
                            <i class="fas fa-check me-1"></i> ${e.estatus}
                        </span>
                    </td>
                </tr>
            `);
        });

        $("#tableInfo").html(`Mostrando <b>${items.length}</b> de <b>${filteredData.length}</b> encuestas`);
        renderPaginationUI();
    }

    // --- FUNCIÓN: PAGINACIÓN CON LÓGICA DE VENTANA ---
    function renderPaginationUI() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        const container = $("#paginationControls");
        container.empty();
        if (totalPages <= 1) return;

        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        container.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}"><i class="fas fa-chevron-left"></i></a></li>`);

        for (let i = startPage; i <= endPage; i++) {
            container.append(`<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link shadow-sm" href="#" data-page="${i}">${i}</a></li>`);
        }

        container.append(`<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}"><i class="fas fa-chevron-right"></i></a></li>`);

        container.find('a').on('click', function(e) {
            e.preventDefault();
            const p = parseInt($(this).attr('data-page'));
            if(p >= 1 && p <= totalPages) renderMasterTable(p);
        });
    }

    // --- FUNCIÓN: RENDER TABLA DETALLADA (ABAJO) ---
    function renderDetailedTable(data) {
    const $headerRow = $("#headersCSV");
    const $tbody = $("#bodyCSV");

    // 1. Renderizar Encabezados
    $headerRow.empty().append('<th style="min-width:60px;">ID</th><th style="min-width:150px;">FOLIO</th>');
    camposCSV.forEach(c => $headerRow.append(`<th>${c.replace(/_/g, ' ').toUpperCase()}</th>`));

    // 2. Renderizar Cuerpo de la Tabla
    $tbody.empty();
    data.forEach(reg => {
        try {
            const json = reg.respuestas_json ? JSON.parse(reg.respuestas_json) : {};
            let rowHtml = `<tr>
                <td class="fw-bold text-muted bg-white">${reg.id}</td>
                <td class="fw-bold text-guinda bg-white">${reg.folio}</td>`;
            
            camposCSV.forEach(campo => {
                let valor = '';

                // --- LÓGICA DE PRIORIDAD: COLUMNA FÍSICA VS JSON ---
                
                if (campo === "tecnico_nombre") {
                    // Mapeo manual porque en la BD se llama 'encuestador'
                    valor = reg.encuestador;
                } 
                else if (campo === "nombre_productor") {
                    // Construcción manual de nombre completo desde columnas físicas
                    valor = `${reg.nombre || ''} ${reg.apellido_paterno || ''} ${reg.apellido_materno || ''}`.trim();
                }
                else if (reg[campo] !== undefined && reg[campo] !== null && reg[campo] !== "") {
                    // Si el campo existe como columna real (fase_proceso, curp, sexo, cp, etc.)
                    valor = reg[campo];
                } 
                else {
                    // Si no es una columna física, lo extraemos del JSON
                    valor = extraerValorGlobal(json, campo);
                }

                // Limpieza estética: Si es un ENUM (como fase_proceso), quitamos los guiones bajos
                if (valor && typeof valor === 'string') {
                    valor = valor.replace(/_/g, ' ');
                }

                rowHtml += `<td>${valor || '---'}</td>`;
            });
            
            rowHtml += `</tr>`;
            $tbody.append(rowHtml);

        } catch (e) { 
            console.warn("Error procesando registro ID:", reg.id, e); 
        }
    });
}


    // --- MÓDULO: VERIFICACIÓN DE CAMPO ---
    function prepararDatosVerificacion(data) {
        return (data || []).map(reg => {
            const textoBusqueda = textoNormalizado([
                reg.folio,
                reg.nombre,
                reg.apellido_paterno,
                reg.apellido_materno,
                reg.colonia_nombre,
                reg.actividad_principal,
                reg.especie_cultivo_principal,
                reg.observaciones_capturista,
                reg.respuestas_json,
                reg.linea_ayuda
            ].join(' '));

            const produccion = detectarProduccion(textoBusqueda);
            const totalFotos = obtenerTotalFotos(reg);
            const tieneCoord = coordenadaValida(reg.latitud_verif, reg.longitud_verif);
            const productor = nombreProductor(reg);
            const comentarios = obtenerComentarios(reg);

            return {
                ...reg,
                productor,
                comentarios_verificacion: comentarios,
                total_fotos_calculado: totalFotos,
                tiene_fotos_calculado: totalFotos !== null ? totalFotos > 0 : false,
                fotos_sin_conteo_backend: totalFotos === null,
                tiene_coord_verificada: tieneCoord,
                produccion_detectada: produccion,
                texto_busqueda: textoNormalizado([
                    reg.folio,
                    productor,
                    reg.encuestador,
                    reg.colonia_nombre,
                    reg.fase_proceso,
                    comentarios,
                    produccion.join(' ')
                ].join(' '))
            };
        });
    }

    function nombreProductor(reg) {
        return `${reg.nombre || ''} ${reg.apellido_paterno || ''} ${reg.apellido_materno || ''}`.replace(/\s+/g, ' ').trim() || '---';
    }

    function obtenerComentarios(reg) {
        const obs = (reg.observaciones_capturista || '').trim();
        if (obs) return obs;

        try {
            const json = reg.respuestas_json ? JSON.parse(reg.respuestas_json) : null;
            const posibles = ['comentarios', 'observaciones', 'observaciones_capturista', 'comentario', 'comentarios_verificacion'];
            for (const campo of posibles) {
                const valor = extraerValorGlobal(json, campo);
                if (valor) return valor;
            }
        } catch(e) {}

        return 'Sin comentarios capturados';
    }

    function obtenerTotalFotos(reg) {
        const posibles = [reg.total_fotos, reg.fotos_total, reg.num_fotos, reg.evidencias_total];
        for (const val of posibles) {
            if (val !== undefined && val !== null && val !== '') {
                const n = parseInt(val, 10);
                return isNaN(n) ? 0 : n;
            }
        }
        return null; // Aún no viene desde backend
    }

    function coordenadaValida(lat, lon) {
        const la = parseFloat(lat);
        const lo = parseFloat(lon);
        return !isNaN(la) && !isNaN(lo) && la !== 0 && lo !== 0;
    }

    function textoNormalizado(str) {
        return (str || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/_/g, ' ');
    }

    function detectarProduccion(texto) {
        const items = [];
        const reglas = [
            { key: 'maiz', label: 'Maíz', icon: 'fa-wheat-awn', re: /\b(maiz|elote|milpa)\b/ },
            { key: 'sorgo', label: 'Sorgo', icon: 'fa-seedling', re: /\bsorgo\b/ },
            { key: 'frijol', label: 'Frijol', icon: 'fa-seedling', re: /\b(frijol|frijoles)\b/ },
            { key: 'vacas', label: 'Vacas', icon: 'fa-cow', re: /\b(vaca|vacas|bovino|bovinos|res|ganado)\b/ },
            { key: 'borregos', label: 'Borregos', icon: 'fa-horse', re: /\b(borrego|borregos|ovino|ovinos|oveja|ovejas)\b/ },
            { key: 'conejos', label: 'Conejos', icon: 'fa-paw', re: /\b(conejo|conejos)\b/ }
        ];

        reglas.forEach(r => { if (r.re.test(texto)) items.push(r); });
        return items;
    }

    function badgeFase(fase) {
        const f = (fase || 'SIN_FASE').toString();
        const label = f.replace(/_/g, ' ');
        const classes = {
            'EMPADRONADO': 'badge-soft-secondary',
            'SOLICITUD_INGRESADA': 'badge-soft-info',
            'VALIDACION_DOCS': 'badge-soft-info',
            'EN_REVISION': 'badge-soft-warning',
            'APROBADO': 'badge-soft-success',
            'RECHAZADO': 'badge-soft-danger'
        };
        return `<span class="badge-soft ${classes[f] || 'badge-soft-secondary'}">${label}</span>`;
    }

    function badgesProduccion(items) {
        if (!items || items.length === 0) return '<span class="badge-soft badge-soft-secondary">Sin detectar</span>';
        return items.map(i => `<span class="badge-soft badge-soft-guinda"><i class="fas ${i.icon}"></i>${i.label}</span>`).join('');
    }

    function renderKPIsVerificacion() {
        const base = verificacionData;
        const enRevision = base.filter(e => e.fase_proceso === 'EN_REVISION').length;
        const conFotos = base.filter(e => e.total_fotos_calculado !== null && e.total_fotos_calculado > 0).length;
        const sinFotos = base.filter(e => e.total_fotos_calculado !== null && e.total_fotos_calculado === 0).length;
        const conCoord = base.filter(e => e.tiene_coord_verificada).length;
        const conProd = base.filter(e => e.produccion_detectada.length > 0).length;
        const pendientes = base.filter(e => !e.tiene_coord_verificada || e.total_fotos_calculado === 0 || e.total_fotos_calculado === null).length;

        $('#kpi-verif-revision').text(enRevision);
        $('#kpi-verif-fotos').text(conFotos);
        $('#kpi-verif-sin-fotos').text(sinFotos);
        $('#kpi-verif-coord').text(conCoord);
        $('#kpi-verif-prod').text(conProd);
        $('#kpi-verif-pendientes').text(pendientes);
    }

    function aplicarFiltrosVerificacion() {
        const q = textoNormalizado($('#verificacionSearch').val());
        const fase = $('#filtroFaseVerif').val();

        verificacionFiltrada = verificacionData.filter(e => {
            const pasaTexto = !q || e.texto_busqueda.includes(q);
            const pasaFase = !fase || e.fase_proceso === fase;
            let pasaChip = true;

            switch (filtroVerificacionActivo) {
                case 'con_fotos': pasaChip = e.total_fotos_calculado !== null && e.total_fotos_calculado > 0; break;
                case 'sin_fotos': pasaChip = e.total_fotos_calculado === 0; break;
                case 'con_coord': pasaChip = e.tiene_coord_verificada; break;
                case 'sin_coord': pasaChip = !e.tiene_coord_verificada; break;
                case 'maiz': pasaChip = e.produccion_detectada.some(i => i.key === 'maiz'); break;
                case 'sorgo': pasaChip = e.produccion_detectada.some(i => i.key === 'sorgo'); break;
                case 'frijol': pasaChip = e.produccion_detectada.some(i => i.key === 'frijol'); break;
                case 'vacas': pasaChip = e.produccion_detectada.some(i => i.key === 'vacas'); break;
                case 'borregos': pasaChip = e.produccion_detectada.some(i => i.key === 'borregos'); break;
                case 'conejos': pasaChip = e.produccion_detectada.some(i => i.key === 'conejos'); break;
                default: pasaChip = true;
            }

            return pasaTexto && pasaFase && pasaChip;
        });

        renderVerificacionCampo();
    }

    function renderVerificacionCampo() {
        renderKPIsVerificacion();
        const tbody = $('#bodyVerificacionCampo');
        tbody.empty();

        if (!verificacionFiltrada.length) {
            tbody.append('<tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-filter me-1"></i>No hay registros con los filtros seleccionados.</td></tr>');
            $('#infoVerificacionCampo').text('Mostrando 0 registros');
            return;
        }

        const items = verificacionFiltrada.slice(0, maxRowsVerificacion);
        items.forEach(e => {
            const fotosBadge = e.total_fotos_calculado === null
                ? '<span class="badge-soft badge-soft-secondary"><i class="fas fa-question-circle"></i>Sin conteo</span>'
                : (e.total_fotos_calculado > 0
                    ? `<span class="badge-soft badge-soft-success"><i class="fas fa-camera"></i>${e.total_fotos_calculado} fotos</span>`
                    : '<span class="badge-soft badge-soft-danger"><i class="fas fa-camera"></i>Sin fotos</span>');

            const coordBadge = e.tiene_coord_verificada
                ? '<span class="badge-soft badge-soft-success"><i class="fas fa-map-pin"></i>Verificada</span>'
                : '<span class="badge-soft badge-soft-warning"><i class="fas fa-location-dot"></i>Pendiente</span>';

            tbody.append(`
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-guinda border fw-bold">${e.folio || '---'}</span></td>
                    <td>
                        <div class="fw-bold text-secondary">${e.productor}</div>
                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${e.colonia_nombre || 'N/A'}</small>
                    </td>
                    <td class="text-center">${badgeFase(e.fase_proceso)}</td>
                    <td class="text-center">${fotosBadge}</td>
                    <td class="text-center">${coordBadge}</td>
                    <td>${badgesProduccion(e.produccion_detectada)}</td>
                    <td><div class="comment-clamp">${e.comentarios_verificacion || 'Sin comentarios'}</div></td>
                    <td class="text-center">
                        <button class="btn btn-outline-guinda btn-sm btn-ver-detalle" data-id="${e.id}">
                            <i class="fas fa-eye me-1"></i> Ver
                        </button>
                    </td>
                </tr>
            `);
        });

        const extra = verificacionFiltrada.length > maxRowsVerificacion ? ` · Se muestran los primeros ${maxRowsVerificacion}` : '';
        $('#infoVerificacionCampo').html(`Mostrando <b>${items.length}</b> de <b>${verificacionFiltrada.length}</b> registros${extra}`);
    }

    function abrirModalVerificacion(id) {
        const reg = verificacionData.find(e => String(e.id) === String(id));
        if (!reg) return;
        registroVerificacionActual = reg;

        $('#modalVerifFolio').text(reg.folio || '---');
        $('#modalVerifProductor').text(reg.productor || '---');
        $('#modalVerifFase').html(badgeFase(reg.fase_proceso));
        $('#modalVerifColonia').text(reg.colonia_nombre || 'N/A');
        $('#modalVerifTotalFotos').text(reg.total_fotos_calculado === null ? 'Consultar' : reg.total_fotos_calculado);
        $('#modalLatOriginal').text(reg.latitud || '---');
        $('#modalLonOriginal').text(reg.longitud || '---');
        $('#modalLatVerif').text(reg.latitud_verif || '---');
        $('#modalLonVerif').text(reg.longitud_verif || '---');
        $('#modalProduccionBadges').html(badgesProduccion(reg.produccion_detectada));
        $('#modalComentarios').text(reg.comentarios_verificacion || 'Sin comentarios capturados');

        cargarFotosModal(reg.id);
        $('#modalVerificacionCampo').modal('show');
    }

    function cargarFotosModal(id) {
        $('#modalFotosStatus').text('Consultando...');
        $('#modalFotosEvidencias').html('<div class="col-12 text-center text-muted py-4"><i class="fas fa-spinner fa-spin me-2"></i>Cargando evidencias...</div>');

        fetch(`<?php echo URLROOT; ?>/Captura/getFotosEvidencia/${id}`)
            .then(res => res.json())
            .then(fotos => {
                const cont = $('#modalFotosEvidencias');
                cont.empty();

                if (!Array.isArray(fotos) || fotos.length === 0) {
                    $('#modalFotosStatus').text('Sin fotos');
                    cont.html(`
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-images fa-3x opacity-25 mb-2"></i>
                            <p class="small mb-0">Este registro no tiene evidencias fotográficas cargadas.</p>
                        </div>
                    `);
                    return;
                }

                $('#modalFotosStatus').text(`${fotos.length} foto(s)`);
                $('#modalVerifTotalFotos').text(fotos.length);
                fotos.forEach(f => {
                    cont.append(`
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="${f.url}" target="_blank" class="d-block evidencia-thumb" title="Abrir evidencia">
                                <img src="${f.url}" alt="Evidencia de verificación">
                            </a>
                        </div>
                    `);
                });
            })
            .catch(() => {
                $('#modalFotosStatus').text('Error');
                $('#modalFotosEvidencias').html('<div class="col-12 text-center text-danger py-4">No se pudieron cargar las evidencias.</div>');
            });
    }

    function renderMapaVerificacion() {
        const reg = registroVerificacionActual;
        if (!reg) return;

        if (mapaVerificacion) {
            mapaVerificacion.remove();
            mapaVerificacion = null;
        }

        mapaVerificacion = L.map('mapa-verificacion');
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapaVerificacion);

        const puntos = [];
        if (coordenadaValida(reg.latitud, reg.longitud)) {
            puntos.push({ lat: parseFloat(reg.latitud), lon: parseFloat(reg.longitud), label: 'Coordenada original', color: '#987b47' });
        }
        if (coordenadaValida(reg.latitud_verif, reg.longitud_verif)) {
            puntos.push({ lat: parseFloat(reg.latitud_verif), lon: parseFloat(reg.longitud_verif), label: 'Coordenada verificada', color: '#773357' });
        }

        if (!puntos.length) {
            mapaVerificacion.setView([19.180, -99.160], 11);
            return;
        }

        puntos.forEach(p => {
            L.circleMarker([p.lat, p.lon], {
                radius: 8, fillColor: p.color, color: '#fff', weight: 2, fillOpacity: .95
            }).addTo(mapaVerificacion).bindPopup(`<b>${p.label}</b><br>${p.lat}, ${p.lon}`);
        });

        if (puntos.length === 1) {
            mapaVerificacion.setView([puntos[0].lat, puntos[0].lon], 15);
        } else {
            const bounds = L.latLngBounds(puntos.map(p => [p.lat, p.lon]));
            mapaVerificacion.fitBounds(bounds, { padding: [30, 30] });
        }
    }

    $('#modalVerificacionCampo').on('shown.bs.modal', function() {
        renderMapaVerificacion();
    });

    $('#filtrosVerificacion').on('click', '.filter-chip', function() {
        $('#filtrosVerificacion .filter-chip').removeClass('active');
        $(this).addClass('active');
        filtroVerificacionActivo = $(this).data('filter');
        aplicarFiltrosVerificacion();
    });

    $('#verificacionSearch, #filtroFaseVerif').on('keyup change', aplicarFiltrosVerificacion);

    $('#bodyVerificacionCampo').on('click', '.btn-ver-detalle', function() {
        abrirModalVerificacion($(this).data('id'));
    });

    $('#btnValidarVerificacion, #btnRechazarVerificacion').on('click', function() {
        const accion = this.id === 'btnValidarVerificacion' ? 'validar' : 'rechazar';
        Swal.fire({
            icon: 'info',
            title: `Acción preparada para ${accion}`,
            text: 'El frontend ya está listo. Para modificar producción de forma segura falta conectar un endpoint backend específico de cambio de fase.',
            confirmButtonColor: '#773357'
        });
    });

    $('#btnExportarVerificacion').on('click', function() {
        const headers = [
            'Folio', 'Productor', 'CURP', 'Telefono', 'Colonia', 'Fase',
            'Actividad principal', 'Especie/Cultivo principal', 'Cabezas/Colmenas',
            'Latitud original', 'Longitud original', 'Latitud verificada', 'Longitud verificada',
            'Coordenada verificada', 'Total fotos', 'Maiz', 'Sorgo', 'Frijol', 'Vacas', 'Borregos', 'Conejos', 'Comentarios'
        ];
        const rows = verificacionFiltrada.map(e => {
            const tiene = key => e.produccion_detectada.some(i => i.key === key) ? 'SI' : 'NO';
            return [
                e.folio, e.productor, e.curp, e.tel_particular, e.colonia_nombre, e.fase_proceso,
                e.actividad_principal, e.especie_cultivo_principal, e.numero_cabezas_colmenas,
                e.latitud, e.longitud, e.latitud_verif, e.longitud_verif,
                e.tiene_coord_verificada ? 'SI' : 'NO',
                e.total_fotos_calculado === null ? 'SIN_CONTEO_BACKEND' : e.total_fotos_calculado,
                tiene('maiz'), tiene('sorgo'), tiene('frijol'), tiene('vacas'), tiene('borregos'), tiene('conejos'),
                e.comentarios_verificacion
            ];
        });
        const csv = "\uFEFF" + headers.join(',') + "\n" + rows.map(r => r.map(v => `"${(v ?? '').toString().replace(/"/g, '""').replace(/,/g, ';').trim()}"`).join(',')).join("\n");
        descargarCSV(csv, 'Verificacion_Campo');
    });


    // Buscador Global
    $("#tablaSearch").on("keyup", function() {
        const val = $(this).val().toLowerCase();
        filteredData = fullMaestroData.filter(e => 
            (e.folio || "").toLowerCase().includes(val) || 
            (e.encuestador || "").toLowerCase().includes(val) ||
            (e.colonia_nombre || "").toLowerCase().includes(val)
        );
        renderMasterTable(1);
    });

    // Helper: Extracción de JSON multi-sección
    function extraerValorGlobal(json, campoBuscado) {
        if (!json) return '';
        if (json["6"] && json["6"][campoBuscado]) return json["6"][campoBuscado];
        for (let sec in json) {
            if (Array.isArray(json[sec])) {
                const matches = json[sec].filter(i => i.name === campoBuscado || i.name === campoBuscado + '[]');
                if (matches.length > 0) return matches.map(m => m.value).join('; ');
            } else if (typeof json[sec] === 'string' && sec === campoBuscado) {
                return json[sec];
            }
        }
        return '';
    }

    // Generador de Gráficas
    function renderCharts(data) {
        new Chart(document.getElementById('chartActividades'), {
            type: 'doughnut',
            data: {
                labels: data.actividades.map(a => a.actividad_principal),
                datasets: [{ data: data.actividades.map(a => a.total), backgroundColor: palette }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }, cutout: '70%' }
        });
        new Chart(document.getElementById('chartTendencia'), {
            type: 'line',
            data: {
                labels: data.tendencia.map(t => t.fecha),
                datasets: [{ label: 'Encuestas', data: data.tendencia.map(t => t.total), borderColor: '#773357', backgroundColor: 'rgba(119, 51, 87, 0.1)', fill: true, tension: 0.4 }]
            },
            options: { maintainAspectRatio: false }
        });
        new Chart(document.getElementById('chartProblemas'), {
            type: 'bar',
            data: {
                labels: data.problemas.map(p => p.problema || 'N/A'),
                datasets: [{ label: 'Casos', data: data.problemas.map(p => p.total), backgroundColor: '#987b47', borderRadius: 5 }]
            },
            options: { indexAxis: 'y', maintainAspectRatio: false }
        });
    }

    // Exportación a Excel
    $("#btnExportar").on("click", function() {
        const headers = ["Folio", "Encuestador", "Actividad", "Colonia", "Superficie", "Fecha", "Estatus"];
        const rows = fullMaestroData.map(e => [e.folio, e.encuestador, e.actividad_principal, e.colonia_nombre, e.superficie_total, e.fecha_inicio, e.estatus]);
        descargarCSV("\uFEFF" + headers.join(",") + "\n" + rows.map(r => r.map(v => `"${v}"`).join(",")).join("\n"), "Censo_Resumen");
    });

    $("#btnExportarFull").on("click", function() {
        let csv = "\uFEFFID,FOLIO," + camposCSV.map(c => c.toUpperCase()).join(",") + "\n";
        $("#bodyCSV tr").each(function() {
            let fila = [];
            $(this).find("td").each(function() {
                fila.push(`"${$(this).text().replace(/"/g, '""').replace(/,/g, ';').trim()}"`);
            });
            csv += fila.join(",") + "\n";
        });
        descargarCSV(csv, "Censo_Detallado_Full");
    });

    function descargarCSV(contenido, nombre) {
        const blob = new Blob([contenido], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `${nombre}_${new Date().toISOString().slice(0,10)}.csv`;
        link.click();
    }
});

function confirmarSalida() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: "Regresa pronto para continuar el censo.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#773357',
        confirmButtonText: 'Sí, salir',
        reverseButtons: true
    }).then((result) => { if (result.isConfirmed) window.location.href = '<?php echo URLROOT; ?>/Auth/logout'; });
}
</script>