const CACHE_NAME = 'tierra-corazon-v4';

const assets = [
  '/',
  '/Encuesta',
  '/Encuesta/',
  '/Encuesta/index',
  '/mapa_offline.php',
  '/css/styles.css',
  '/css/leaflet.css',
  '/js/survey-engine.js',
  '/js/leaflet.js',
  '/js/pouchdb.min.js',
  '/js/L.TileLayer.PouchDB.js',
  '/logos/Logo%20AT%20Vertical%20guinda%20100%20PX.png',
  '/logos/Logo%20AT%20Vertical%20N%20100PX.png',
  'https://code.jquery.com/jquery-3.6.0.min.js',
  'https://cdn.jsdelivr.net/npm/sweetalert2@11',
  'https://unpkg.com/dexie/dist/dexie.js'
];

// 1. INSTALACIÓN
self.addEventListener('install', e => {
  self.skipWaiting(); 
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('SW: Cacheando activos críticos...');
      return cache.addAll(assets);
    })
  );
});

// 2. ACTIVACIÓN
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

// 3. ESTRATEGIA DE RED (Cache First + Network Fallback)
self.addEventListener('fetch', e => {
    if (!(e.request.url.indexOf('http') === 0)) return;

    e.respondWith(
        caches.match(e.request, { ignoreSearch: true }).then(res => {
            // 1. Si está en caché, lo servimos (Cache First)
            if (res) return res;

            // 2. Si no está, intentamos red
            return fetch(e.request).then(fetchRes => {
                // Opcional: Podrías cachear dinámicamente aquí lo que vayas encontrando
                return fetchRes;
            }).catch(() => {
                // 🚨 FALLBACK OFFLINE (Aquí evitamos el pantallazo negro)
                
                // Si el técnico está navegando (recarga o link)
                if (e.request.mode === 'navigate') {
                    // Si la URL contiene "Encuesta", devolvemos el cascarón cacheado
                    if (e.request.url.includes('/Encuesta')) {
                        return caches.match('/Encuesta');
                    }
                    return caches.match('/');
                }

                // Si falla un recurso (img/js) devolvemos error controlado
                return new Response('Offline: Recurso no disponible', {
                    status: 503,
                    headers: { 'Content-Type': 'text/plain' }
                });
            });
        })
    );
});