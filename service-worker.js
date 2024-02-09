const cacheName = 'weather-app-cache'; //name of the cache
const cacheFiles = [ //list of files to be cached
  '/',
  '/index.html',
  '/style.css',
  '/favicon-32x32.png',
  '/search-removebg-preview.png',
  '/wind-removebg-preview (1).png',
  '/humidity-removebg-preview (1).png',
  '/script.js',
  '/weather_data.php'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(cacheName).then(cache => { //open the cache
      return cache.addAll(cacheFiles); //add the files to the cache
    })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => { //check if the requested resource is in the cache
      return response || fetch(event.request); //return the cached response or fetch it from the network
    })
  );
});
