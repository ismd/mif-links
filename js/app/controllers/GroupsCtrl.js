'use strict';

window.mainModule.controller('GroupsCtrl', ['$scope', '$timeout', 'Group', function($scope, $timeout, Group) {
    const GROUPS_PER_PAGE = 20;

    $scope.group = '';
    $scope.groups = [];
    $scope.loading = true;
    $scope.pager = {
        from: 0,
        to: GROUPS_PER_PAGE,
        count: null
    };
    $scope.searchText = '';
    $scope.searchActive = false;
    $scope.groupExists = false;

    fetchGroups();

    $scope.addGroup = function(title) {
        $scope.loading = true;

        Group.addGroup(title).then(function(data) {
            if ($scope.pager.to < GROUPS_PER_PAGE) {
                $scope.pager.to++;
            }

            $scope.group = '';
            fetchGroups();
        }, function(data) {
            if (data.result === 'duplicate') {
                $scope.groupExists = true;
                $scope.loading = false;
            }
        });
    };

    $scope.previousPage = function() {
        if ($scope.pager.from - GROUPS_PER_PAGE < 0) {
            return;
        }

        $scope.pager.from -= GROUPS_PER_PAGE;
        $scope.pager.to = $scope.pager.from + GROUPS_PER_PAGE;

        fetchGroups();
    };

    $scope.nextPage = function() {
        if ($scope.pager.from + GROUPS_PER_PAGE > $scope.pager.count) {
            return;
        }

        $scope.pager.from += GROUPS_PER_PAGE;
        $scope.pager.to = $scope.pager.from + GROUPS_PER_PAGE;

        if ($scope.pager.to > $scope.pager.count) {
            $scope.pager.to = $scope.pager.count;
        }

        fetchGroups();
    };

    var searchTimer = null,
        idSearchRequest = 0;

    $scope.searchChanged = function() {
        $scope.loading = true;

        if (++idSearchRequest > 10000) {
            idSearchRequest = 0;
        }

        try {
            $timeout.cancel(searchTimer);
        } catch (err) {
        }

        searchTimer = $timeout(function() {
            searchGroup($scope.searchText);
        }, 500);
    };

    $scope.$watch('group', function() {
        $scope.groupExists = false;
    });

    function searchGroup(search) {
        if (!search) {
            $scope.searchActive = false;
            $scope.searchGroups = undefined;
            $scope.loading = false;
            return;
        }

        Group.search(search, idSearchRequest).then(function(data) {
            if (data.idSearchRequest != idSearchRequest) {
                return;
            }

            $scope.searchActive = true;
            $scope.searchGroups = data.groups;
            $scope.loading = false;
        });
    }

    function fetchGroups() {
        return Group.fetchGroups($scope.pager.from, $scope.pager.to).then(function(data) {
            $scope.groups = data.groups;

            if ($scope.pager.to > data.count) {
                $scope.pager.to = data.count;
            }

            $scope.pager.count = data.count;
            $scope.loading = false;
        });
    }
}]);
