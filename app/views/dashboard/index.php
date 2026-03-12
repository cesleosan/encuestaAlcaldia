<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

<div class="container-fluid py-4" id="dashboard-app" style="background-color: #f8f9fc;">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Panel de Control: Tierra con Corazón 2026</h1>
        <button onclick="location.reload()" class="btn btn-sm btn-guinda shadow-sm">
            <i class="fas fa-sync-alt fa-sm text-white-50"></i> Actualizar Datos
        </button>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-guinda shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-guinda text-uppercase mb-1">Total Encuestas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-total">0</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clipboard-list fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hectáreas Censadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-hectareas">0.00</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-seedling fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Técnicos en Campo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kpi-tecnicos">0</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-guinda"><i class="fas fa-map-marked-alt"></i> Georreferenciación Territorial</h6>
                </div>
                <div class="card-body p-0">
                    <div id="mapa-dashboard" style="height: 550px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-guinda">Vocación Productiva</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area"><canvas id="chartActividades"></canvas></div>
                    <hr>
                    <small class="text-muted">Distribución porcentual por tipo de actividad principal.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-guinda">Top 10 Colonias por Superficie</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla-colonias" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Colonia / Pueblo</th>
                                    <th>Productores</th>
                                    <th>Hectáreas</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-guinda">Problemáticas del Campo</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartProblemas" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root { --guinda: #773357; --dorado: #987b47; }
    .btn-guinda { background-color: var(--guinda); color: white; border-radius: 8px; }
    .btn-guinda:hover { background-color: #5a2642; color: #eee; }
    .border-left-guinda { border-left: .25rem solid var(--guinda)!important; }
    .border-left-success { border-left: .25rem solid #1cc88a!important; }
    .border-left-info { border-left: .25rem solid #36b9cc!important; }
    .card { border-radius: 12px; overflow: hidden; }
    .card-header { border-bottom: 1px solid #f1f1f1; }
    #mapa-dashboard { z-index: 1; }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Configuración de colores Glomo
    const colors = ['#773357', '#987b47', '#2e59d9', '#1cc88a', '#f6c23e', '#e74a3b', '#858796'];

    // 1. Mapa centrado en el sur de Tlalpan (Zona Agrícola)
    const map = L.map('mapa-dashboard').setView([19.180, -99.150], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

    // 2. Fetch de datos
    fetch('<?php echo URLROOT; ?>/Encuesta/getEstadisticas')
        .then(res => res.json())
        .then(data => {
            
            // Llenar KPIs
            $("#kpi-total").text(data.kpis.total_encuestas);
            $("#kpi-hectareas").text(parseFloat(data.kpis.total_hectareas).toFixed(2));
            $("#kpi-tecnicos").text(data.kpis.tecnicos_activos);

            // Mapa: Puntos con popups profesionales
            data.puntos.forEach(p => {
                if(p.latitud && p.longitud) {
                    L.circleMarker([p.latitud, p.longitud], {
                        radius: 6,
                        fillColor: "#773357",
                        color: "#fff",
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.9
                    }).addTo(map)
                    .bindPopup(`
                        <div style="font-family: 'Montserrat', sans-serif;">
                            <strong style="color:var(--guinda);">Folio: ${p.folio}</strong><br>
                            <b>Actividad:</b> ${p.actividad_principal}<br>
                            <hr style="margin:5px 0">
                            <small>Punto capturado vía GPS</small>
                        </div>
                    `);
                }
            });

            // Gráfica de Actividades (Doughnut)
            new Chart(document.getElementById('chartActividades'), {
                type: 'doughnut',
                data: {
                    labels: data.actividades.map(a => a.actividad_principal),
                    datasets: [{
                        data: data.actividades.map(a => a.total),
                        backgroundColor: colors,
                        hoverOffset: 10
                    }]
                },
                options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
            });

            // Gráfica de Problemas (Barras Horizontales)
            new Chart(document.getElementById('chartProblemas'), {
                type: 'bar',
                data: {
                    labels: data.problemas.map(p => p.problema),
                    datasets: [{
                        label: 'Número de Productores',
                        data: data.problemas.map(p => p.total),
                        backgroundColor: '#987b47'
                    }]
                },
                options: { indexAxis: 'y', plugins: { legend: { display: false } } }
            });

            // Llenar Tabla de Colonias
            const tbody = $("#tabla-colonias tbody");
            data.colonias.forEach(c => {
                tbody.append(`
                    <tr>
                        <td><i class="fas fa-map-pin text-guinda mr-2"></i> ${c.colonia_nombre}</td>
                        <td><span class="badge badge-light">${c.total}</span></td>
                        <td class="font-weight-bold">${parseFloat(c.hectareas).toFixed(2)} ha</td>
                    </tr>
                `);
            });

        });
});
</script>