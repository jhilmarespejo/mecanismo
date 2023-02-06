self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        })
    );
    console.log('ssssssssss');
});


// self.addEventListener('fetch', event => {
//     alert(event);
// });




