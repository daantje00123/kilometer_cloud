(function() {
    angular.module('kmApp', ['ngRoute', 'uiGmapgoogle-maps'])
        .config(config);

    function config($routeProvider, $locationProvider, uiGmapGoogleMapApiProvider) {
        $routeProvider
            .when('/', {
                templateUrl: 'assets/views/home.html',
                controller: 'homeController',
                controllerAs: 'ctrl'
            })
            .when('/start', {
                templateUrl: 'assets/views/start.html',
                controller: 'startController',
                controllerAs: 'ctrl'
            });

        $locationProvider.html5Mode(true);

        uiGmapGoogleMapApiProvider.configure({
            key: 'AIzaSyC83qOwsQ6dCRP9TlWoklYl2k63LN3zdLI'
        });
    }
})();