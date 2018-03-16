'use strict';

window.mainModule.controller('StatCtrl', ['$scope', '$routeParams', '$q', 'Stat', 'Link', 'Group', function($scope, $routeParams, $q, Stat, Link, Group) {
    $scope.idLink = $routeParams.id;
    $scope.info = null;
    $scope.group = {
        editEnabled: false,
        id: null
    };
    $scope.groups = null;

    Group.fetchGroups().then(function(data) {
        $scope.groups = data.items;
    });

    fetchLink();

    $scope.fetchStat = function(from, to) {
        var defer = $q.defer();

        Stat.fetchStat($scope.idLink, from, to).then(function(data) {
            defer.resolve(data);
        }, function() {
            defer.reject();
        });

        return defer.promise;
    };

    $scope.groupChanged = function(groupId) {
        Link.editLink({
            id: $scope.info.id,
            groupId: groupId
        }, $scope.info.short_link).then(function() {
            fetchLink();
        });
    };

    function fetchLink() {
        Link.fetchLinkById($scope.idLink).then(function(data) {
            $scope.info = data;
            $scope.group.id = $scope.info.group_id;
            $scope.group.editEnabled = false;
        });
    }
}]);
