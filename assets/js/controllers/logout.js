(function() {
    angular.module('kmApp')
        .controller('logoutController', logoutController);

    function logoutController($location, authFactory) {
        var vm = this;

        authFactory.deleteJwt();
        $location.path('/login');
    }
})();