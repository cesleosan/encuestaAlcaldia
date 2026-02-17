<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="top-header">
    <div class="page-title">
        <h1>Geolocalización</h1>
        <p>Distribución territorial de las unidades productivas</p>
    </div>
</div>

<div class="card" style="height: 600px; padding:0; position:relative;">
    <div id="map" style="width: 100%; height: 100%; border-radius:25px;"></div>
    
    <div style="position:absolute; bottom:20px; right:20px; background:white; padding:15px; border-radius:25px; box-shadow:0 4px 10px rgba(0,0,0,0.2); z-index:999;">
        <h5 style="margin:0 0 10px 0;">Simbología</h5>
        <div style="display:flex; align-items:center; gap:5px; margin-bottom:5px;"><span style="width:10px; height:10px; background:#2ecc71; border-radius:50%;"></span> Agrícola</div>
        <div style="display:flex; align-items:center; gap:5px; margin-bottom:5px;"><span style="width:10px; height:10px; background:#e74c3c; border-radius:50%;"></span> Pecuaria</div>
        <div style="display:flex; align-items:center; gap:5px;"><span style="width:10px; height:10px; background:#3498db; border-radius:25px;"></span> Huerto</div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inicializar mapa centrado en Tlalpan
    var map = L.map('map').setView([19.200, -99.150], 12);

    // Cargar capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Datos desde PHP
    var puntos = <?php echo $data['puntos']; ?>;

    // Icono personalizado (opcional, usaremos colores simples por ahora)
    puntos.forEach(p => {
        let color = '#3388ff';
        if(p.tipo === 'Agrícola') color = 'green';
        if(p.tipo === 'Pecuaria') color = 'red';

        // Marcador simple
        L.circleMarker([p.lat, p.lng], {
            color: color,
            fillColor: color,
            fillOpacity: 0.5,
            radius: 8
        }).addTo(map)
        .bindPopup(`<b>${p.nombre}</b><br>${p.tipo}`);
    });
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>