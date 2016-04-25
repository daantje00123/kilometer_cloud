(function() {
    angular.module('kmApp')
        .controller('homeController', homeController);

    homeController.$inject = ['$location', 'authFactory'];
    function homeController($location, authFactory) {
        var vm = this;

        vm.user = authFactory.getUserData();

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