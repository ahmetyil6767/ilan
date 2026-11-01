// JaayQuick Service Worker (basic cache-first for static assets)
const CACHE = 'jaayquick-v1';
const PRECACHE = ['/', '/manifest.json'];

self.addEventListener('install', function(e) {
  e.waitUntil(caches.open(CACHE).then(function(c){ return c.addAll(PRECACHE); }).catch(function(){}));
  self.skipWaiting();
});

self.addEventListener('activate', function(e) {
  e.waitUntil(caches.keys().then(function(keys){
    return Promise.all(keys.filter(function(k){return k!==CACHE;}).map(function(k){return caches.delete(k);}));
  }));
  self.clients.claim();
});

self.addEventListener('fetch', function(e) {
  if (e.request.method !== 'GET') return;
  e.respondWith(
    caches.match(e.request).then(function(r){
      return r || fetch(e.request).then(function(resp){
        // Cache static assets
        if (resp && resp.status === 200 && (e.request.url.indexOf('.css')>-1 || e.request.url.indexOf('.js')>-1 || e.request.url.indexOf('/uploads/')>-1)) {
          var clone = resp.clone();
          caches.open(CACHE).then(function(c){ c.put(e.request, clone); });
        }
        return resp;
      }).catch(function(){ return caches.match('/'); });
    })
  );
});