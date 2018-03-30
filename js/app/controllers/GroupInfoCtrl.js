'use strict';

module.exports = ['$scope', '$routeParams', 'Group', 'Link', function($scope, $routeParams, Group, Link) {
    $scope.idGroup = $routeParams.id;
    $scope.groupInfo = null;
    $scope.edit = {
        title: false,
        loading: false,
        values: {
            id: $scope.idGroup,
            title: null
        }
    };

    $scope.editGroup = function(group) {
        $scope.edit.loading = true;

        Group.editGroup(group).then(function() {
            $scope.groupInfo.title = group.title;
            $scope.edit.title = false;
        }).finally(function() {
            $scope.edit.loading = false;
        });
    };

    Group.fetchGroupById($scope.idGroup).then(function(data) {
        $scope.groupInfo = data;
        $scope.edit.values.title = $scope.groupInfo.title;
    });

    $scope.fetchLinks = function(from, to, period) {
        return Link.fetchLinks(from, to, $scope.idGroup, period);
    };

    $scope.searchLinks = function(search, idSearchRequest) {
        return Link.searchByGroup(search, $scope.idGroup, idSearchRequest);
    };

    $scope.fetchVisitsByGroup = function(period) {
        return Group.fetchVisitsById($scope.idGroup, period);
    };
}];
