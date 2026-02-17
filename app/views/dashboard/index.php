<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>

<div class="top-header">
    <div class="page-title">
        <h1>Visión General</h1>
        <p>Resultados en tiempo real del Censo Productivo</p>
    </div>
    <button class="btn-action">
        <i class="fa-solid fa-download"></i> Descargar Reporte PDF
    </button>
</div>

<div class="kpi-grid">
    <div class="card">
        <div class="card-info"><p>Total Encuestas</p><h3><?php echo number_format($data['stats']['kpis']['total']); ?></h3></div>
        <div class="card-icon" style="color:var(--guinda);"><i class="fa-solid fa-clipboard-list"></i></div>
    </div>
    <div class="card">
        <div class="card-info"><p>Productores Activos</p><h3><?php echo number_format($data['stats']['kpis']['productores']); ?></h3></div>
        <div class="card-icon" style="color:#2ecc71;"><i class="fa-solid fa-user-check"></i></div>
    </div>
    <div class="card">
        <div class="card-info"><p>Hectáreas Reg.</p><h3><?php echo $data['stats']['kpis']['hectareas']; ?></h3></div>
        <div class="card-icon" style="color:#f39c12;"><i class="fa-solid fa-seedling"></i></div>
    </div>
    <div class="card">
        <div class="card-info"><p>Avance Meta</p><h3><?php echo $data['stats']['kpis']['avance']; ?>%</h3></div>
        <div class="card-icon" style="color:#3498db;"><i class="fa-solid fa-chart-line"></i></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 20px;">
    
    <div class="card" style="display:block;">
        <h4 style="margin-top:0; color:var(--oscuro);">1. Distribución por Tipo de Producción</h4>
        <div style="height: 250px;">
            <canvas id="chartProduccion"></canvas>
        </div>
    </div>

    <div class="card" style="display:block;">
        <h4 style="margin-top:0; color:var(--oscuro);">2. Principales Problemáticas (Top 5)</h4>
        <div style="height: 250px;">
            <canvas id="chartProblemas"></canvas>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    
    <div class="card" style="display:block;">
        <h4 style="margin-top:0; color:var(--oscuro);">3. Avance por Pueblo Originario</h4>
        <div style="height: 250px;">
            <canvas id="chartPueblos"></canvas>
        </div>
    </div>

    <div class="card" style="display:block;">
        <h4 style="margin-top:0; color:var(--oscuro);">4. Género</h4>
        <div style="height: 250px; display:flex; justify-content:center;">
            <canvas id="chartSexo"></canvas>
        </div>
    </div>
</div>

<script>
    // Colores Institucionales
    const colorGuinda = '#9F2241';
    const colorDorado = '#BC955C';
    const colorGris = '#2C3E50';
    const paleta = ['#9F2241', '#BC955C', '#2C3E50', '#e67e22', '#27ae60'];

    // --- GRÁFICA 1: DONA (PRODUCCIÓN) ---
    new Chart(document.getElementById('chartProduccion'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($data['stats']['grafica_produccion']['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($data['stats']['grafica_produccion']['data']); ?>,
                backgroundColor: paleta,
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right' } },
            cutout: '60%'
        }
    });

    // --- GRÁFICA 2: BARRAS HORIZONTALES (PROBLEMAS) ---
    new Chart(document.getElementById('chartProblemas'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($data['stats']['grafica_problemas']['labels']); ?>,
            datasets: [{
                label: 'Reportes',
                data: <?php echo json_encode($data['stats']['grafica_problemas']['data']); ?>,
                backgroundColor: colorGuinda,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y', // Esto la hace horizontal
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // --- GRÁFICA 3: BARRAS VERTICALES (PUEBLOS) ---
    new Chart(document.getElementById('chartPueblos'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($data['stats']['grafica_pueblos']['labels']); ?>,
            datasets: [{
                label: 'Encuestas',
                data: <?php echo json_encode($data['stats']['grafica_pueblos']['data']); ?>,
                backgroundColor: colorDorado,
                borderRadius: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // --- GRÁFICA 4: PASTEL (SEXO) ---
    new Chart(document.getElementById('chartSexo'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($data['stats']['grafica_sexo']['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($data['stats']['grafica_sexo']['data']); ?>,
                backgroundColor: [colorGuinda, colorGris],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>