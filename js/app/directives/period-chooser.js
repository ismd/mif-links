'use strict';

module.exports = function() {
    return {
        templateUrl: '/partial/index/period-chooser',
        scope: {
            period: '='
        },
        controller: ['$scope', '$window', '$element', '$route', '$routeParams', function($scope, $window, $element, $route, $routeParams) {
            $scope.showDateRange = false;
            $scope.interval = {};

            var updateSelect = true,
                skipRouteUpdate = false;

            init(true);

            function init(useGlobal) {
                var period = parsePeriod($routeParams.period);

                if (!useGlobal || period != null || angular.equals($scope.period, {})) {
                    if (updateSelect) {
                        $scope.interval.select = period == null ? 'all-time' : 'dates';
                    } else {
                        updateSelect = true;
                    }

                    $scope.interval.start = period != null ? period.start : null;
                    $scope.interval.end = period != null ? period.end : null;
                } else {
                    $scope.interval.select = $scope.period.select;
                    $scope.interval.start = $scope.period.start;
                    $scope.interval.end = $scope.period.end;

                    skipRouteUpdate = true;
                    intervalChanged($scope.interval, {
                        select: null,
                        start: null,
                        end: null
                    });
                }

                if ($scope.interval.select != 'all-time' && $scope.interval.start != null && $scope.interval.end != null) {
                    $scope.periodText = dateFormat($scope.interval.start, 'dd.mm') + ' - ' + dateFormat($scope.interval.end, 'dd.mm');
                } else {
                    $scope.periodText = '';
                }

                $scope.period.select = $scope.interval.select;
                $scope.period.start = $scope.interval.start;
                $scope.period.end = $scope.interval.end;
            }

            $scope.$watchCollection('interval', intervalChanged);

            function intervalChanged(newValue, oldValue) {
                if (newValue.select != 'all-time' && !compareIntervals(newValue, oldValue)) {
                    if (dateFormat(newValue.end, 'dd.mm.yyyy') != dateFormat(oldValue.end, 'dd.mm.yyyy')) {
                        $scope.showDateRange = false;
                    }

                    $route.updateParams({
                        period: dateFormat(newValue.start, 'dd.mm.yyyy') + '-' + dateFormat(newValue.end, 'dd.mm.yyyy')
                    });
                } else if (newValue.select != oldValue.select) {
                    var startDate = new Date(),
                        endDate = new Date();

                    switch (newValue.select) {
                    case 'all-time':
                        $route.updateParams({
                            period: null
                        });
                        return;
                        break;

                    case '3-days':
                        startDate.setDate(startDate.getDate() - 2);
                        break;

                    case 'week':
                        startDate.setDate(startDate.getDate() - 7);
                        break;

                    case 'month':
                        startDate.setMonth(startDate.getMonth() - 1);
                        break;

                    case 'dates':
                        if (oldValue.select == 'all-time') {
                            startDate.setMonth(startDate.getMonth() - 1);
                        } else {
                            startDate = newValue.start;
                            endDate = newValue.end;
                        }
                        break;
                    }

                    $scope.interval.start = startDate;
                    $scope.interval.end = endDate;

                    updateSelect = false;
                    $route.updateParams({
                        period: dateFormat($scope.interval.start, 'dd.mm.yyyy') + '-' + dateFormat($scope.interval.end, 'dd.mm.yyyy')
                    });
                }
            }

            $scope.$on('$routeUpdate', function(e, current) {
                if (!skipRouteUpdate) {
                    init();
                } else {
                    skipRouteUpdate = false;
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

            function compareIntervals(newValue, oldValue) {
                if (newValue.start == null) {
                    return oldValue.start == null;
                } else if (oldValue.start == null) {
                    return false;
                } else {
                    return dateFormat(newValue.start, 'dd.mm.yyyy') == dateFormat(oldValue.start, 'dd.mm.yyyy') &&
                        dateFormat(newValue.end, 'dd.mm.yyyy') == dateFormat(oldValue.end, 'dd.mm.yyyy');
                }
            }

            function parsePeriod(period) {
                if (typeof period == 'undefined') {
                    return null;
                }

                var result = {
                    start: null,
                    end: null
                };

                var periodSplit = period.split('-'),
                    startSplit = periodSplit[0].split('.'),
                    endSplit = periodSplit[1].split('.');

                result.start = new Date(parseInt(startSplit[2]), parseInt(startSplit[1] - 1), parseInt(startSplit[0]));
                result.end = new Date(parseInt(endSplit[2]), parseInt(endSplit[1] - 1), parseInt(endSplit[0]));

                return result;
            }
        }]
    };
};
