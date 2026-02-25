const CACHE_NAME = 'tierra-corazon-v3'; // Incrementamos versión para forzar actualización

// 1. Rutas con "/" al inicio para asegurar que siempre apunten a la raíz pública
const assets = [
  '/Encuesta',
  '/css/styles.css',
  '/js/survey-engine.js',
  '/logos/Logo%20AT%20Vertical%20guinda%20100%20PX.png',
  '/logos/Logo%20AT%20Vertical%20N%20100PX.png',
  'https://code.jquery.com/jquery-3.6.0.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11',
  'https://unpkg.com/dexie/dist/dexie.js',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
  'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
];

// 2. INSTALACIÓN: Forzamos a que el nuevo SW tome el control de inmediato
self.addEventListener('install', e => {
  self.skipWaiting(); 
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('SW: Guardando archivos en la bodega local...');
      return cache.addAll(assets);
    })
  );
});

// 3. ACTIVACIÓN: Limpieza de cachés viejos (v1, v2, etc.)
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

// 4. ESTRATEGIA DE RED: Cache First (Lo que ya tenemos, úsalo)
self.addEventListener('fetch', e => {
  e.respondWith(
    caches.match(e.request).then(res => {
      // Si está en caché, lo servimos de inmediato (ahorra datos y tiempo)
      if (res) return res;

      // Si no, vamos a internet
      return fetch(e.request).then(fetchRes => {
        // Si son mapas de OpenStreetMap, los guardamos sobre la marcha
        if (e.request.url.includes('tile.openstreetmap.org')) {
          return caches.open(CACHE_NAME).then(cache => {
            cache.put(e.request.url, fetchRes.clone());
            return fetchRes;
          });
        }
        return fetchRes;
      });
    }).catch(() => {
        // FALLBACK: Si falla internet y no está en caché (Offline total)
        // Si el usuario intenta navegar a la encuesta, le servimos la página principal cacheada
        if (e.request.mode === 'navigate' || e.request.url.includes('/Encuesta')) {
            return caches.match('/Encuesta');
        }
    })
  );
});
