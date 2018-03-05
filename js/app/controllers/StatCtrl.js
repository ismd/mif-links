'use strict';

window.mainModule.controller('StatCtrl', ['$scope', '$window', '$routeParams', 'Stat', function($scope, $window, $routeParams, Stat) {
    const LINKS_PER_PAGE = 20;

    $scope.idLink = $routeParams.id;
    $scope.info = null;
    $scope.stat = [];
    $scope.loading = true;

    $scope.pager = {
        from: 0,
        to: LINKS_PER_PAGE,
        count: null
    };

    fetchStat();

    $scope.previousPage = function() {
        if ($scope.pager.from - LINKS_PER_PAGE < 0) {
            return;
        }

        $scope.pager.from -= LINKS_PER_PAGE;
        $scope.pager.to = $scope.pager.from + LINKS_PER_PAGE;

        fetchStat();
    };

    $scope.nextPage = function() {
        if ($scope.pager.from + LINKS_PER_PAGE > $scope.pager.count) {
            return;
        }

        $scope.pager.from += LINKS_PER_PAGE;
        $scope.pager.to = $scope.pager.from + LINKS_PER_PAGE;

        if ($scope.pager.to > $scope.pager.count) {
            $scope.pager.to = $scope.pager.count;
        }

        fetchStat();
    };

    $scope.back = function() {
        $window.history.back();
    };

    function fetchStat() {
        return Stat.fetchStat($scope.idLink, $scope.pager.from, $scope.pager.to).then(function(data) {
            $scope.info = data.link_info;
            $scope.stat = data.stat;

            if ($scope.pager.to > data.count) {
                $scope.pager.to = data.count;
            }

            $scope.pager.count = data.count;
            $scope.loading = false;
        });
    }
}]);
