'use strict';

window.mainModule.controller('GroupInfoCtrl', ['$scope', '$routeParams', '$q', 'Group', 'Link', function($scope, $routeParams, $q, Group, Link) {

    $scope.idGroup = $routeParams.id;
    $scope.groupInfo = null;

    Group.fetchGroupById($scope.idGroup).then(function(data) {
        $scope.groupInfo = data;
    });

    $scope.fetchLinks = function(from, to) {
        var defer = $q.defer();

        Link.fetchLinks(from, to, $scope.idGroup).then(function(data) {
            defer.resolve(data);
        });

        return defer.promise;
    };

    $scope.searchLinks = Link.search;
}]);
