<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prototipo Mapa Offline - Tierra con Corazón</title>
    
    <link rel="stylesheet" href="css/leaflet.css" />
    
    <style>
        body { margin: 0; font-family: sans-serif; background: #f4f4f4; }
        #map { height: calc(100vh - 120px); width: 100%; background: #eee; }
        .controls { 
            padding: 15px; 
            background: #773357; 
            color: white; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .btn-group { display: flex; gap: 10px; margin-bottom: 10px; }
        .btn-accion {
            flex: 1;
            padding: 12px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            background: white;
            color: #773357;
            font-size: 14px;
        }
        .btn-offline { background: #2c3e50; color: white; }
        #status { font-size: 0.85rem; margin: 0; background: rgba(0,0,0,0.1); padding: 5px; border-radius: 3px; }
    </style>
</head>
<body>

<div class="controls">
    <div class="btn-group">
        <button class="btn-accion" onclick="descargarZona()">1. Descargar Tlalpan</button>
        <button class="btn-accion btn-offline" onclick="simularOffline()">2. Probar sin Red</button>
    </div>
    <p id="status">Estado: Listo para trabajar.</p>
</div>

<div id="map"></div>

<script src="js/leaflet.js"></script>
<script src="js/pouchdb.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@mapbox/tile-cover@3.0.2/index.js"></script>

<script src="js/L.TileLayer.PouchDB.js"></script>

<script>
    // --- PARCHE DE SEGURIDAD ---
    // Si la librería se cargó como 'tilecover', se la pasamos a 'tileCover'
    if (typeof window.tilecover !== 'undefined') {
        window.tileCover = window.tilecover;
    } else {
        console.error("La librería tile-cover no cargó. Revisa tu conexión o el link.");
    }
    // 1. PARCHE DE COMPATIBILIDAD
    // El plugin busca 'tileCover' pero la librería se registra como 'tilecover'
    window.tileCover = window.tilecover;

    // 2. INICIALIZACIÓN
    var map = L.map('map').setView([19.289, -99.167], 15); 
    var marker;

    // 3. CONFIGURACIÓN DE CAPA (Ajustada al plugin que pegaste)
    var offlineLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        minZoom: 12,
        useCache: true,      // Activa PouchDB
        crossOrigin: true,
        cacheMaxAge: 24*3600*1000 * 7 // 7 días de vida
    });

    offlineLayer.addTo(map);

    // 4. FUNCIÓN SEED (Descarga masiva)
    function descargarZona() {
        const status = document.getElementById('status');
        
        if (!navigator.onLine) {
            alert("Necesitas conexión a internet para descargar la zona.");
            return;
        }

        // Definimos el área de Tlalpan Centro
        var bbox = L.latLngBounds(
            [19.250, -99.200], // Sur-Oeste
            [19.320, -99.130]  // Norte-Este
        );

        status.innerHTML = "<b>Calculando cuadros...</b>";
        
        // El plugin usa .seed(bbox, minZoom, maxZoom)
        // Zoom 14 al 16 es ideal para ver calles sin pesar gigabytes
        offlineLayer.seed(bbox, 14, 16);
    }

    // 5. EVENTOS DEL PLUGIN (Nombres corregidos según tu código fuente)
    
    // Al iniciar
    offlineLayer.on('tilecache-load-start', function(res) {
        document.getElementById('status').innerHTML = "<b>Iniciando descarga masiva...</b>";
    });

    // Durante el progreso
    offlineLayer.on('tilecache-load-progress', function(p) {
        var porcentaje = ((p.done / p.total) * 100).toFixed(0);
        document.getElementById('status').innerText = `Descargando: ${porcentaje}% (${p.done} de ${p.total} fotos)`;
    });

    // Al terminar
    offlineLayer.on('tilecache-load-done', function(res) {
        document.getElementById('status').innerHTML = "✅ <b>Tlalpan guardado (" + res.downloadSize + " MB).</b>";
        alert("¡Éxito! El mapa ya funciona sin internet.");
    });

    // 6. LÓGICA DE SIMULACIÓN Y GPS
    function simularOffline() {
        alert("Ahora puedes desconectar tu internet o poner modo avión. El mapa seguirá ahí.");
        document.getElementById('status').innerText = "Modo: TRABAJO EN CAMPO (Sin Conexión)";
    }

    function rastrearGPS() {
        map.locate({
            setView: true, 
            maxZoom: 16, 
            watch: true, 
            enableHighAccuracy: true 
        });
        
        map.on('locationfound', function(e) {
            if (!marker) {
                marker = L.circleMarker(e.latlng, {
                    radius: 8,
                    fillColor: "#3498db",
                    color: "#fff",
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map).bindPopup("Ubicación del Técnico");
            } else {
                marker.setLatLng(e.latlng);
            }
        });
    }

    // Ejecutar GPS
    rastrearGPS();
</script>

</body>
</html>