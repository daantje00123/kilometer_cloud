(function() {
    angular.module('kmApp')
        .controller('routesController', routesController);

    routesController.$inject = ['$http', '$location', '$filter'];
    function routesController($http, $location, $filter) {
        var vm = this;

        vm.routes = [];
        vm.checked = [];
        vm.totals = [];

        vm.totalRoutes = 0;
        vm.routesPerPage = 10;
        vm.pagination = {
            current: 1
        };

        getResultsPage(1);

        vm.back = function() {
            console.log(vm.checked);
            $location.path('/');
        };

        vm.pageChanged = function(newPage) {
            getResultsPage(newPage);
        };

        vm.checkAll = function() {
            if (vm.selectedAll) {
                vm.selectedAll = true;
            } else {
                vm.selectedAll = false;
            }

            angular.forEach(vm.routes, function(item) {
                vm.checked[item.id_route] = vm.selectedAll;
            });
        };

        vm.showAction = function() {
            var trues = $filter("filter")(vm.checked , true);
            return trues.length;
        };

        vm.view = function(route) {
            $location.path('/routes/view/'+route.id_route);
        };

        vm.edit = function(route) {
            $location.path('/routes/edit/'+route.id_route);
        };

        vm.delete = function(route) {
            if (confirm("Weet u zeker dat u de rit wilt verwijderen?")) {
                $http.delete('/api/v1/protected/route?id_route='+route.id_route)
                    .success(function(data) {
                        getResultsPage(vm.pagination.current);
                        alert("De rit is succesvol verwijderd.");
                    })
                    .error(function(data) {
                        console.log(data);
                        alert("Er is een fout opgetreden tijdens het verwijderen van de rit.");
                    });
            }
        };

        vm.batchPay = function(status) {
            var items = getCheckedItems();
            
            $http.put('/api/v1/protected/batch/routes/paid-status', {
                routes: items,
                status: status
            })
                .success(function(data) {
                    getResultsPage(vm.pagination.current);
                    vm.checked = [];
                })
                .error(function(data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het aanpassen van de betaald status.");
                });
        };

        vm.batchDelete = function() {
            if (!confirm("Weet u zeker dat u deze ritten wilt verwijderen?")) {
                vm.checked = [];
                return;
            }

            var items = getCheckedItems();

            $http.delete('/api/v1/protected/batch/routes/delete?routes='+JSON.stringify(items))
                .success(function(data) {
                    getResultsPage(vm.pagination.current);
                })
                .error(function(data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het verwijderen van de ritten.");
                });
        };

        function getResultsPage(pageNumber) {
            $http.get('/api/v1/protected/routes?page='+pageNumber)
                .success(function(data) {
                    vm.routes = data.routes;
                    vm.totals = data.totals;
                    vm.totalRoutes = data.totals.count;
                })
                .error(function(data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het ophalen van de ritten, probeer het later nog een keer.");
                });
        }

        function getCheckedItems() {
            var output = [];

            angular.forEach(vm.checked, function(item, index) {
                if (item === true) {
                    output.push(index);
                }
            });

            return output;
        }
    }
})();