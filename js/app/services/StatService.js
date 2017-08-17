'use strict';

(function() {
    window.mainModule.factory('Stat', ['$q', '$http', function($q, $http) {
        var service = {};

        service.fetchInfo = function(idLink) {
            var defer = $q.defer();

            $http.get('/api/stat/info/' + idLink + '/' + from + '-' + to).then(function(result) {
                result.data.stat.forEach(function(item) {
                    item.visited = new Date(item.visited);
                });

                defer.resolve(result.data);
            }, function() {
                defer.reject();
                alert('Не удалось получить статистику');
            });

            return defer.promise;
        };

        service.fetchStat = function(idLink, from, to) {
            var defer = $q.defer();

            $http.get('/api/stat/fetch/' + idLink + '/' + from + '-' + to).then(function(result) {
                result.data.stat.forEach(function(item) {
                    item.visited = new Date(item.visited);
                });

                defer.resolve(result.data);
            }, function() {
                defer.reject();
                alert('Не удалось получить статистику');
            });

            return defer.promise;
        };

        return service;
    }]);
})();
