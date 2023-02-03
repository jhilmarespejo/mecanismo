    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/js/sw.js', {
            scope: '.'
        }).then(function (registration) {
            // Registration was successful
            console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            // registration failed :(
            console.log('Laravel PWA: ServiceWorker registration failed: ', err);
        });
    } else {
        navigator.serviceWorker.register('/js/sw.js', { scope: '.' });
    }
