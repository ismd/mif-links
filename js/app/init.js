'use strict';

window.mainModule = angular.module('main', ['ngRoute', 'angular-clipboard'])
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

        // Фикс, чтобы копирование работало в модальных окнах
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        // Для Bootstrap 4
        // $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    }]);
