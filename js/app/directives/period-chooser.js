'use strict';

module.exports = function() {
    return {
        templateUrl: '/partial/index/period-chooser',
        scope: {
            period: '='
        },
        controller: ['$scope', '$window', '$element', '$rootScope', function($scope, $window, $element, $rootScope) {
            $scope.showDateRange = false;

            $scope.period = {
                select: 'all-time',
                interval: {
                    start: null,
                    end: null
                },
                text: ''
            };

            var justChoosedSelect = false,
                datesInitialized = false;

            $scope.$watch('period.select', function() {
                var start = new Date(),
                    end = new Date();

                switch ($scope.period.select) {
                case 'all-time':
                    start = null;
                    end = null;
                    break;

                case '3-days':
                    start.setDate(start.getDate() - 2);
                    break;

                case 'week':
                    start.setDate(start.getDate() - 7);
                    break;

                case 'month':
                    start.setMonth(start.getMonth() - 1);
                    break;

                case 'dates':
                    if (!datesInitialized) {
                        start.setMonth(start.getMonth() - 1);
                    } else {
                        start = $scope.period.interval.start;
                        end = $scope.period.interval.end;
                    }
                    break;
                }

                justChoosedSelect = true;
                datesInitialized = $scope.period.select != 'all-time';
                $scope.showDateRange = false;

                $scope.period.interval = {
                    start: start,
                    end: end
                };
            });

            $scope.$watchCollection('period.interval', function(oldValue, newValue) {
                if ($scope.period.select == 'all-time') {
                    $rootScope.$broadcast('updateVisitsChart');
                    $scope.showDateRange = false;
                    return;
                }

                if (!justChoosedSelect && $scope.period.select != 'dates') {
                    $scope.period.select = 'dates';
                }

                justChoosedSelect = false;
                $scope.period.text = dateFormat($scope.period.interval.start, 'dd.mm') + ' - ' + dateFormat($scope.period.interval.end, 'dd.mm');

                $rootScope.$broadcast('updateVisitsChart');

                if (dateFormat(oldValue.end, 'dd.mm.yyyy') != dateFormat(newValue.end, 'dd.mm.yyyy')) {
                    $scope.showDateRange = false;
                }
            });

            $scope.inputFocus = function(e) {
                $(e.target).blur();
            };

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
