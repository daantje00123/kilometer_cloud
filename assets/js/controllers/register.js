(function() {
    angular.module('kmApp')
        .controller('registerController', registerController);

    registerController.$inject = ['$http', '$location'];
    function registerController($http, $location) {
        var vm = this;

        vm.user = {};

        vm.doRegister = function() {
            if (
                !vm.user.username ||
                !vm.user.email ||
                !vm.user.password1 ||
                !vm.user.password2 ||
                !vm.user.firstname ||
                !vm.user.lastname
            ) {
                alert("Vul alle verplichten velden in.");
                return;
            }

            if (vm.password1 != vm.password2) {
                alert("De wachtwoorden moeten overeen komen.");
                return;
            }

            $http.post('/api/v1/auth/register', vm.user)
                .success(function(data) {
                    alert("Uw account is aangemaakt.\nEr is een activatie email verzonden.");
                    $location.path('/');
                })
                .error(function(data) {
                    console.log(data);
                    alert("Er is een fout opgetreden tijdens het aanmaken van uw account.");
                });
        };

        vm.gotoLogin = function() {
            $location.path('/login');
        };
    }
})();
