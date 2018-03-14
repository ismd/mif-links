'use strict';

window.mainModule.directive('listTable', function() {
    return {
        templateUrl: '/partial/index/list-table',
        scope: {
            table: '=',
            fetchItems: '=',
            searchItems: '=?',
            loading: '=?',
            redirect: '=?'
        },
        controller: ['$scope', '$timeout', function($scope, $timeout) {
            $scope.itemsPerPage = 20;

            $scope.items = [];

            $scope.search = {
                items: null,
                active: false,
                text: ''
            };

            $scope.pager = {
                from: 0,
                to: $scope.itemsPerPage,
                count: null
            };

            fetchItems();

            $scope.previousPage = function() {
                if ($scope.pager.from - $scope.itemsPerPage < 0) {
                    return;
                }

                $scope.pager.from -= $scope.itemsPerPage;
                $scope.pager.to = $scope.pager.from + $scope.itemsPerPage;

                fetchItems();
            };

            $scope.nextPage = function() {
                if ($scope.pager.from + $scope.itemsPerPage > $scope.pager.count) {
                    return;
                }

                $scope.pager.from += $scope.itemsPerPage;
                $scope.pager.to = $scope.pager.from + $scope.itemsPerPage;

                if ($scope.pager.to > $scope.pager.count) {
                    $scope.pager.to = $scope.pager.count;
                }

                fetchItems();
            };

            var searchTimer = null,
                idSearchRequest = 0;

            $scope.searchChanged = function() {
                $scope.loading = true;

                if (++idSearchRequest > 10000) {
                    idSearchRequest = 0;
                }

                try {
                    $timeout.cancel(searchTimer);
                } catch (err) {
                }

                searchTimer = $timeout(function() {
                    searchItems($scope.search.text);
                }, 500);
            };

            $scope.$on('updateListTable', function() {
                fetchItems();
            });

            function searchItems(search) {
                if (!search) {
                    $scope.search.items = null;
                    $scope.search.active = false;
                    $scope.loading = false;
                    return;
                }

                $scope.searchItems(search, idSearchRequest).then(function(data) {
                    if (data.idSearchRequest != idSearchRequest) {
                        return;
                    }

                    $scope.search.items = data.items;
                    $scope.search.active = true;
                    $scope.loading = false;
                });
            };

            function fetchItems() {
                $scope.fetchItems($scope.pager.from, $scope.pager.from + $scope.itemsPerPage).then(function(data) {
                    $scope.items = data.items;

                    $scope.pager.to = $scope.pager.from + $scope.items.length;
                    $scope.pager.count = data.count;

                    $scope.loading = false;
                }, function() {
                    alert('Не удалось загрузить данные');
                });
            };
        }]
    };
});
