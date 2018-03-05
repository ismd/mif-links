'use strict';

window.mainModule.controller('GroupInfoCtrl', ['$scope', '$routeParams', 'Group', function($scope, $routeParams, Group) {

    $scope.idGroup = $routeParams.id;
    $scope.groupTitle = '';

    Group.fetchGroupById($scope.idGroup).then(function(data) {
        $scope.groupTitle = data.title;
    });
}]);
