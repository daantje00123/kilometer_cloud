(function() {
    angular.module('kmApp')
        .controller('routesViewController', routesViewController);

    routesViewController.$inject = ['$http', '$location', '$routeParams', 'uiGmapGoogleMapApi', 'uiGmapIsReady'];
    function routesViewController($http, $location, $routeParams, uiGmapGoogleMapApi, uiGmapIsReady) {
        var vm = this;

        vm.id_route = $routeParams.id;
        vm.route = {};
        vm.map = {
            center: {
                latitude: 52.092876,
                longitude: 5.104480
            },
            zoom: 8,
            control: {}
        };

        if (!vm.id_route) {
            $location.path('/routes');
        }

        var routePath;

        uiGmapGoogleMapApi.then(function(maps) {
            uiGmapIsReady.promise(1).then(function (instances) {
                var map = instances[0].map;
                routePath = new maps.Polyline({
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    map: map
                });

                $http.get('/api/v1/protected/route?id_route='+vm.id_route)
                    .success(function(data) {
                        vm.route = data.route;

                        if (!vm.route.route) {
                            alert("De route kan niet worden weergegeven op de kaart.\nMogelijk is deze rit handmatig ingevoerd.");
                            return;
                        }

                        routePath.setPath(vm.route.route);
                        console.log(vm.route.route);
                        vm.map.center.latitude = vm.route.route[0].lat;
                        vm.map.center.longitude = vm.route.route[0].lng;
                        map.setZoom(14);

                        var startMarker = new maps.Marker({
                            position: vm.route.route[0],
                            map: map,
                            title: 'Start'
                        });
                    })
                    .error(function(data) {
                        console.log(data);
                        alert("Er is een fout opgetreden tijdens het laden van de rit gegevens.");
                    });
            });
        });

        vm.back = function() {
            $location.path('/routes');
        };
    }
})();