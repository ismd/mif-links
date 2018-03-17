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

            $scope.fetchItems().then(function(data) {
                var itemsValues = Object.values(data.items);
                var itemsKeys = Object.keys(data.items);

                if (itemsValues.length == 0) {
                    return;
                }

                var date1 = new Date(itemsKeys[0].split('.').reverse().join('-'));
                date1.setDate(date1.getDate() - 1);

                itemsValues.unshift(0);
                itemsKeys.unshift(dateFormat(date1, 'dd.mm.yyyy'));

                var date2 = new Date(itemsKeys[itemsKeys.length - 1].split('.').reverse().join('-'));
                date2.setDate(date2.getDate() + 1);

                if (date2 <= new Date()) {
                    itemsValues.push(0);
                    itemsKeys.push(dateFormat(date2, 'dd.mm.yyyy'));
                }

                $scope.chart.data = [itemsValues];
                $scope.chart.labels = itemsKeys.map(function(item) {
                    return item.split('.').slice(0, 2).join('.');
                });
            });
        }]
    };
});
