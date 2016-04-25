"use strict";angular.module("ngLocale",[],["$provide",function(e){function r(e){e+="";var r=e.indexOf(".");return-1==r?0:e.length-r-1}function a(e,a){var n=a;void 0===n&&(n=Math.min(r(e),3));var i=Math.pow(10,n),m=(e*i|0)%i;return{v:n,f:m}}var n={ZERO:"zero",ONE:"one",TWO:"two",FEW:"few",MANY:"many",OTHER:"other"};e.value("$locale",{DATETIME_FORMATS:{AMPMS:["a.m.","p.m."],DAY:["zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag"],ERANAMES:["voor Christus","na Christus"],ERAS:["v.Chr.","n.Chr."],FIRSTDAYOFWEEK:0,MONTH:["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],SHORTDAY:["zo","ma","di","wo","do","vr","za"],SHORTMONTH:["jan.","feb.","mrt.","apr.","mei","jun.","jul.","aug.","sep.","okt.","nov.","dec."],STANDALONEMONTH:["Januari","Februari","Maart","April","Mei","Juni","Juli","Augustus","September","Oktober","November","December"],WEEKENDRANGE:[5,6],fullDate:"EEEE d MMMM y",longDate:"d MMMM y",medium:"d MMM y HH:mm:ss",mediumDate:"d MMM y",mediumTime:"HH:mm:ss","short":"dd-MM-yy HH:mm",shortDate:"dd-MM-yy",shortTime:"HH:mm"},NUMBER_FORMATS:{CURRENCY_SYM:"€",DECIMAL_SEP:",",GROUP_SEP:".",PATTERNS:[{gSize:3,lgSize:3,maxFrac:3,minFrac:0,minInt:1,negPre:"-",negSuf:"",posPre:"",posSuf:""},{gSize:3,lgSize:3,maxFrac:2,minFrac:2,minInt:1,negPre:"¤ -",negSuf:"",posPre:"¤ ",posSuf:""}]},id:"nl-nl",localeID:"nl_NL",pluralCat:function(e,r){var i=0|e,m=a(e,r);return 1==i&&0==m.v?n.ONE:n.OTHER}})}]);
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
})();
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
(function() {
    angular.module('kmApp')
        .controller('loginController', loginController);

    loginController.$inject = ['$http', 'authFactory', '$location'];
    function loginController($http, authFactory, $location) {
        var vm = this;
        vm.username = "";
        vm.password = "";

        vm.doLogin = function() {
            if (!vm.username || !vm.password) {
                alert("Zorg ervoor dat de gebruikersnaam en het wachtwoord zijn ingevuld.");
                return;
            }

            $http({
                method: "POST",
                url: "/api/v1/auth/login",
                data: {
                    username: vm.username,
                    password: vm.password
                }
            })
                .success(function(data) {
                    authFactory.setJwt(data.jwt);
                    authFactory.setUserId(data.id_user);
                    $location.path('/');
                })
                .error(function(data) {
                    alert(data.message);
                });
        }
    }
})();

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
(function() {
    angular.module('kmApp')
        .controller('routesController', routesController);

    routesController.$inject = ['$http', '$location', '$filter', 'authFactory'];
    function routesController($http, $location, $filter, authFactory) {
        var vm = this;

        vm.user = authFactory.getUserData();

        vm.routes = [];
        vm.checked = [];
        vm.totals = [];

        var date = new Date();

        vm.filter = {
            month: (date.getMonth()+1).toString(),
            year: date.getFullYear().toString(),
            paid: "0"
        };

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

        vm.filterSubmit = function() {
            getResultsPage(1);
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
                    vm.selectedAll = false;
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
                    vm.selectedAll = false;
                })
                .error(function(data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het verwijderen van de ritten.");
                });
        };

        function getResultsPage(pageNumber) {
            $http.get('/api/v1/protected/routes?page='+pageNumber+'&month='+vm.filter.month+'&year='+vm.filter.year+'&paid='+vm.filter.paid)
                .success(function(data) {
                    vm.routes = data.routes;
                    vm.totals = data.totals;
                    vm.totalRoutes = data.totals.count;
                    vm.filter = data.filter;
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
(function() {
    angular.module('kmApp')
        .controller('routesEditController', routesEditController);

    routesEditController.$inject = ['$routeParams', '$location', '$http', 'authFactory'];
    function routesEditController($routeParams, $location, $http, authFactory) {
        var vm = this;

        vm.user = authFactory.getUserData();

        vm.id_route = $routeParams.id;
        vm.description = "";
        vm.paid = 0;

        if (!vm.id_route) {
            $location.path('/routes');
        }
        
        $http.get('/api/v1/protected/route?id_route='+vm.id_route)
            .success(function(data) {
                vm.description = data.route.omschrijving;
                vm.paid = data.route.betaald;
            })
            .error(function(data) {
                console.log(data);
                alert("Er is een fout opgetreden tijdens het laden van de rit gegevens.");
            });

        vm.save = function() {
            $http.put('/api/v1/protected/route', {
                id_route: vm.id_route,
                description: vm.description,
                paid: vm.paid
            })
                .success(function(data) {
                    $location.path('/routes');
                })
                .error(function(data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het opslaan van de rit.");
                });
        };

        vm.back = function() {
            $location.path('/routes');
        }
    }
})();
(function() {
    angular.module('kmApp')
        .controller('routesViewController', routesViewController);

    routesViewController.$inject = ['$http', '$location', '$routeParams', 'uiGmapGoogleMapApi', 'uiGmapIsReady', 'authFactory'];
    function routesViewController($http, $location, $routeParams, uiGmapGoogleMapApi, uiGmapIsReady, authFactory) {
        var vm = this;

        vm.user = authFactory.getUserData();

        vm.id_route = $routeParams.id;
        vm.route = {};
        vm.map = {
            center: {
                latitude: 52.092876,
                longitude: 5.104480
            },
            zoom: 8,
            control: {}
        };

        if (!vm.id_route) {
            $location.path('/routes');
        }

        var routePath;

        uiGmapGoogleMapApi.then(function(maps) {
            uiGmapIsReady.promise(1).then(function (instances) {
                var map = instances[0].map;
                routePath = new maps.Polyline({
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    map: map
                });

                $http.get('/api/v1/protected/route?id_route='+vm.id_route)
                    .success(function(data) {
                        vm.route = data.route;

                        if (!vm.route.route) {
                            alert("De route kan niet worden weergegeven op de kaart.\nMogelijk is deze rit handmatig ingevoerd.");
                            return;
                        }

                        routePath.setPath(vm.route.route);
                        console.log(vm.route.route);
                        vm.map.center.latitude = vm.route.route[0].lat;
                        vm.map.center.longitude = vm.route.route[0].lng;
                        map.setZoom(14);

                        var startMarker = new maps.Marker({
                            position: vm.route.route[0],
                            map: map,
                            title: 'Start'
                        });
                    })
                    .error(function(data) {
                        console.log(data);
                        alert("Er is een fout opgetreden tijdens het laden van de rit gegevens.");
                    });
            });
        });

        vm.back = function() {
            $location.path('/routes');
        };
    }
})();
(function() {
    angular.module('kmApp')
        .controller('startController', startController);

    startController.$inject = ['uiGmapGoogleMapApi', 'uiGmapIsReady', '$location', '$http', '$httpParamSerializer', 'authFactory'];
    function startController(uiGmapGoogleMapApi, uiGmapIsReady, $location, $http, $httpParamSerializer, authFactory) {
        var vm = this;

        vm.user = authFactory.getUserData();

        vm.map = {
            center: {
                latitude: 52.092876,
                longitude: 5.104480
            },
            zoom: 8,
            control: {}
        };

        vm.speed = 0;

        var path = [];
        vm.distance = 0;
        var startDate = new Date();
        startDate = startDate.getFullYear()+'-'+(startDate.getMonth()+1)+'-'+startDate.getDate()+' '+startDate.getHours()+':'+startDate.getMinutes()+":"+startDate.getSeconds();

        console.log("Start datetime: "+startDate);

        if (!navigator.geolocation) {
            alert("Uw browser wordt niet ondersteund!");
            return;
        }

        uiGmapGoogleMapApi.then(function(maps) {
            uiGmapIsReady.promise(1).then(function(instances) {
                var map = instances[0].map;
                var routePath = new maps.Polyline({
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    map: map
                });

                navigator.geolocation.getCurrentPosition(function(position){
                    var startMarker = new maps.Marker({
                        position: {lat: position.coords.latitude, lng: position.coords.longitude},
                        map: map,
                        title: 'Start'
                    });

                    path.push({lat: position.coords.latitude, lng: position.coords.longitude});
                    routePath.setPath(path);

                    map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
                    map.setZoom(16);
                    var time1 = Date.now()/1000;

                    navigator.geolocation.watchPosition(function(position) {
                        var distance = calculateDistance(path[path.length-1].lat, path[path.length-1].lng, position.coords.latitude, position.coords.longitude)
                        vm.distance = vm.distance + distance;
                        vm.speed = calculateSpeed(time1, Date.now()/1000, distance);

                        path.push({lat: position.coords.latitude, lng: position.coords.longitude});
                        routePath.setPath(path);

                        map.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
                        time1 = Date.now()/1000;
                    }, null, {enableHighAccuracy: true});
                }, null, {enableHighAccuracy: true});
            });
        });

        vm.delete = function() {
            if (!confirm("Weet u zeker dat u de route wilt verwijderen?")) {
                return;
            }

            $location.path('/');
        };

        vm.save = function() {
            if (!confirm("Weet u zeker dat u de route wilt opslaan?")) {
                return;
            }

            if (path.length < 1) {
                alert("De route is nog te kort om op te slaan.");
                return;
            }

            $http({
                method: "POST",
                url: '/api/v1/protected/route',
                data: $httpParamSerializer({
                    kms: vm.distance,
                    route: JSON.stringify(path),
                    start_date: startDate,
                    id_user: authFactory.getUserId()
                }),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(response) {
                alert("De route is opgeslagen!");
                $location.path('/');
            }, function(response) {
                console.log(response);
                alert("Er is iets fout gegaan tijdens het opslaan van de route");
            });
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the earth in km
        var dLat = deg2rad(lat2 - lat1);  // deg2rad below
        var dLon = deg2rad(lon2 - lon1);
        var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2)
            ;
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c; // Distance in km
        if (d > 0.5) {
            return 0;
        }
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180)
    }

    function calculateSpeed(time1, time2, distance) {
        // Convert seconds to hours
        time1 = time1/60/60;
        time2 = time2/60/60;

        return distance / (time2 - time1);
    }
})();