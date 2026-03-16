const CACHE_NAME = 'tierra-corazon-v5'; // 🔥 Incrementamos versión para forzar actualización

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
  self.skipWaiting(); // 🔥 Fuerza al SW nuevo a tomar el control de inmediato
  e.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('SW: Cacheando activos críticos v5...');
      return cache.addAll(assets);
    })
  );
});

// 2. ACTIVACIÓN: Limpieza de cachés viejos
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (key !== CACHE_NAME) {
            console.log('SW: Borrando caché antiguo:', key);
            return caches.delete(key);
          }
        })
      );
    })
  );
  return self.clients.claim(); // 🔥 Toma el control de las pestañas abiertas inmediatamente
});

// 3. ESTRATEGIA DE RED (Network First para Navegación / Cache First para Activos)
self.addEventListener('fetch', e => {
  // Solo procesar peticiones HTTP/HTTPS
  if (!(e.request.url.indexOf('http') === 0)) return;

  // ESTRATEGIA PARA NAVEGACIÓN (Páginas HTML)
  // Esto evita el pantallazo negro al redireccionar o recargar
  if (e.request.mode === 'navigate') {
    e.respondWith(
      fetch(e.request).catch(() => {
        // Si falla la red (estamos offline), buscamos en el caché
        // Intentamos coincidir con /Encuesta o el Index
        return caches.match('/Encuesta') || 
               caches.match('/Encuesta/') || 
               caches.match('/Encuesta/index') ||
               caches.match('/');
      })
    );
    return;
  }

  // ESTRATEGIA PARA RECURSOS (Imágenes, JS, CSS)
  e.respondWith(
    caches.match(e.request, { ignoreSearch: true }).then(res => {
      // 1. Si está en caché, lo servimos
      if (res) return res;

      // 2. Si no, vamos a la red
      return fetch(e.request).then(fetchRes => {
        // Opcional: Podrías guardar en caché dinámico aquí
        return fetchRes;
      }).catch(() => {
        // Si falla todo, devolvemos una respuesta vacía válida
        return new Response('Offline: Recurso no disponible', {
          status: 503,
          headers: { 'Content-Type': 'text/plain' }
        });
      });
    })
  );
});