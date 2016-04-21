(function() {
    angular.module('kmApp', ['ngRoute', 'uiGmapgoogle-maps', 'ngStorage'])
        .config(config)
        .factory("authFactory", authFactory)
        .factory("httpRequestInterceptor", httpRequestInterceptor)
        .run(function($q, $http, $location, authFactory, $interval) {
            $http.get('/api/v1/protected/ping').success(function(data) {
                authFactory.setLoggedin(true);
                authFactory.setJwt(data.jwt);
            }).error(function() {
                authFactory.setLoggedin(false);
                authFactory.deleteJwt();
                $location.path('/login');
            });

            $interval(function() {
                $http.get('/api/v1/protected/ping').success(function(data) {
                    authFactory.setLoggedin(true);
                    authFactory.setJwt(data.jwt);
                }).error(function() {
                    authFactory.setLoggedin(false);
                    authFactory.deleteJwt();
                    $location.path('/login');
                });
            }, 30000);
        });

    var checkLoggedin = function($q, $http, $location, authFactory) {
        var deffered = $q.defer();

        $http.get('/api/v1/protected/ping').success(function(data) {
            authFactory.setLoggedin(true);
            authFactory.setJwt(data.jwt);
            deffered.resolve();
        }).error(function() {
            authFactory.setLoggedin(false);
            authFactory.deleteJwt();
            deffered.reject();
            $location.path('/login');
        });

        return deffered.promise;
    };

    function config($httpProvider, $routeProvider, $locationProvider, uiGmapGoogleMapApiProvider) {
        $httpProvider.interceptors.push('httpRequestInterceptor');

        $routeProvider
            .when('/login', {
                templateUrl: 'assets/views/login.html',
                controller: 'loginController',
                controllerAs: 'vm'
            })
            .when('/', {
                templateUrl: 'assets/views/home.html',
                controller: 'homeController',
                controllerAs: 'ctrl',
                resolve: {
                    loggedin: checkLoggedin
                }
            })
            .when('/start', {
                templateUrl: 'assets/views/start.html',
                controller: 'startController',
                controllerAs: 'ctrl',
                resolve: {
                    loggedin: checkLoggedin
                }
            })
            .when('/logout', {
                templateUrl: 'assets/views/logout.html',
                controller: 'logoutController',
                controllerAs: 'vm'
            });
        $locationProvider.html5Mode(true);

        uiGmapGoogleMapApiProvider.configure({
            key: 'AIzaSyC83qOwsQ6dCRP9TlWoklYl2k63LN3zdLI'
        });
    }

    function authFactory($sessionStorage) {
        var factory = {
            loggedin: false,
            id_user: 0,
            setLoggedin: function(status) {
                factory.loggedin = status;
            },
            setJwt: function(jwt) {
                $sessionStorage.jwt = jwt;
            },
            getJwt: function() {
                return $sessionStorage.jwt;
            },
            setUserId: function(id_user) {
                factory.id_user = id_user;
            },
            getUserId: function() {
                return factory.id_user;
            },
            deleteJwt: function() {
                $sessionStorage.jwt = undefined;
            }
        };

        return factory;
    }

    function httpRequestInterceptor(authFactory) {
        return {
            request: function (config) {
                if (authFactory.getJwt()) {
                    if (config.headers) {
                        config.headers['Authorization'] = 'Bearer ' + authFactory.getJwt();
                    } else {
                        config.headers = {Authorization: 'Bearer ' + authFactory.getJwt()};
                    }
                }
                return config;
            }
        };
    }
})();