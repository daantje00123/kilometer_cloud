(function() {
    console.re.log('INIT');
    
    if (!navigator.geolocation) {
        // Geolocation wordt niet ondersteund
        alert("Uw browser ondersteunt geen geolocation!");
        return;
    }

    // Geolocation wordt wel ondersteund

    // Alle jQuery elementen inladen
    var startLat = $('#startLat');
    var startLon = $('#startLon');
    var currentLat = $('#currentLat');
    var currentLon = $('#currentLon');
    var distanceEl = $('#distance');
    var startDate = new Date();
    startDate = startDate.getFullYear()+'-'+(startDate.getMonth()+1)+'-'+startDate.getDate()+' '+startDate.getHours()+':'+startDate.getMinutes()+":"+startDate.getSeconds();

    // Lege variabelen inladen
    var distance = 0;
    var map;
    var routePath;
    var route = [];
    var startMarker;

    // Funtie om de start position te vinden
    function setStartPos(position) {
        addNewPos(position);
        startLat.html(position.coords.latitude);
        startLon.html(position.coords.longitude);
    }

    // Functie om de huidige locatie te vinden
    function setCurrentPos(position) {
        currentLat.html(position.coords.latitude);
        currentLon.html(position.coords.longitude);

        var prevPos = route[route.length-1];
        var newDistance = distance + calculateDistance(prevPos.lat, prevPos.lng, position.coords.latitude, position.coords.longitude);
        setDistance(newDistance);

        addNewPos(position);

        if (map) {
            map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
        }
    }

    function addNewPos(position) {
        route.push({lat: position.coords.latitude, lng: position.coords.longitude});

        //console.re.log(route);

        if (routePath) {
            routePath.setPath(route);
        }
    }

    // Functie om de afstand te updaten op het scherm
    function setDistance(value) {
        distance = value;
        distanceEl.html((Math.round(value*100)/100).toString().replace('.', ','));
    }

    // Functie om error te laten zien
    function showError(error) {
        console.re.error(error);
        //alert("Er is een fout opgetreden tijdens het ophalen van uw locatie");
    }

    // Functie om afstand te meten tussen twee coordinaten
    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the earth in km
        var dLat = deg2rad(lat2 - lat1);  // deg2rad below
        var dLon = deg2rad(lon2 - lon1);
        var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2)
            ;
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c; // Distance in km
        if (d > 0.5) {
            return 0;
        }
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180)
    }

    // Initialize google maps
    window.initMap = function() {
        if (!route[0]) {
            var timer = setInterval(function() {
                if (route[0]) {
                    clearInterval(timer);

                    map = new google.maps.Map(document.getElementById('map'), {
                        center: route[0],
                        zoom: 18
                    });

                    routePath = new google.maps.Polyline({
                        geodesic: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 1.0,
                        strokeWeight: 2
                    });

                    startMarker = new google.maps.Marker({
                        position: route[0],
                        map: map,
                        title: 'Start'
                    });

                    routePath.setMap(map);
                }
            }, 100);
        }
    };

    var posOptions = {
        enableHighAccuracy: true
    };

    navigator.geolocation.getCurrentPosition(setStartPos, showError, posOptions);
    navigator.geolocation.watchPosition(setCurrentPos, showError, posOptions);

    // Event handlers
    $('#toggleDetails').on('click', function() {
        $('#details').toggle();
    });

    $('#saveRoute').on('click', function() {
        if (distance <= 0 || route.length <= 0) {
            alert("De route is nog te kort om op te slaan.");
            return;
        }

        if (!confirm("Weet u zeker dat u de route wilt opslaan?")) {
            alert("Route wordt verder opgenomen.");
            return;
        }

        var tmp_route = JSON.stringify(route);

        console.re.info("Route wordt opgeslagen");
        $.post('https://km.thuis.daanvanberkel.nl/api/save_kms.php', {route: tmp_route, total: distance, start_date: startDate}, function(data) {
            alert("De route is opgeslagen");
            window.location.href = "https://km.thuis.daanvanberkel.nl/";
        });
    });

    $('#deleteRoute').on('click', function() {
        if (confirm("Weet u zeker dat u de route wilt verwijderen?")) {
            window.location.href = "https://km.thuis.daanvanberkel.nl/";
        }
    });
})();