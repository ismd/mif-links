'use strict';

(function() {
    window.mainModule.factory('Group', ['$q', '$http', function($q, $http) {
        var service = {};

        service.addGroup = function(title) {
            var defer = $q.defer();

            $http.post('/api/groups/add', {
                title: title
            }).then(function(result) {
                if (result.data.result !== 'ok') {
                    defer.reject(result.data);

                    if (result.data.result !== 'duplicate') {
                        alert('Не удалось создать группу');
                    }

                    return;
                }

                defer.resolve(result.data);
            }, function() {
                alert('Не удалось создать группу');
                defer.reject();
            });

            return defer.promise;
        };

        service.fetchGroups = function(from, to) {
            var defer = $q.defer();

            var url = '/api/groups/list/';
            if (from && to) {
                url += from + '-' + to;
            }

            $http.get(url).then(function(result) {
                defer.resolve(result.data);
            }, function() {
                alert('Не удалось получить группы');
                defer.reject();
            });

            return defer.promise;
        };

        service.fetchGroupById = function(id) {
            var defer = $q.defer();

            $http.get('/api/groups/get/' + id).then(function(result) {
                if (result.data.result == 'ok') {
                    defer.resolve(result.data.group);
                } else {
                    alert('Группа не найдена');
                    defer.reject();
                }
            }, function() {
                alert('Не удалось получить группу');
                defer.reject();
            });

            return defer.promise;
        };

        service.search = function(search, idSearchRequest) {
            var defer = $q.defer();

            $http.post('/api/groups/search', {
                search: search,
                idSearchRequest: idSearchRequest
            }).then(function(result) {
                defer.resolve(result.data);
            }, function() {
                alert('Не удалось осуществить поиск');
                defer.reject();
            });

            return defer.promise;
        };

        return service;
    }]);
})();