'use strict';

module.exports = function() {
    return {
        templateUrl: '/partial/index/period-chooser',
        controller: ['$scope', '$window', '$element', function($scope, $window, $element) {
            $scope.showDateRange = false;

            var dateStart = new Date();
            dateStart.setMonth(dateStart.getMonth() - 1);

            $scope.period = {
                select: 'all-time',
                interval: {
                    start: dateStart,
                    end: new Date()
                },
                text: ''
            };

            $scope.$watchCollection('period.interval', function(oldValue, newValue) {
                $scope.period.text = dateFormat($scope.period.interval.start, 'dd.mm') + ' - ' + dateFormat($scope.period.interval.end, 'dd.mm');

                if (oldValue.end != newValue.end) {
                    $scope.showDateRange = false;
                }
            });

            $window.addEventListener('click', function(e) {
                if ($(e.target).closest('.period-chooser').length == 0) {
                    $scope.showDateRange = false;
                    $scope.$apply();
                }
            });

            $window.addEventListener('keyup', function(e) {
                // enter
                if (e.keyCode == 13) {
                    $scope.showDateRange = false;
                    $scope.$apply();
                }
            });
        }]
    };
};
