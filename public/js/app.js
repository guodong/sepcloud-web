var app = angular.module('app', [ 'ngRoute', 'appControllers' ]);
app.service('Auth', [ '$rootScope', '$location',
		function($rootScope, $location) {
			var service = {
				isLogin : function() {
					if (!window.admin) {
						$rootScope.$broadcast('admin.update');
						$location.path('login');
					}
				}
			}
			return service;
		} ]);
app.config([ '$routeProvider', function($routeProvider) {
	$routeProvider.when('/', {
		templateUrl : 'tpl/index.html',
		controller : 'IndexCtrl'
	}).when('/login', {
		templateUrl : 'tpl/admin/login.html',
		controller : 'LoginCtrl'
	}).when('/instance', {
		templateUrl : 'tpl/instance.html',
		controller : 'InstanceCtrl'
	}).when('/user', {
		templateUrl : 'tpl/user.html',
		controller : 'UserCtrl'
	}).when('/client', {
		templateUrl : 'tpl/client/login.html',
		controller : 'ClientLoginCtrl'
	}).when('/client/main', {
		templateUrl : 'tpl/client/main.html',
		controller : 'ClientMainCtrl'
	}).otherwise({
		redirectTo : '/'
	});
} ]);