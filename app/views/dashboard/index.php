<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">

<style>
    :root { --guinda: #773357; --guinda-light: #fdf2f7; --dorado: #987b47; }
    body { background-color: #f4f6f9; font-family: 'Montserrat', sans-serif; }
    
    /* Tarjetas Modernas */
    .card { border: none; border-radius: 15px; transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); }
    .border-left-guinda { border-left: 5px solid var(--guinda) !important; }
    .border-left-success { border-left: 5px solid #28a745 !important; }
    .border-left-info { border-left: 5px solid #17a2b8 !important; }
    
    /* Títulos y Botones */
    .text-guinda { color: var(--guinda); font-weight: 800; }
    .btn-guinda { background-color: var(--guinda); color: white; border-radius: 10px; padding: 10px 20px; font-weight: 600; border: none; }
    .btn-guinda:hover { background-color: #5a2642; color: white; }
    
    /* Mapa y Gráficas */
    #mapa-dashboard { height: 500px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 1; }
    .chart-container { position: relative; height: 300px; }
    
    .kpi-icon { font-size: 2rem; opacity: 0.3; }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="text-guinda mb-0">Panel de Control: Tierra con Corazón 2026</h1>
            <p class="text-muted">Monitoreo de levantamientos en tiempo real - Alcaldía Tlalpan</p>
        </div>
        <div class="col-md-4 text-end">
            <button onclick="location.reload()" class="btn btn-guinda shadow-sm">
                <i class="fas fa-sync-alt"></i> ACTUALIZAR DATOS
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-guinda p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small">Total Encuestas</h6>
                        <h2 class="fw-bold mb-0" id="kpi-total">0</h2>
                    </div>
                    <i class="fas fa-clipboard-check kpi-icon text-guinda"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-success p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small">Superficie Total</h6>
                        <h2 class="fw-bold mb-0"><span id="kpi-hectareas">0</span> <small class="h6">ha</small></h2>
                    </div>
                    <i class="fas fa-mountain-sun kpi-icon text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-left-info p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase text-muted small">Técnicos Activos</h6>
                        <h2 class="fw-bold mb-0" id="kpi-tecnicos">0</h2>
                    </div>
                    <i class="fas fa-user-shield kpi-icon text-info"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 text-guinda"><i class="fas fa-map-location-dot me-2"></i> Georreferenciación Territorial</h5>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-dashboard"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 text-guinda"><i class="fas fa-chart-pie me-2"></i> Vocación Productiva</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartActividades"></canvas>
                    </div>
                    <div id="legendActividades" class="mt-3 small text-muted"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 text-guinda"><i class="fas fa-list-ol me-2"></i> Top 10 Colonias por Producción</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tabla-colonias">
                            <thead class="table-light text-guinda">
                                <tr>
                                    <th>Colonia / Pueblo</th>
                                    <th class="text-center">Productores</th>
                                    <th class="text-center">Superficie (ha)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 text-center">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 text-start">
                    <h5 class="m-0 text-guinda"><i class="fas fa-exclamation-triangle me-2"></i> Problemáticas Principales</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartProblemas" style="max-height: 350px;"></canvas>
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
    // Configuración de colores
    const palette = ['#773357', '#987b47', '#4a1e36', '#2c3e50', '#1cc88a', '#36b9cc', '#f6c23e'];

    // 1. Inicializar Mapa con estilo Light
    const map = L.map('mapa-dashboard').setView([19.180, -99.160], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; Glomo MX 2026'
    }).addTo(map);

    // 2. Cargar datos desde el controlador
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            
            // Llenar KPIs (Asegúrate de que el Back envíe estos nombres)
            $("#kpi-total").text(data.kpis.total_encuestas || 0);
            $("#kpi-hectareas").text(parseFloat(data.kpis.total_hectareas || 0).toFixed(2));
            $("#kpi-tecnicos").text(data.kpis.tecnicos_activos || 0);

            // Mapa: Puntos con círculo estético
            data.puntos.forEach(p => {
                if(p.latitud && p.longitud) {
                    L.circleMarker([p.latitud, p.longitud], {
                        radius: 7,
                        fillColor: "#773357",
                        color: "#fff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map)
                    .bindPopup(`<div style="font-family:'Montserrat';"><strong>Folio:</strong> ${p.folio}<br><strong>Actividad:</strong> ${p.actividad_principal}</div>`);
                }
            });

            // Gráfica de Pay (Actividades)
            new Chart(document.getElementById('chartActividades'), {
                type: 'doughnut',
                data: {
                    labels: data.actividades.map(a => a.actividad_principal),
                    datasets: [{
                        data: data.actividades.map(a => a.total),
                        backgroundColor: palette,
                        borderWidth: 0
                    }]
                },
                options: { cutout: '75%', plugins: { legend: { display: false } } }
            });

            // Gráfica de Barras (Problemas)
            new Chart(document.getElementById('chartProblemas'), {
                type: 'bar',
                data: {
                    labels: data.problemas.map(p => p.problema || 'No especificado'),
                    datasets: [{
                        label: 'Productores afectados',
                        data: data.problemas.map(p => p.total),
                        backgroundColor: '#987b47',
                        borderRadius: 10
                    }]
                },
                options: { indexAxis: 'y', plugins: { legend: { display: false } } }
            });

            // Llenar Tabla
            const tbody = $("#tabla-colonias tbody");
            data.colonias.forEach(c => {
                tbody.append(`
                    <tr>
                        <td class="ps-3 fw-bold text-muted">${c.colonia_nombre}</td>
                        <td class="text-center"><span class="badge bg-light text-dark">${c.total}</span></td>
                        <td class="text-center font-weight-bold text-guinda">${parseFloat(c.hectareas).toFixed(2)}</td>
                    </tr>
                `);
            });
        })
        .catch(err => console.error("Error cargando dashboard:", err));
});
</script>