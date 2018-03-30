'use strict';

module.exports = function() {
    return {
        templateUrl: '/partial/index/list-table',
        scope: {
            table: '=',
            fetchItems: '=',
            searchItems: '=?',
            loading: '=?',
            redirect: '=?',
            itemsPerPage: '=?'
        },
        controller: ['$scope', '$timeout', '$routeParams', '$route', '$location', function($scope, $timeout, $routeParams, $route, $location) {
            $scope.itemsPerPage = $scope.itemsPerPage || 15;

            $scope.items = [];

            $scope.search = {
                items: null,
                active: false,
                text: ''
            };

            init();

            $scope.previousPage = function() {
                if ($scope.pager.from - $scope.itemsPerPage < 0) {
                    return;
                }

                $scope.pager.from -= $scope.itemsPerPage;
                $scope.pager.to = $scope.pager.from + $scope.itemsPerPage;

                updatePage($scope.pager.page - 1);
            };

            $scope.nextPage = function() {
                if ($scope.pager.from + $scope.itemsPerPage >= $scope.pager.count) {
                    return;
                }

                $scope.pager.from += $scope.itemsPerPage;
                $scope.pager.to = $scope.pager.from + $scope.itemsPerPage;

                if ($scope.pager.to > $scope.pager.count) {
                    $scope.pager.to = $scope.pager.count;
                }

                updatePage($scope.pager.page + 1);
            };

            var searchTimer = null;
            $scope.searchChanged = function() {
                $scope.loading = true;

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

            $scope.$on('$routeUpdate', function(e, current) {
                init();
            });

            function init() {
                var page = $routeParams.page;
                page = page ? Number(page) - 1 : 0;

                $scope.pager = {
                    page: page,
                    from: page * $scope.itemsPerPage,
                    to: (page + 1) * $scope.itemsPerPage - 1,
                    count: null
                };

                fetchItems();
            }

            var idSearchRequest = 0;
            function searchItems(search) {
                if (!search) {
                    $scope.search.items = null;
                    $scope.search.active = false;
                    $scope.loading = false;
                    return;
                }

                if (++idSearchRequest > 10000) {
                    idSearchRequest = 0;
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
                $scope.fetchItems($scope.pager.from, $scope.pager.from + $scope.itemsPerPage, $routeParams.period).then(function(data) {
                    $scope.items = data.items;
                    $scope.pager.count = data.count;

                    if ($scope.pager.from >= $scope.pager.count) {
                        updatePage(0);
                        fetchItems();
                    } else {
                        $scope.pager.to = $scope.pager.from + $scope.items.length;
                        $scope.loading = false;
                    }
                }, function() {
                    alert('Не удалось загрузить данные');
                });
            };

            function updatePage(page) {
                $scope.pager.page = page;

                $route.updateParams({
                    page: page > 0 ? page + 1 : null
                });
            }
        }]
    };
};
