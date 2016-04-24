(function() {
    angular.module('kmApp')
        .controller('logoutController', logoutController);

    logoutController.$inject = ['$location', 'authFactory'];
    function logoutController($location, authFactory) {
        var vm = this;

        authFactory.deleteJwt();
        $location.path('/login');
    }
})();