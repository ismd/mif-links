'use strict';

window.mainModule.controller('AdminCtrl', ['$scope', '$location', '$timeout', 'Link', function($scope, $location, $timeout, Link) {
    $scope.link = '';
    $scope.lastLink = null;
    $scope.editShortLink = '';
    $scope.linkExists = false;
    $scope.loading = false;

    $scope.generateLink = function(link, force) {
        $scope.loading = true;

        Link.generateLink(link, force).then(function(data) {
            $scope.link = '';
            $scope.lastLink = data.info;
            $scope.editShortLink = $scope.lastLink.shortLink;
            $scope.$broadcast('updateListTable');
        }, function(data) {
            if (data.result === 'duplicate') {
                $scope.duplicates = data.links;

                $('.js-duplicate-popup').modal().on('hide.bs.modal', function() {
                    $timeout(function() {
                        $scope.loading = false;
                    });
                });
            }
        });
    };

    $scope.regenerateLink = function(link) {
        $scope.loading = true;

        Link.regenerateLink(link.id).then(function(data) {
            $scope.lastLink = data.info;
            $scope.editShortLink = $scope.lastLink.shortLink;
            fetchLinks();
        });
    };

    $scope.editLink = function(shortLink) {
        $scope.loading = true;

        Link.editLink($scope.lastLink.id, shortLink).then(function(data) {
            $('#edit-link').modal('hide');
            $scope.lastLink = data.info;
            $scope.editShortLink = $scope.lastLink.shortLink;
            fetchLinks();
        }, function(data) {
            if (data.result === 'duplicate') {
                $scope.linkExists = true;
                $scope.loading = false;
            }
        });
    };

    $scope.$watch('editShortLink', function() {
        $scope.linkExists = false;
    });

    $scope.fetchLinks = Link.fetchLinks;
    $scope.searchLinks = Link.search;
}]);
