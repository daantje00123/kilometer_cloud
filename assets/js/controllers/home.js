(function() {
    angular.module('kmApp')
        .controller('homeController', homeController);

    function homeController($location) {
        var vm = this;

        vm.start = function() {
            $location.path('/start');
        }
    }
})();