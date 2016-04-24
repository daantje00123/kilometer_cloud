(function() {
    angular.module('kmApp')
        .controller('homeController', homeController);

    homeController.$inject = ['$location'];
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