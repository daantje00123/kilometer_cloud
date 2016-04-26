(function() {
    angular.module('kmApp')
        .controller('startController', startController);

    startController.$inject = ['uiGmapGoogleMapApi', 'uiGmapIsReady', '$location', '$http', '$httpParamSerializer', 'authFactory', 'saveRoutePart'];
    function startController(uiGmapGoogleMapApi, uiGmapIsReady, $location, $http, $httpParamSerializer, authFactory, saveRoutePart) {
        var vm = this;

        vm.user = authFactory.getUserData();

        vm.map = {
            center: {
                latitude: 52.092876,
                longitude: 5.104480
            },
            zoom: 8,
            control: {}
        };

        vm.speed = 0;

        var path = [];
        vm.distance = 0;
        var startDate = new Date();
        startDate = startDate.getFullYear()+'-'+(startDate.getMonth()+1)+'-'+startDate.getDate()+' '+startDate.getHours()+':'+startDate.getMinutes()+":"+startDate.getSeconds();

        console.log("Start datetime: "+startDate);

        if (!navigator.geolocation) {
            alert("Uw browser wordt niet ondersteund!");
            return;
        }

        uiGmapGoogleMapApi.then(function(maps) {
            uiGmapIsReady.promise(1).then(function(instances) {
                var map = instances[0].map;
                var routePath = new maps.Polyline({
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    map: map
                });

                navigator.geolocation.getCurrentPosition(function(position){
                    var startMarker = new maps.Marker({
                        position: {lat: position.coords.latitude, lng: position.coords.longitude},
                        map: map,
                        title: 'Start'
                    });

                    path.push({lat: position.coords.latitude, lng: position.coords.longitude});
                    routePath.setPath(path);

                    saveRoutePart(position.coords.latitude, position.coords.longitude);

                    map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
                    map.setZoom(16);
                    var time1 = Date.now()/1000;

                    navigator.geolocation.watchPosition(function(position) {
                        var distance = calculateDistance(path[path.length-1].lat, path[path.length-1].lng, position.coords.latitude, position.coords.longitude)
                        vm.distance = vm.distance + distance;
                        vm.speed = calculateSpeed(time1, Date.now()/1000, distance);

                        path.push({lat: position.coords.latitude, lng: position.coords.longitude});
                        routePath.setPath(path);

                        saveRoutePart(position.coords.latitude, position.coords.longitude);

                        map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
                        time1 = Date.now()/1000;
                    }, null, {enableHighAccuracy: true});
                }, null, {enableHighAccuracy: true});
            });
        });

        vm.delete = function() {
            if (!confirm("Weet u zeker dat u de route wilt verwijderen?")) {
                return;
            }

            $location.path('/');
        };

        vm.save = function() {
            if (!confirm("Weet u zeker dat u de route wilt opslaan?")) {
                return;
            }

            if (path.length < 1) {
                alert("De route is nog te kort om op te slaan.");
                return;
            }

            saveRoutePart(true)
                .then(function(data) {
                    $http({
                        method: "POST",
                        url: '/api/v1/protected/route',
                        data: $httpParamSerializer({
                            kms: vm.distance,
                            start_date: startDate
                        }),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    }).then(function(response) {
                        alert("De route is opgeslagen!");
                        $location.path('/');
                    }, function(response) {
                        console.log(response);
                        alert("Er is iets fout gegaan tijdens het opslaan van de route");
                    });
                }, function (data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het opslaan van de route");
                });
        }
    }

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

    function calculateSpeed(time1, time2, distance) {
        // Convert seconds to hours
        time1 = time1/60/60;
        time2 = time2/60/60;

        return distance / (time2 - time1);
    }
})();