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
                options: {
                    elements: {
                        line: {
                            tension: 0
                        }
                    },
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
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

            $scope.fetchItems().then(function(data) {
                var itemsValues = Object.values(data.items);
                var itemsKeys = Object.keys(data.items);

                if (itemsValues.length == 0) {
                    return;
                }

                $scope.chart.data = [itemsValues];
                $scope.chart.labels = itemsKeys.map(function(item) {
                    return item.split('.').slice(0, 2).join('.');
                });
            });
        }]
    };
});
