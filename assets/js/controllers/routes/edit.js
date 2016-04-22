(function() {
    angular.module('kmApp')
        .controller('routesEditController', routesEditController);

    function routesEditController($routeParams, $location, $http) {
        var vm = this;
        vm.id_route = $routeParams.id;
        vm.description = "";

        if (!vm.id_route) {
            $location.path('/routes');
        }
        
        $http.get('/api/v1/protected/route?id_route='+vm.id_route)
            .success(function(data) {
                vm.description = data.route.omschrijving;
            })
            .error(function(data) {
                console.log(data);
                alert("Er is een fout opgetreden tijdens het laden van de rit gegevens.");
            });

        vm.save = function() {
            $http.put('/api/v1/protected/route', {
                id_route: vm.id_route,
                description: vm.description
            })
                .success(function(data) {
                    $location.path('/routes');
                })
                .error(function(data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het opslaan van de rit.");
                });
        };

        vm.back = function() {
            $location.path('/routes');
        }
    }
})();