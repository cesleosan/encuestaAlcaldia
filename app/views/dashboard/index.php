<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">

<style>
    :root { --guinda: #773357; --guinda-light: #fdf2f7; --dorado: #987b47; }
    body { background-color: #f4f6f9; font-family: 'Montserrat', sans-serif; }
    .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
    .text-guinda { color: var(--guinda); }
    .bg-guinda { background-color: var(--guinda); }
    .btn-guinda { background-color: var(--guinda); color: white; border-radius: 10px; font-weight: 600; }
    .btn-guinda:hover { background-color: #5a2642; color: white; }
    #mapa-dashboard { height: 450px; border-radius: 15px; z-index: 1; }
    
    .table thead th { background-color: #f8f9fa; color: var(--guinda); border-bottom: 2px solid #dee2e6; text-transform: uppercase; font-size: 0.7rem; }
    .badge-status { border-radius: 20px; padding: 5px 12px; font-size: 0.7rem; }
    
    /* Paginación Estilo Guinda */
    .pagination .page-link { color: var(--guinda); }
    .pagination .page-item.active .page-link { background-color: var(--guinda); border-color: var(--guinda); }
    /* Añadir a tu bloque <style> */
    .btn-danger {
        background-color: #a02020; /* Un rojo más serio */
        border: none;
        border-radius: 10px;
        font-weight: 600;
    }
    .btn-danger:hover {
        background-color: #7a1818;
    }
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
                        <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-file-csv me-2"></i>Base de Datos Detallada (JSON Aplanado)</h6>
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
    
    // Variables para Paginación
    let fullMaestroData = [];
    let filteredData = [];
    const pageSize = 5;
    let currentPage = 1;

    // --- CONFIGURACIÓN DE COLUMNAS PARA REPORTE DETALLADO (CSV) ---
    const camposCSV = [
        "tecnico_nombre", "folio", "curp", "nombre_productor", "fecha_nacimiento", "sexo", "tiempo_residencia",
        "grupo_etnico", "estado_civil", "ocupacion", "tel_particular", "tel_recados", "email", "tiempo_residencia_cdmx",
        "cp", "pueblo_colonia", "latitud", "longitud", "calle_numero", "situacion_unidad", "grado_estudios",
        "dependientes_economicos", "servicios_salud", "material_pisos", "combustible_cocina", "bienes_vivienda",
        "tipo_agua", "insumos_agricolas", "maquinaria", "problema_principal", "ingreso_mensual", "dependencia_economica",
        "destino_produccion", "financiamiento", "dificultades_comercializacion", "participacion_mujeres",
        "nuevas_generaciones", "tema_capacitacion", "tipo_apoyo", "carta_finiquito", "tipo_produccion",
        "tipo_granja", "especies_granja", "alimentacion_granja", "productos_granja", "destino_granja",
        "necesidades_granja", "superficie_prod", "volumen_prod", "unidad_medida", "capacitaciones_deseadas", "observaciones"
    ];

    // 1. Mapa
    const map = L.map('mapa-dashboard').setView([19.180, -99.160], 11);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

    // 2. Fetch de Datos
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            
            // A. KPIs
            $("#kpi-total").text(data.kpis.total_encuestas || 0);
            $("#kpi-hectareas").text(parseFloat(data.kpis.total_hectareas || 0).toFixed(2));
            $("#kpi-tecnicos").text(data.kpis.tecnicos_activos || 0);
            $("#kpi-avance").text(Math.round((data.kpis.total_encuestas || 0) / (data.tendencia.length || 1)));

            // B. Mapa
            data.puntos.forEach(p => {
                if(p.latitud && p.longitud) {
                    L.circleMarker([p.latitud, p.longitud], {
                        radius: 6, fillColor: "#773357", color: "#fff", weight: 1, opacity: 1, fillOpacity: 0.8
                    }).addTo(map).bindPopup(`<b>Folio:</b> ${p.folio}<br><b>Actividad:</b> ${p.actividad_principal}`);
                }
            });

            // C. Gráfica Actividades
            new Chart(document.getElementById('chartActividades'), {
                type: 'doughnut',
                data: {
                    labels: data.actividades.map(a => a.actividad_principal),
                    datasets: [{ data: data.actividades.map(a => a.total), backgroundColor: palette }]
                },
                options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
            });

            // D. Gráfica Tendencia
            new Chart(document.getElementById('chartTendencia'), {
                type: 'line',
                data: {
                    labels: data.tendencia.map(t => t.fecha),
                    datasets: [{
                        label: 'Encuestas',
                        data: data.tendencia.map(t => t.total),
                        borderColor: '#773357',
                        backgroundColor: 'rgba(119, 51, 87, 0.1)',
                        fill: true, tension: 0.4
                    }]
                },
                options: { maintainAspectRatio: false }
            });

            // E. Gráfica Problemas
            new Chart(document.getElementById('chartProblemas'), {
                type: 'bar',
                data: {
                    labels: data.problemas.map(p => p.problema || 'N/A'),
                    datasets: [{
                        label: 'Reportes',
                        data: data.problemas.map(p => p.total),
                        backgroundColor: '#987b47'
                    }]
                },
                options: { indexAxis: 'y', maintainAspectRatio: false }
            });

            // F. Tabla Colonias
            const tCol = $("#tablaColonias tbody");
            data.colonias.forEach(c => {
                tCol.append(`
                    <tr>
                        <td class="ps-3 fw-bold">${c.colonia_nombre}</td>
                        <td class="text-center">${c.total}</td>
                        <td class="text-center fw-bold text-guinda">${parseFloat(c.hectareas).toFixed(2)} ha</td>
                    </tr>
                `);
            });

            // G. Listado Maestro con Paginación
            fullMaestroData = data.maestro;
            filteredData = [...fullMaestroData];
            renderTable(1);

            // 🔥 H. Llenar Nueva Tabla Detallada (JSON Aplanado)
            renderTablaDetalladaJSON(fullMaestroData);

        });

    // --- FUNCIÓN PARA EXTRAER VALORES DEL JSON (MULTI-SECCIÓN) ---
    function extraerValorGlobal(json, campoBuscado) {
        // Caso especial pantalla 6 (Coordenadas son objeto directo)
        if (json["6"] && json["6"][campoBuscado]) return json["6"][campoBuscado];

        for (let sec in json) {
            if (Array.isArray(json[sec])) {
                // Filtramos por nombre exacto o nombre con corchetes []
                const matches = json[sec].filter(i => i.name === campoBuscado || i.name === campoBuscado + '[]');
                if (matches.length > 0) {
                    // Si hay varios (checkboxes), los unimos con punto y coma
                    return matches.map(m => m.value).join('; ');
                }
            } else if (typeof json[sec] === 'string' && sec === campoBuscado) {
                return json[sec];
            }
        }
        return '';
    }

    // --- FUNCIÓN RENDER TABLA DETALLADA ---
    function renderTablaDetalladaJSON(data) {
        const $headerRow = $("#headersCSV");
        const $tbody = $("#bodyCSV");

        // Cabeceras
        $headerRow.empty().append('<th>ID_BD</th>');
        camposCSV.forEach(c => $headerRow.append(`<th>${c.replace(/_/g, ' ').toUpperCase()}</th>`));

        // Filas
        $tbody.empty();
        data.forEach(reg => {
            try {
                const json = JSON.parse(reg.respuestas_json);
                let rowHtml = `<tr><td>${reg.id}</td>`;
                camposCSV.forEach(campo => {
                    let valor = extraerValorGlobal(json, campo);
                    rowHtml += `<td>${valor || ''}</td>`;
                });
                rowHtml += `</tr>`;
                $tbody.append(rowHtml);
            } catch (e) { console.error("Error parseando JSON ID: " + reg.id); }
        });
    }

    // Función para Renderizar Tabla con Paginación (Original)
    function renderTable(page) {
        currentPage = page;
        const start = (page - 1) * pageSize;
        const end = start + pageSize;
        const items = filteredData.slice(start, end);
        
        const tbody = $("#tablaEncuestas tbody");
        tbody.empty();

        items.forEach(e => {
            tbody.append(`
                <tr>
                    <td class="ps-3 fw-bold text-guinda">${e.folio}</td>
                    <td class="small">${e.encuestador || 'Sin asignar'}</td>
                    <td class="small text-uppercase">${e.actividad_principal}</td>
                    <td class="small text-muted">${e.colonia_nombre || 'N/A'}</td>
                    <td class="text-center font-monospace">${parseFloat(e.superficie_total).toFixed(2)}</td>
                    <td class="text-center small">${e.fecha_inicio.substring(0,10)}</td>
                    <td class="text-center"><span class="badge bg-success badge-status">${e.estatus}</span></td>
                </tr>
            `);
        });

        $("#tableInfo").text(`Mostrando ${items.length} de ${filteredData.length} registros`);
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / pageSize);
        const container = $("#paginationControls");
        container.empty();

        if(totalPages <= 1) return;

        for(let i = 1; i <= totalPages; i++) {
            container.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="event.preventDefault();">${i}</a>
                </li>
            `);
        }

        container.find('a').on('click', function() {
            renderTable(parseInt($(this).text()));
        });
    }

    // Buscador (Original)
    $("#tablaSearch").on("keyup", function() {
        const val = $(this).val().toLowerCase();
        filteredData = fullMaestroData.filter(e => 
            e.folio.toLowerCase().includes(val) || 
            (e.encuestador && e.encuestador.toLowerCase().includes(val)) ||
            (e.colonia_nombre && e.colonia_nombre.toLowerCase().includes(val))
        );
        renderTable(1);
    });

    // --- EXPORTACIÓN LISTA RÁPIDA (Original) ---
    $("#btnExportar").on("click", function() {
        if (fullMaestroData.length === 0) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'No hay datos.', confirmButtonColor: '#773357' });
            return;
        }
        const headers = ["Folio", "Encuestador", "Actividad", "Colonia", "Superficie (ha)", "Fecha", "Estatus"];
        const rows = fullMaestroData.map(e => [e.folio, e.encuestador || "Sin asignar", e.actividad_principal, e.colonia_nombre || "N/A", parseFloat(e.superficie_total).toFixed(2), e.fecha_inicio, e.estatus]);
        
        let csvContent = "\uFEFF" + headers.join(",") + "\n";
        rows.forEach(r => { csvContent += r.map(val => `"${val}"`).join(",") + "\n"; });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `Censo_Tlalpan_Resumen_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        Swal.fire({ title: '¡Éxito!', text: 'Reporte resumido generado.', icon: 'success', timer: 2000, showConfirmButton: false });
    });

    // --- 🔥 NUEVA EXPORTACIÓN FULL (CSV DETALLADO) ---
    $("#btnExportarFull").on("click", function() {
        if (fullMaestroData.length === 0) return;

        let csv = "\uFEFF"; // BOM para acentos en Excel
        let headers = ["ID_BD", ...camposCSV.map(c => c.toUpperCase())];
        csv += headers.join(",") + "\n";

        $("#bodyCSV tr").each(function() {
            let fila = [];
            $(this).find("td").each(function() {
                // Limpieza de comas y comillas para no romper el CSV
                let texto = $(this).text().replace(/"/g, '""').replace(/,/g, ';').trim();
                fila.push(`"${texto}"`);
            });
            csv += fila.join(",") + "\n";
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `Censo_Detallado_Full_${new Date().toISOString().slice(0,10)}.csv`;
        link.click();
        
        Swal.fire({ title: '¡Descarga Completa!', text: 'Se ha generado el archivo con todos los datos del JSON.', icon: 'success', timer: 2500, showConfirmButton: false });
    });

});

function confirmarSalida() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: "Tendrás que ingresar tus credenciales nuevamente.",
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