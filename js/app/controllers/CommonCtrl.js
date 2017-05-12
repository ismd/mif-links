'use strict';

window.mainModule.controller('CommonCtrl', ['$scope', 'clipboard', function($scope, clipboard) {

    if (!clipboard.supported) {
        console.log('Sorry, copy to clipboard is not supported');
    }

    $scope.copyToClipboard = function(text) {
        clipboard.copyText(text);
    };
}]);
