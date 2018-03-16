'use strict';

window.mainModule.directive('visitsChart', function() {
    return {
        templateUrl: '/partial/index/visitsChart',
        scope: {
            fetchItems: '='
        },
        controller: ['$scope', function($scope) {
            $scope.chart = {
                data: null,
                labels: null,
                datasetOverride: [{
                    lineTension: .1
                }],
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
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

            var to = new Date();
            var from = new Date();
            from.setMonth(from.getMonth() - 1);

            $scope.fetchItems(from.getDate() + '-' + (from.getMonth() + 1) + '-' + from.getFullYear(),
                              to.getDate() + '-' + (to.getMonth() + 1) + '-' + to.getFullYear()).then(function(data) {
                $scope.chart.data = [Object.values(data.items)];
                $scope.chart.labels = Object.keys(data.items).map(function(item) {
                    return item.split('.').slice(0, 2).join('.');
                });
            });
        }]
    };
});
