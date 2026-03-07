const CACHE_NAME = 'tierra-corazon-v4';

const assets = [
  '/',
  '/Encuesta',
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
    caches.match(e.request).then(res => {
      // Si está en caché, lo servimos de inmediato
      if (res) return res;

      // Si no, intentamos ir a la red
      return fetch(e.request).then(fetchRes => {
        return fetchRes;
      }).catch(() => {
        // 🔥 FIX QUIRÚRGICO: Manejo de errores para evitar el 'Failed to convert to Response'
        
        // Si es una navegación (una página)
        if (e.request.mode === 'navigate') {
          if (e.request.url.includes('/Encuesta')) return caches.match('/Encuesta');
          if (e.request.url.includes('mapa_offline')) return caches.match('/mapa_offline.php');
          return caches.match('/');
        }

        // Si es un recurso (imagen, script, etc) y no hay red ni caché
        // Devolvemos una respuesta vacía pero VÁLIDA para que el navegador no lance el TypeError
        return new Response('Recurso no disponible offline', {
          status: 503,
          statusText: 'Service Unavailable',
          headers: new Headers({ 'Content-Type': 'text/plain' })
        });
      });
    })
  );
});