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

            var page = $routeParams.page;
            page = page ? Number(page) - 1 : 0;

            $scope.pager = {
                page: page,
                from: page * $scope.itemsPerPage,
                to: (page + 1) * $scope.itemsPerPage - 1,
                count: null
            };

            fetchItems();

            $scope.previousPage = function() {
                if ($scope.pager.from - $scope.itemsPerPage < 0) {
                    return;
                }

                $scope.pager.from -= $scope.itemsPerPage;
                $scope.pager.to = $scope.pager.from + $scope.itemsPerPage;

                updatePage($scope.pager.page - 1);
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

                updatePage($scope.pager.page + 1);
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

            function updatePage(page) {
                $scope.pager.page = page;

                if (page > 0) {
                    $route.updateParams({
                        page: page + 1
                    });
                } else {
                    $location.search('page', null);
                }
            }
        }]
    };
};
