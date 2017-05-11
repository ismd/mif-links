'use strict';

window.mainModule.controller('AdminCtrl', ['$scope', 'Link', function($scope, Link) {
    const LINKS_PER_PAGE = 20;

    $scope.link = '';
    $scope.links = [];
    $scope.loading = true;
    $scope.lastLink = null;
    $scope.pager = {
        from: 0,
        to: LINKS_PER_PAGE,
        count: null
    };

    fetchLinks();

    $scope.generateLink = function(link) {
        $scope.loading = true;

        Link.generateLink(link).then(function(data) {
            if ($scope.pager.to < LINKS_PER_PAGE) {
                $scope.pager.to++;
            }

            $scope.link = '';
            $scope.lastLink = data.info;

            fetchLinks();
        });
    };

    $scope.regenerateLink = function(lastLink) {
        $scope.loading = true;

        Link.regenerateLink(lastLink.id).then(function(data) {
            $scope.lastLink.shortLink = data.shortLink;
            fetchLinks();
        });
    };

    $scope.previousPage = function() {
        if ($scope.pager.from - LINKS_PER_PAGE < 0) {
            return;
        }

        $scope.pager.from -= LINKS_PER_PAGE;
        $scope.pager.to = $scope.pager.from + LINKS_PER_PAGE;

        fetchLinks();
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

        fetchLinks();
    };

    function fetchLinks() {
        return Link.fetchLinks($scope.pager.from, $scope.pager.to).then(function(data) {
            $scope.links = data.links;

            if ($scope.pager.to > data.count) {
                $scope.pager.to = data.count;
            }

            $scope.pager.count = data.count;
            $scope.loading = false;
        });
    }
}]);
