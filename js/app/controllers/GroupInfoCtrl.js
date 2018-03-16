'use strict';

window.mainModule.controller('GroupInfoCtrl', ['$scope', '$routeParams', '$q', 'Group', 'Link', function($scope, $routeParams, $q, Group, Link) {

    $scope.idGroup = $routeParams.id;
    $scope.groupInfo = null;

    Group.fetchGroupById($scope.idGroup).then(function(data) {
        $scope.groupInfo = data;
    });

    $scope.fetchLinks = function(from, to) {
        var defer = $q.defer();

        Link.fetchLinks(from, to, $scope.idGroup).then(function(data) {
            defer.resolve(data);
        });

        return defer.promise;
    };

    $scope.searchLinks = Link.search;
    $scope.fetchVisitsByGroup = function(from, to) {
        return Group.fetchVisitsById($scope.idGroup, from, to);
    };

    // chart
    $scope.labels = ["January", "February", "March", "April", "May", "June", "July"];
    $scope.data = [
        [65, 59, 80, 81, 56, 55, 40]
    ];

    $scope.chartData = [{
        date: '01.01',
        clicks: 5
    }, {
        date: '02.01',
        clicks: 1
    }, {
        date: '03.01',
        clicks: 7
    }];
}]);
