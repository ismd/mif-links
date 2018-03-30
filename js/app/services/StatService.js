'use strict';

module.exports = ['$q', '$http', function($q, $http) {
    this.fetchStat = function(idLink, from, to, period) {
        var defer = $q.defer();

        var url = '/api/stat/fetch/' + idLink + '/' + from + '-' + to;
        if (period) {
            url += '/' + period;
        }

        $http.get(url).then(function(result) {
            result.data.items.forEach(function(item) {
                item.visited = new Date(item.visited);
            });

            defer.resolve(result.data);
        }, function() {
            defer.reject();
            alert('Не удалось получить статистику');
        });

        return defer.promise;
    };
}];
