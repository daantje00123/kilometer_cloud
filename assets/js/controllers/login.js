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
        };

        vm.gotoRegister = function() {
            $location.path('/register');
        };
    }
})();
