'use strict';

(function() {
    window.mainModule.factory('Link', ['$q', '$http', function($q, $http) {
        var service = {};

        service.generateLink = function(link) {
            var defer = $q.defer();

            $http.post('/api/links/add', {
                link: link
            }).then(function(result) {
                if (result.data.result != 'ok') {
                    defer.reject(result.data);

                    if (result.data.result != 'duplicate') {
                        alert('Не удалось сгенерировать ссылку');
                    }

                    return;
                }

                defer.resolve(result.data);
            }, function() {
                defer.reject();
                alert('Не удалось сгенерировать ссылку');
            });

            return defer.promise;
        };

        service.fetchLinks = function(from, to) {
            var defer = $q.defer();

            $http.get('/api/links/list/' + from + '-' + to).then(function(result) {
                defer.resolve(result.data);
            }, function() {
                defer.reject();
                alert('Не удалось получить ссылки');
            });

            return defer.promise;
        };

        service.regenerateLink = function(id) {
            var defer = $q.defer();

            $http.get('/api/links/regenerate/' + id).then(function(result) {
                defer.resolve(result.data);
            }, function() {
                defer.reject();
                alert('Не удалось перегенерировать ссылку');
            });

            return defer.promise;
        };

        return service;
    }]);
})();
