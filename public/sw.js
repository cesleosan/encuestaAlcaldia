const CACHE_NAME = 'tierra-corazon-v4'; // Subimos a v4 para aplicar cambios

// 1. Lista de activos locales (Sin depender de CDNs externas para lo crítico)
const assets = [
  '/',
  '/Encuesta',
  '/mapa_offline.php',            // NUEVO: La página del prototipo
  '/css/styles.css',
  '/css/leaflet.css',             // CAMBIO: Ahora local
  '/js/survey-engine.js',
  '/js/leaflet.js',               // CAMBIO: Ahora local
  '/js/pouchdb.min.js',           // NUEVO: Librería de base de datos de mapas
  '/js/L.TileLayer.PouchDB.js',   // NUEVO: Plugin de Leaflet
  '/logos/Logo%20AT%20Vertical%20guinda%20100%20PX.png',
  '/logos/Logo%20AT%20Vertical%20N%20100PX.png',
  'https://code.jquery.com/jquery-3.6.0.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11',
  'https://unpkg.com/dexie/dist/dexie.js'
];

// 2. INSTALACIÓN
self.addEventListener('install', e => {
  self.skipWaiting(); 
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('SW: Guardando archivos locales y librerías de mapas...');
      return cache.addAll(assets);
    })
  );
});

// 3. ACTIVACIÓN
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      );
    })
  );
  return self.clients.claim();
});

// 4. ESTRATEGIA DE RED
self.addEventListener('fetch', e => {
  // Ignorar peticiones de esquemas que no sean http/https (como chrome-extension)
  if (!(e.request.url.indexOf('http') === 0)) return;

  e.respondWith(
    caches.match(e.request).then(res => {
      if (res) return res;

      return fetch(e.request).then(fetchRes => {
        // OPTIMIZACIÓN: No guardamos los tiles de OSM en el Cache del SW
        // ¿Por qué? Porque para eso ya instalamos PouchDB, que es más eficiente.
        // Si los guardamos en ambos lados, llenarás la memoria del celular el doble de rápido.
        
        return fetchRes;
      });
    }).catch(() => {
        // FALLBACK: Si falla internet y no está en caché
        if (e.request.mode === 'navigate') {
            if (e.request.url.includes('/Encuesta')) return caches.match('/Encuesta');
            if (e.request.url.includes('mapa_offline')) return caches.match('/mapa_offline.php');
        }
    })
  );
});