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
    .table thead th { background-color: #f8f9fa; color: var(--guinda); border-bottom: 2px solid #dee2e6; text-transform: uppercase; font-size: 0.75rem; }
    .badge-status { border-radius: 20px; padding: 5px 12px; font-size: 0.7rem; }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-7">
            <h2 class="fw-bold text-guinda mb-0">Command Center: Tierra con Corazón</h2>
            <p class="text-muted">Análisis integral del Censo Agropecuario Tlalpan 2026</p>
        </div>
        <div class="col-md-5 text-end">
            <button class="btn btn-outline-secondary me-2"><i class="fas fa-file-export"></i> Exportar</button>
            <button onclick="location.reload()" class="btn btn-guinda"><i class="fas fa-sync-alt"></i> Sincronizar</button>
        </div>
    </div>

    <div class="row">
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
                        <h6 class="text-muted small mb-1">AVANCE DIARIO (PROMEDIO)</h6>
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
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-map-marked-alt me-2"></i>Distribución de Levantamientos por Coordenadas</h6>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-dashboard"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 text-center">
                    <h6 class="m-0 fw-bold text-guinda">Vocación Productiva</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <canvas id="chartActividades"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-history me-2"></i>Ritmo de Levantamiento (Histórico Diario)</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartTendencia" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-guinda"><i class="fas fa-table me-2"></i>Listado Maestro de Encuestas (Sin Datos Sensibles)</h6>
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
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    const palette = ['#773357', '#987b47', '#4a1e36', '#2c3e50', '#1cc88a', '#36b9cc', '#f6c23e'];

    // 1. Mapa
    const map = L.map('mapa-dashboard').setView([19.180, -99.160], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

    // 2. Obtener Datos
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            
            // KPIs
            $("#kpi-total").text(data.kpis.total_encuestas || 0);
            $("#kpi-hectareas").text(parseFloat(data.kpis.total_hectareas || 0).toFixed(2));
            $("#kpi-tecnicos").text(data.kpis.tecnicos_activos || 0);

            // Mapa
            data.puntos.forEach(p => {
                if(p.latitud && p.longitud) {
                    L.circleMarker([p.latitud, p.longitud], {
                        radius: 6, fillColor: "#773357", color: "#fff", weight: 1, opacity: 1, fillOpacity: 0.8
                    }).addTo(map).bindPopup(`<b>Folio:</b> ${p.folio}<br><b>Actividad:</b> ${p.actividad_principal}`);
                }
            });

            // Gráfica Doughnut (Actividades)
            new Chart(document.getElementById('chartActividades'), {
                type: 'doughnut',
                data: {
                    labels: data.actividades.map(a => a.actividad_principal),
                    datasets: [{ data: data.actividades.map(a => a.total), backgroundColor: palette }]
                },
                options: { plugins: { legend: { position: 'bottom' } }, cutout: '60%' }
            });

            // Gráfica de Tendencia (Línea)
            new Chart(document.getElementById('chartTendencia'), {
                type: 'line',
                data: {
                    labels: data.tendencia.map(t => t.fecha),
                    datasets: [{
                        label: 'Encuestas por día',
                        data: data.tendencia.map(t => t.total),
                        borderColor: '#773357',
                        backgroundColor: 'rgba(119, 51, 87, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // Llenar Tabla Maestra
            const tbody = $("#tablaEncuestas tbody");
            data.maestro.forEach(e => {
                tbody.append(`
                    <tr>
                        <td class="ps-3 fw-bold text-guinda">${e.folio}</td>
                        <td class="small">${e.encuestador}</td>
                        <td class="small text-uppercase">${e.actividad_principal}</td>
                        <td class="small text-muted">${e.colonia_nombre || 'N/A'}</td>
                        <td class="text-center font-monospace">${parseFloat(e.superficie_total).toFixed(2)}</td>
                        <td class="text-center small">${e.fecha_inicio.substring(0,10)}</td>
                        <td class="text-center">
                            <span class="badge bg-success badge-status">${e.estatus}</span>
                        </td>
                    </tr>
                `);
            });

            // Buscador de tabla sencillo
            $("#tablaSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#tablaEncuestas tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
});
</script>