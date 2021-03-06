(function() {
    angular.module('kmApp', ['ngRoute', 'uiGmapgoogle-maps', 'ngStorage', 'angularUtils.directives.dirPagination'])
        .config(config)
        .filter('dateToISO', dateToISO)
        .directive('selectOnClick', ['$window', function ($window) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    element.on('click', function () {
                        if (!$window.getSelection().toString()) {
                            // Required for mobile Safari
                            this.setSelectionRange(0, this.value.length)
                        }
                    });
                }
            };
        }])
        .factory("authFactory", authFactory)
        .factory("httpRequestInterceptor", httpRequestInterceptor)
        .factory('saveRoutePart', saveRoutePart)
        .run(['$q', '$http', '$location', 'authFactory', '$interval', function($q, $http, $location, authFactory, $interval) {
            $http.get('/api/v1/protected/ping').success(function(data) {
                authFactory.setLoggedin(true);

                if (data.jwt) {
                    authFactory.setJwt(data.jwt);
                }

                if (data.id_user) {
                    authFactory.setUserId(data.id_user);
                }

                if (data.user_data) {
                    authFactory.setUserData(data.user_data);
                }
            }).error(function() {
                authFactory.setLoggedin(false);
                authFactory.deleteJwt();
                authFactory.deleteUserData();
                if ($location.path() == "/register") {
                    return;
                }
                $location.path('/login');
            });

            $interval(function() {
                $http.get('/api/v1/protected/ping').success(function(data) {
                    authFactory.setLoggedin(true);

                    if (data.jwt) {
                        authFactory.setJwt(data.jwt);
                    }

                    if (data.id_user) {
                        authFactory.setUserId(data.id_user);
                    }

                    if (data.user_data) {
                        authFactory.setUserData(data.user_data);
                    }
                }).error(function() {
                    authFactory.setLoggedin(false);
                    authFactory.deleteJwt();
                    authFactory.deleteUserData();
                    if ($location.path() == "/register") {
                        return;
                    }
                    $location.path('/login');
                });
            }, 30000);
        }]);

    checkLoggedin.$inject = ['$q', '$http', '$location', 'authFactory'];
    function checkLoggedin($q, $http, $location, authFactory) {
        var deffered = $q.defer();

        $http.get('/api/v1/protected/ping').success(function(data) {
            authFactory.setLoggedin(true);

            if (data.jwt) {
                authFactory.setJwt(data.jwt);
            }

            if (data.id_user) {
                authFactory.setUserId(data.id_user);
            }

            if (data.user_data) {
                authFactory.setUserData(data.user_data);
            }

            deffered.resolve();
        }).error(function() {
            authFactory.setLoggedin(false);
            authFactory.deleteJwt();
            authFactory.deleteUserData();
            deffered.reject();
            if ($location.path() == "/register") {
                return;
            }
            $location.path('/login');
        });

        return deffered.promise;
    }

    config.$inject = ['$httpProvider', '$routeProvider', '$locationProvider', 'uiGmapGoogleMapApiProvider'];
    function config($httpProvider, $routeProvider, $locationProvider, uiGmapGoogleMapApiProvider) {
        $httpProvider.interceptors.push('httpRequestInterceptor');

        $routeProvider
            .when('/login', {
                templateUrl: 'assets/views/login.html',
                controller: 'loginController',
                controllerAs: 'vm'
            })
            .when('/register', {
                templateUrl: 'assets/views/register.html',
                controller: 'registerController',
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
                controllerAs: 'vm',
                resolve: {
                    loggedin: checkLoggedin
                }
            })
            .when('/routes', {
                templateUrl: 'assets/views/routes.html',
                controller: 'routesController',
                controllerAs: 'vm',
                resolve: {
                    loggedin: checkLoggedin
                }
            })
            .when('/routes/edit/:id', {
                templateUrl: 'assets/views/routes/edit.html',
                controller: 'routesEditController',
                controllerAs: 'vm',
                resolve: {
                    loggedin: checkLoggedin
                }
            })
            .when('/routes/view/:id', {
                templateUrl: 'assets/views/routes/view.html',
                controller: 'routesViewController',
                controllerAs: 'vm',
                resolve: {
                    loggedin: checkLoggedin
                }
            });
        $locationProvider.html5Mode(true);

        uiGmapGoogleMapApiProvider.configure({
            key: 'AIzaSyC83qOwsQ6dCRP9TlWoklYl2k63LN3zdLI'
        });
    }

    authFactory.$inject = ['$sessionStorage'];
    function authFactory($sessionStorage) {
        var factory = {
            loggedin: false,
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
                $sessionStorage.id_user = id_user;
            },
            getUserId: function() {
                return $sessionStorage.id_user;
            },
            deleteJwt: function() {
                $sessionStorage.jwt = undefined;
            },
            setUserData: function(user_data) {
                $sessionStorage.user_data = user_data;
            },
            getUserData: function() {
                return $sessionStorage.user_data;
            },
            deleteUserData: function() {
                $sessionStorage.user_data = undefined;
            }
        };

        return factory;
    }

    httpRequestInterceptor.$inject = ['authFactory'];
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

    function dateToISO() {
        return function(input) {
            return input.replace(/(.+) (.+)/, "$1T$2Z");
        }
    }

    saveRoutePart.$inject = ['$http'];
    function saveRoutePart($http) {
        var part = [];
        return function(lat, lng) {
            if (lat === true) {
                if (part.length < 1) {
                    part = [];
                    return $http.get('/api/v1/protected/ping');
                }

                var tmp = part;
                part = [];

                return $http.post('/api/v1/protected/route-part', {
                    'part': tmp
                });
            }

            part.push({lat: lat, lng: lng});
            if (part.length >= 10) {
                $http.post('/api/v1/protected/route-part', {
                    'part': part
                });
                part = [];
            }
        }
    }
})();