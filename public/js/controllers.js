var appControllers = angular.module('appControllers', []);

appControllers.controller('IndexCtrl', function ($scope, $routeParams, $location) {
    if (!window.admin) {
        $location.path('login');
    }
}).controller('LoginCtrl', ['$scope', '$routeParams', '$location', '$http', '$rootScope', function ($scope, $routeParams, $location, $http, $rootScope) {
    $scope.login = function (admin) {
        $http({
            url: 'admin/login',
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(admin)
        }).success(function (d) {
            if (d == 0) {
                alert("Authenicate error!");
            } else {
                window.admin = d;
                $rootScope.admin = d;
                $location.path('instance');
            }
        });
    }
}]).controller('InstanceCtrl', ['$scope', '$http', '$location', function ($scope, $http, $location) {
    if (!window.admin) {
        $location.path('login');
        return;
    }
    $scope.instances = [];
    $scope.loadInstance = function(){
    	$http.get('instance.json').success(function (data) {
	        $scope.instances = data;
	        $scope.check();
	    });
    }
    
    $http.get('user.json').success(function (d) {
        $scope.users = d;
    });
    $scope.check = function () {
        for (var i in $scope.instances) {
            $http({
                url: 'instance/status.json?uuid=' + $scope.instances[i].uuid,
                method: 'GET',
                async: true,
                dataType: 'json'
            }).success(

            function (d) {
                for (var j in $scope.instances) {
                    if ($scope.instances[j].uuid == d.uuid) $scope.instances[j].status = d.status;
                }
                // $scope.instances[i].status
                // = d
            });
        }
    }
    $scope.loadInstance();
    var p = setInterval($scope.check, 5000);
    $("nav li").removeClass("active");
    $("nav li").find("[href='#/instance']").parent().addClass("active");
    $scope.create = function (instance) {
        $http({
            url: 'instance',
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(instance),

        }).success(function (data) {
            $('#create').modal('hide');
            $scope.loadInstance();
        });
    };
    $scope.start = function (instance) {
        $http.get('instance/start?id=' + instance.id);
    };
    $scope.shutdown = function (instance) {
        $http.get('instance/shutdown?id=' + instance.id);
    };
}]).controller('UserCtrl', ['$scope', '$http', '$location', function ($scope, $http, $location) {

    if (!window.admin) {
        $location.path('login');
        return;
    }
    $http.get('user.json').success(function (data) {
        $scope.users = data;
    });

    $("nav li").removeClass("active");
    $("nav li").find("[href='#/user']").parent().addClass("active");
}]);

appControllers.controller('ClientLoginCtrl', ['$scope', '$http', '$location', function ($scope, $http, $location) {
    $scope.login = function (user) {
        $http({
            url: 'client/login',
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            data: $.param(user),

        }).success(function (data) {
            if (data == 0) {
                alert("Login Failed!");
            } else {
                window.user = (data.user);
                window.vms = data.vms;
                $location.path('/client/main');
            }
        });
    };
    $scope.rdp_login = function (d) {
        proxy.rdplogin(d.ip, d.username, d.password);
    }
}]).controller('ClientMainCtrl', ['$scope', '$http', '$location', function ($scope, $http, $location) {
    $scope.vms = window.vms;
    $scope.check = function () {
        for (var i in $scope.vms) {
            $http({
                url: 'instance/status.json?uuid=' + $scope.vms[i].uuid,
                method: 'GET',
                async: true,
                dataType: 'json'
            }).success(function (d) {
                for (var j in $scope.vms) {
                    if ($scope.vms[j].uuid == d.uuid) $scope.vms[j].status = d.status;
                }
            });
        }
    };
    $scope.refresh = function () {
        $http({
            url: 'client/myvms?uid=' + window.user.id,
            method: 'GET',
            dataType: 'json'
        }).success(function (data) {
            $scope.vms = data;
        });
    };
    $scope.check();
    var p = setInterval($scope.check, 5101);
    var p1 = setInterval($scope.refresh, 60001);
    $scope.open = function (vm) {
        proxy.open(vm.spiceport);
    };
    $scope.start = function (instance) {
        $http.get('instance/start?id=' + instance.id);
    };
    $scope.shutdown = function (instance) {
        $http.get('instance/shutdown?id=' + instance.id);
    };
}]);