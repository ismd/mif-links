'use strict';

window.mainModule.controller('CommonCtrl', ['$scope', 'clipboard', function($scope, clipboard) {

    $scope.location = window.location;

    if (!clipboard.supported) {
        console.log('Sorry, copy to clipboard is not supported');
    }

    $scope.copyToClipboard = function(text, $ev) {
        if ($ev) {
            $ev.preventDefault();
            $($ev.target).addClass('copied');

            setTimeout(function () {
                $($ev.target).removeClass('copied');
            }, 1000);
        }

        clipboard.copyText(text);
    };
}]);
