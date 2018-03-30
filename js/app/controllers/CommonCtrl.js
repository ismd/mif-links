'use strict';

module.exports = ['$scope', '$window', '$location', 'clipboard', function($scope, $window, $location, clipboard) {
    $scope.location = window.location;

    if (!clipboard.supported) {
        console.log('Sorry, copy to clipboard is not supported');
    }

    $scope.copyToClipboard = function(text, $ev) {
        if ($ev) {
            $ev.preventDefault();
            $ev.stopPropagation();

            $($ev.target).addClass('copied');

            setTimeout(function () {
                $($ev.target).removeClass('copied');
            }, 1000);
        }

        clipboard.copyText(text);
    };

    $scope.redirect = function(url, $ev) {
        if ($ev) {
            $ev.preventDefault();
            $ev.stopPropagation();
        }

        $location.path(url);
        $location.search('page', null);
    };
}];
