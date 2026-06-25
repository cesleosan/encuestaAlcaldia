<?php require_once APPROOT . '/views/inc/header_dashboard.php'; ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<header class="top-header">
    <div class="page-title">
        <h1>Geolocalización</h1>
        <p>Distribución territorial de las unidades productivas</p>
    </div>
    <div class="badge text-bg-light border px-3 py-2"><i class="fa-solid fa-location-dot text-guinda me-2"></i>Vista territorial</div>
</header>

<section class="card p-0 overflow-hidden position-relative" style="height:min(70vh,680px);min-height:480px;">
    <div id="map" style="width:100%;height:100%;"></div>
    <aside class="position-absolute bg-white p-3 shadow" style="right:18px;bottom:18px;z-index:999;border-radius:15px;min-width:170px;">
        <h6 class="fw-bold text-guinda mb-3">Simbología</h6>
        <div class="small mb-2"><i class="fa-solid fa-circle text-success me-2"></i>Agrícola</div>
        <div class="small mb-2"><i class="fa-solid fa-circle text-danger me-2"></i>Pecuaria</div>
        <div class="small"><i class="fa-solid fa-circle text-primary me-2"></i>Huerto</div>
    </aside>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([19.200, -99.150], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

const puntos = <?php echo $data['puntos']; ?>;
puntos.forEach(p => {
    let color = '#3388ff';
    if (p.tipo === 'Agrícola') color = '#24875d';
    if (p.tipo === 'Pecuaria') color = '#c83e4d';
    L.circleMarker([p.lat, p.lng], {
        color, fillColor: color, fillOpacity: .72, radius: 8, weight: 2
    }).addTo(map).bindPopup(`<b>${p.nombre}</b><br>${p.tipo}`);
});
</script>

<?php require_once APPROOT . '/views/inc/footer_dashboard.php'; ?>
