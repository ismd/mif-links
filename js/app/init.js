'use strict';

window.mainModule = angular.module('main', ['ngRoute'])
    .config(['$routeProvider', '$locationProvider', '$httpProvider', function($routeProvider, $locationProvider, $httpProvider) {
        $locationProvider.html5Mode(true);

        $routeProvider
            .when('/', {
                templateUrl: '/partial/index/index'
            })
            .when('/admin', {
                controller: 'AdminCtrl',
                templateUrl: '/partial/admin/index'
            })
            .otherwise({
                redirectTo: '/'
            });

        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
        $httpProvider.defaults.transformRequest = function(data) {
            if (data === undefined) {
                return data;
            }

            return $.param(data);
        };
    }]);
