(function() {
    angular.module('kmApp')
        .controller('routesEditController', routesEditController);

    routesEditController.$inject = ['$routeParams', '$location', '$http', 'authFactory'];
    function routesEditController($routeParams, $location, $http, authFactory) {
        var vm = this;

        vm.user = authFactory.getUserData();

        vm.id_route = $routeParams.id;
        vm.description = "";
        vm.paid = 0;

        if (!vm.id_route) {
            $location.path('/routes');
        }
        
        $http.get('/api/v1/protected/route?id_route='+vm.id_route)
            .success(function(data) {
                vm.description = data.route.omschrijving;
                vm.paid = data.route.betaald;
            })
            .error(function(data) {
                console.log(data);
                alert("Er is een fout opgetreden tijdens het laden van de rit gegevens.");
            });

        vm.save = function() {
            $http.put('/api/v1/protected/route', {
                id_route: vm.id_route,
                description: vm.description,
                paid: vm.paid
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