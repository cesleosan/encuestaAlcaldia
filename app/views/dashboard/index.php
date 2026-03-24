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
    const pageSize = 5;
    let currentPage = 1;

    // Configuración exacta de las 23 columnas (CSV)
    const camposCSV = [
        "tecnico_nombre", "curp", "nombre_productor", "sexo", "estado_civil", 
        "ocupacion", "tel_particular", "tel_recados", "email", "cp", 
        "pueblo_colonia", "situacion_unidad", "grado_estudios", "tipo_agua", 
        "financiamiento", "tema_capacitacion", "tipo_apoyo", "tipo_produccion", 
        "superficie_prod", "volumen_prod", "unidad_medida"
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
            
            renderMasterTable(1); // Tabla de arriba
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

        $headerRow.empty().append('<th style="min-width:60px;">ID</th><th style="min-width:150px;">FOLIO</th>');
        camposCSV.forEach(c => $headerRow.append(`<th>${c.replace(/_/g, ' ').toUpperCase()}</th>`));

        $tbody.empty();
        data.forEach(reg => {
            try {
                const json = reg.respuestas_json ? JSON.parse(reg.respuestas_json) : {};
                let rowHtml = `<tr><td class="fw-bold text-muted bg-white">${reg.id}</td><td class="fw-bold text-guinda bg-white">${reg.folio}</td>`;
                
                camposCSV.forEach(campo => {
                    let valor = extraerValorGlobal(json, campo);
                    rowHtml += `<td>${valor || '---'}</td>`;
                });
                
                rowHtml += `</tr>`;
                $tbody.append(rowHtml);
            } catch (e) { console.warn("Error JSON en ID:", reg.id); }
        });
    }

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