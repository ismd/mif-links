'use strict';

window.mainModule.controller('AdminCtrl', ['$scope', '$timeout', 'Link', function($scope, $timeout, Link) {
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
    $scope.searchText = '';
    $scope.searchActive = false;

    fetchLinks();

    $scope.generateLink = function(link, force) {
        $scope.loading = true;

        Link.generateLink(link, force).then(function(data) {
            if ($scope.pager.to < LINKS_PER_PAGE) {
                $scope.pager.to++;
            }

            $scope.link = '';
            $scope.lastLink = data.info;

            fetchLinks();
        }, function(data) {
            if (data.result === 'duplicate') {
                $scope.duplicates = data.links;
                $('.js-duplicate-popup').modal();
            }
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

    var searchTimer = null,
        idSearchRequest = 0;

    $scope.searchChanged = function() {
        $scope.loading = true;

        if (++idSearchRequest > 10000) {
            idSearchRequest = 0;
        }

        try {
            searchTimer.cancel();
        } catch (err) {
        }

        searchTimer = $timeout(function() {
            searchLink($scope.searchText);
        }, 500);
    };

    function searchLink(search) {
        if (!search) {
            $scope.searchActive = false;
            $scope.searchLinks = undefined;
            $scope.loading = false;
            return;
        }

        Link.search(search, idSearchRequest).then(function(data) {
            if (data.idSearchRequest != idSearchRequest) {
                return;
            }

            $scope.searchActive = true;
            $scope.searchLinks = data.links;
            $scope.loading = false;
        });
    }

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
