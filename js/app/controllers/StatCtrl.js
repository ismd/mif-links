'use strict';

(function() {
    window.mainModule.controller('StatCtrl', ['$scope', '$routeParams', 'Stat', 'Link', 'Group', function($scope, $routeParams, Stat, Link, Group) {
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
            return Stat.fetchStat($scope.idLink, from, to);
        };

        $scope.groupChanged = function(groupId) {
            Link.editLink({
                id: $scope.info.id,
                groupId: groupId
            }, $scope.info.short_link).then(function() {
                fetchLink();
            });
        };

        $scope.fetchVisitsByLink = function(from, to) {
            return Link.fetchVisitsById($scope.idLink, from, to);
        };

        function fetchLink() {
            Link.fetchLinkById($scope.idLink).then(function(data) {
                $scope.info = data;
                $scope.group.id = $scope.info.group_id;
                $scope.group.editEnabled = false;
            });
        }
    }]);
})();
