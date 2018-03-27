'use strict';

module.exports = function() {
    return {
        templateUrl: '/partial/index/period-chooser',
        scope: {
            period: '='
        },
        controller: ['$scope', '$window', '$element', '$rootScope', function($scope, $window, $element, $rootScope) {
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

            $scope.$watch('period.select', function() {
                $rootScope.$broadcast('updateVisitsChart');
            });

            $scope.$watchCollection('period.interval', function(oldValue, newValue) {
                $scope.period.text = dateFormat($scope.period.interval.start, 'dd.mm') + ' - ' + dateFormat($scope.period.interval.end, 'dd.mm');
                $rootScope.$broadcast('updateVisitsChart');

                if (oldValue.end != newValue.end) {
                    $scope.showDateRange = false;
                }
            });

            $window.addEventListener('click', function(e) {
                if ($(e.target).closest('.period-chooser').length == 0) {
                    update();
                }
            });

            $window.addEventListener('keyup', function(e) {
                // enter
                if (e.keyCode == 13) {
                    update();
                }
            });

            function update() {
                $rootScope.$broadcast('updateVisitsChart');
                $scope.showDateRange = false;
            }
        }]
    };
};
