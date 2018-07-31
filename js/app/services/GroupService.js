'use strict';

module.exports = ['$q', '$http', function($q, $http) {
    this.addGroup = function(title) {
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

    this.editGroup = function(group) {
        var defer = $q.defer();

        $http.post('/api/groups/edit', {
            id: group.id,
            title: group.title
        }).then(function(result) {
            if (result.data.result != 'ok') {
                if (result.data.result != 'duplicate') {
                    alert('Не удалось изменить группу');
                }

                defer.reject(result.data);
                return;
            }

            defer.resolve();
        }, function() {
            alert('Не удалось изменить группу');
            defer.reject();
        });

        return defer.promise;
    };

    this.removeGroup = function(id) {
        var defer = $q.defer();

        $http.post('/api/groups/remove', {
            id: id
        }).then(function(result) {
            if (result.data.result != 'ok') {
                defer.reject(result.data);
                return;
            }

            defer.resolve();
        }, function() {
            alert('Не удалось удалить группу');
            defer.reject();
        });

        return defer.promise;
    };

    this.fetchGroups = function(from, to) {
        var defer = $q.defer();

        var url = '/api/groups/list/';
        if (typeof from != 'undefined' && typeof to != 'undefined') {
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

    this.fetchGroupById = function(id, period) {
        var defer = $q.defer();

        var url = '/api/groups/get/' + id;
        if (period) {
            url += '/' + period;
        }

        $http.get(url).then(function(result) {
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

    this.fetchVisitsById = function(id, period) {
        var defer = $q.defer();

        var url = '/api/stat/fetch-by-group/' + id;
        if (period) {
            url += '/' + period;
        }

        $http.get(url).then(function(result) {
            defer.resolve(result.data);
        }, function() {
            alert('Не удалось получить посещения для группы');
            defer.reject();
        });

        return defer.promise;
    };

    this.search = function(search, idSearchRequest) {
        var defer = $q.defer();

        $http.post('/api/groups/search', {
            search: search
        }).then(function(result) {
            result.data.idSearchRequest = idSearchRequest;
            defer.resolve(result.data);
        }, function() {
            alert('Не удалось осуществить поиск');
            defer.reject();
        });

        return defer.promise;
    };
}];
