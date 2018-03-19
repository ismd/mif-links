'use strict';

(function() {
    window.mainModule.controller('GroupInfoCtrl', ['$scope', '$routeParams', 'Group', 'Link', function($scope, $routeParams, Group, Link) {

        $scope.idGroup = $routeParams.id;
        $scope.groupInfo = null;

        Group.fetchGroupById($scope.idGroup).then(function(data) {
            $scope.groupInfo = data;
        });

        $scope.fetchLinks = function(from, to) {
            return Link.fetchLinks(from, to, $scope.idGroup);
        };

        $scope.searchLinks = function(search, idSearchRequest) {
            return Link.searchByGroup(search, $scope.idGroup, idSearchRequest);
        };

        $scope.fetchVisitsByGroup = function(from, to) {
            return Group.fetchVisitsById($scope.idGroup, from, to);
        };
    }]);
})();
