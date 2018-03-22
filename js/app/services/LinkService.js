'use strict';

module.exports = ['$q', '$http', function($q, $http) {
    this.generateLink = function(link, groupId, force) {
        if (typeof force === 'undefined') {
            force = false;
        }

        var defer = $q.defer();

        $http.post('/api/links/add', {
            link: link,
            groupId: groupId ? groupId : null,
            force: force
        }).then(function(result) {
            if (result.data.result !== 'ok') {
                defer.reject(result.data);

                if (result.data.result !== 'duplicate') {
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

    this.editLink = function(link, shortLink) {
        var defer = $q.defer();

        $http.post('/api/links/edit', {
            id: link.id,
            groupId: link.groupId ? link.groupId : null,
            shortLink: shortLink
        }).then(function(result) {
            if (result.data.result !== 'ok') {
                defer.reject(result.data);

                if (result.data.result !== 'duplicate') {
                    alert('Не удалось сгенерировать ссылку');
                }

                return;
            }

            defer.resolve(result.data);
        }, function() {
            defer.reject();
            alert('Не удалось сохранить ссылку');
        });

        return defer.promise;
    };

    this.fetchLinks = function(from, to, groupId) {
        var defer = $q.defer();

        var url = '/api/links/list/' + from + '-' + to;
        if (groupId) {
            url += '/' + groupId;
        }

        $http.get(url).then(function(result) {
            defer.resolve(result.data);
        }, function() {
            defer.reject();
            alert('Не удалось получить ссылки');
        });

        return defer.promise;
    };

    this.regenerateLink = function(id) {
        var defer = $q.defer();

        $http.get('/api/links/regenerate/' + id).then(function(result) {
            defer.resolve(result.data);
        }, function() {
            defer.reject();
            alert('Не удалось перегенерировать ссылку');
        });

        return defer.promise;
    };

    this.search = function(search, idSearchRequest) {
        var defer = $q.defer();

        $http.post('/api/links/search', {
            search: search,
            idSearchRequest: idSearchRequest
        }).then(function(result) {
            defer.resolve(result.data);
        }, function() {
            defer.reject();
            alert('Не удалось осуществить поиск');
        });

        return defer.promise;
    };

    this.searchByGroup = function(search, groupId, idSearchRequest) {
        var defer = $q.defer();

        $http.post('/api/links/search', {
            search: search,
            groupId: groupId,
            idSearchRequest: idSearchRequest
        }).then(function(result) {
            defer.resolve(result.data);
        }, function() {
            defer.reject();
            alert('Не удалось осуществить поиск');
        });

        return defer.promise;
    };

    this.fetchLinkById = function(id) {
        var defer = $q.defer();

        $http.get('/api/links/get/' + id).then(function(result) {
            if (result.data.result == 'ok') {
                defer.resolve(result.data.link);
            } else {
                alert('Ссылка не найдена');
                defer.reject();
            }
        }, function() {
            alert('Не удалось получить ссылку');
            defer.reject();
        });

        return defer.promise;
    };

    this.fetchVisitsById = function(id, from, to) {
        var defer = $q.defer();

        var url = '/api/stat/fetch-by-link/' + id;
        if (from && to) {
            url += '/' + from + '/' + to;
        }

        $http.get(url).then(function(result) {
            defer.resolve(result.data);
        }, function() {
            alert('Не удалось получить посещения для ссылки');
            defer.reject();
        });

        return defer.promise;
    };
}];
