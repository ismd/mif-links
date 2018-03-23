'use strict';

window.$ = require('jquery');
window.jQuery = $;

require('angular');
require('angular-route');
require('angular-clipboard');
require('bootstrap');
require('chart.js');
require('angular-chart.js');
require('dateformat');

angular.module('app', ['ngRoute', 'angular-clipboard', 'chart.js'])
    .config(['$routeProvider', '$locationProvider', '$httpProvider', function($routeProvider, $locationProvider, $httpProvider) {
        $locationProvider.html5Mode(true);

        $routeProvider
            .when('/admin/links', {
                controller: 'LinksCtrl',
                templateUrl: '/partial/links/index',
                reloadOnSearch: false
            })
            .when('/admin/link/:id', {
                controller: 'LinkInfoCtrl',
                templateUrl: '/partial/stat/index',
                reloadOnSearch: false
            })
            .when('/admin/groups', {
                controller: 'GroupsCtrl',
                templateUrl: '/partial/groups/index',
                reloadOnSearch: false
            })
            .when('/admin/group/:id', {
                controller: 'GroupInfoCtrl',
                templateUrl: '/partial/groups/info',
                reloadOnSearch: false
            })
            .otherwise({
                redirectTo: '/admin/links'
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

require('./controllers');
require('./directives');
require('./services');
