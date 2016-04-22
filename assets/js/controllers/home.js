(function() {
    angular.module('kmApp')
        .controller('homeController', homeController);

    function homeController($location) {
        var vm = this;

        vm.start = function() {
            $location.path('/start');
        };

        vm.history = function() {
            $location.path('/routes');
        };

        vm.logout = function() {
            $location.path('/logout');
        };
    }
})();