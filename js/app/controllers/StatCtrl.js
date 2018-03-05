'use strict';

window.mainModule.controller('StatCtrl', ['$scope', '$routeParams', '$q', 'Stat', 'Link', function($scope, $routeParams, $q, Stat, Link) {
    $scope.idLink = $routeParams.id;
    $scope.info = null;

    Link.fetchLinkById($scope.idLink).then(function(data) {
        $scope.info = data;
    });

    $scope.fetchStat = function(from, to) {
        var defer = $q.defer();

        Stat.fetchStat($scope.idLink, from, to).then(function(data) {
            defer.resolve(data);
        }, function() {
            defer.reject();
        });

        return defer.promise;
    };
}]);
