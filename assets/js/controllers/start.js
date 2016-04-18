(function() {
    angular.module('kmApp')
        .controller('startController', startController);

    function startController($scope, uiGmapGoogleMapApi, uiGmapIsReady, $location, $http, $httpParamSerializer) {
        var vm = this;

        vm.map = {
            center: {
                latitude: 52.092876,
                longitude: 5.104480
            },
            zoom: 8,
            control: {}
        };

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

                    map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
                    map.setZoom(16);
                }, null, {enableHighAccuracy: true});
                
                navigator.geolocation.watchPosition(function(position) {
                    vm.distance = vm.distance + calculateDistance(path[path.length-1].lat, path[path.length-1].lng, position.coords.latitude, position.coords.longitude);

                    path.push({lat: position.coords.latitude, lng: position.coords.longitude});
                    routePath.setPath(path);

                    map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
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

            $http({
                method: "POST",
                url: '/api/save_kms.php',
                data: $httpParamSerializer({
                    total: vm.distance,
                    route: JSON.stringify(path),
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
})();