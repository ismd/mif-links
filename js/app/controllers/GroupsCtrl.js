'use strict';

(function() {
    window.mainModule.controller('GroupsCtrl', ['$scope', '$timeout', 'Group', function($scope, $timeout, Group) {
        $scope.group = '';
        $scope.groupExists = false;
        $scope.loading = false;

        $scope.addGroup = function(title) {
            $scope.loading = true;

            Group.addGroup(title).then(function(data) {
                $scope.group = '';
                $scope.$broadcast('updateListTable');
            }, function(data) {
                if (data.result === 'duplicate') {
                    $scope.groupExists = true;
                    $scope.loading = false;
                }
            });
        };

        $scope.$watch('group', function() {
            $scope.groupExists = false;
        });

        $scope.fetchGroups = Group.fetchGroups;
        $scope.searchGroups = Group.search;
    }]);
})();
