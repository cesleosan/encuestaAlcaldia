<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prototipo Mapa Offline - Tierra con Corazón</title>
    
    <link rel="stylesheet" href="css/leaflet.css" />
    
    <style>
        body { margin: 0; font-family: sans-serif; }
        #map { height: calc(100 vh - 100px); width: 100%; background: #eee; }
        .controls { 
            padding: 15px; 
            background: #773357; /* Color guinda Alcaldía */
            color: white; 
            display: flex; 
            flex-direction: column; 
            gap: 10px;
        }
        .btn-accion {
            padding: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            background: white;
            color: #773357;
        }
        #status { font-size: 0.9rem; margin: 0; font-style: italic; }
        .indicador-gps { color: #2ecc71; font-weight: bold; }
    </style>
</head>
<body>

<div class="controls">
    <div style="display: flex; gap: 10px;">
        <button class="btn-accion" onclick="descargarZona()">1. Descargar Tlalpan (Oficina)</button>
        <button class="btn-accion" style="background: #2c3e50; color: white;" onclick="simularOffline()">2. Probar Offline</button>
    </div>
    <p id="status">Estado: Listo. Conecta internet para descargar capas.</p>
</div>

<div id="map"></div>

<script src="js/leaflet.js"></script>
<script src="js/pouchdb.min.js"></script>
<script src="https://unpkg.com/@mapbox/tile-cover@3.0.2/index.js"></script>
<script src="js/L.TileLayer.PouchDB.js"></script>

<script>
    // 1. Inicialización del mapa centrando en Tlalpan
    var map = L.map('map').setView([19.289, -99.167], 15); 
    var marker;

    // 2. Configuración de Capa con PouchDB
    // Nota: El navegador guardará los "tiles" en IndexedDB bajo el nombre 'mapa-tlalpan-v1'
    var offlineLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        minZoom: 12,
        useCache: true,
        crossOrigin: true, 
        cacheName: 'mapa-tlalpan-v1',
        attribution: '© OpenStreetMap'
    });

    offlineLayer.addTo(map);

    // 3. Función de Descarga Automática (SEED)
    function descargarZona() {
        const status = document.getElementById('status');
        
        if (!navigator.onLine) {
            alert("Necesitas internet para descargar el mapa primero.");
            return;
        }

        // Definimos el rectángulo de Tlalpan Centro / Zonas críticas
        var bbox = L.latLngBounds(
            [19.250, -99.200], // Suroeste
            [19.320, -99.130]  // Noreste
        );

        status.innerHTML = "<b>Iniciando descarga masiva...</b> No cierres la ventana.";

        // Descargamos zooms del 13 al 16 (equilibrio entre detalle y peso)
        offlineLayer.seed(bbox, 13, 16);
    }

    // Eventos de Progreso del SEED
    offlineLayer.on('seedprogress', function(p) {
        var porcentaje = ((p.downloaded / p.remainingData) * 100).toFixed(0);
        document.getElementById('status').innerText = `Descargando: ${porcentaje}% (${p.downloaded} de ${p.remainingData} cuadros)`;
    });

    offlineLayer.on('seedend', function() {
        document.getElementById('status').innerHTML = "✅ <b>Tlalpan descargado.</b> Ya puedes desconectar el Wi-Fi.";
        alert("Mapa guardado en la memoria del celular.");
    });

    offlineLayer.on('seederror', function(err) {
        document.getElementById('status').innerText = "❌ Error en descarga: " + err.error;
    });

    // 4. Lógica de Simulación
    function simularOffline() {
        alert("Simulando... Apaga tu Wi-Fi manualmente y refresca la página para la prueba real.");
        document.getElementById('status').innerText = "Modo: TRABAJO EN CAMPO (Offline)";
    }

    // 5. Lógica del GPS (Punto Azul Dinámico)
    function rastrearGPS() {
        // watch: true mantiene el seguimiento mientras el técnico camina
        map.locate({
            setView: true, 
            maxZoom: 16, 
            watch: true, 
            enableHighAccuracy: true 
        });
        
        map.on('locationfound', function(e) {
            console.log("GPS Localizado: ", e.latlng);
            if (!marker) {
                // Icono circular tipo Google Maps
                marker = L.circleMarker(e.latlng, {
                    radius: 8,
                    fillColor: "#3498db",
                    color: "#fff",
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map).bindPopup("Tu ubicación actual");
            } else {
                marker.setLatLng(e.latlng);
            }
        });

        map.on('locationerror', function(e) {
            console.warn("No se pudo obtener GPS: " + e.message);
        });
    }

    // Iniciar GPS al cargar
    rastrearGPS();
</script>
</body>
</html>