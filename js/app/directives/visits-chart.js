'use strict';

module.exports = function() {
    return {
        templateUrl: '/partial/index/visitsChart',
        scope: {
            fetchItems: '='
        },
        controller: ['$scope', '$routeParams', function($scope, $routeParams) {
            $scope.chart = {
                data: null,
                labels: null,
                options: {
                    elements: {
                        line: {
                            tension: 0
                        }
                    },
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            ticks: {
                                autoSkipPadding: 8
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(tick, index, ticks) {
                                    if (tick.toString().indexOf('.') != -1) {
                                        return null;
                                    }

                                    return tick.toLocaleString();
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItem, data) {
                                return data.labels[tooltipItem[0]['index']].split('.').slice(0, 2).join('.');
                            },
                            label: function(tooltipItem, data) {
                                return 'Посещений: ' + data.datasets[0].data[tooltipItem['index']];
                            }
                        }
                    }
                }
            };

            fetchItems();

            var currentPeriod = $routeParams.period;
            $scope.$on('$routeUpdate', function(e, current) {
                if ($routeParams.period != currentPeriod) {
                    fetchItems();
                    currentPeriod = $routeParams.period;
                }
            });

            function fetchItems() {
                $scope.fetchItems($routeParams.period).then(function(data) {
                    var itemsValues = Object.values(data.items);
                    var itemsKeys = Object.keys(data.items);

                    $scope.chart.data = [itemsValues];
                    $scope.chart.labels = itemsKeys.map(function(item) {
                        return item.split('.').slice(0, 2).join('.');
                    });
                });
            }
        }]
    };
};
