app.controller('NavController', ['$scope', '$location', 'pages', function($scope, $location, pages) {
    $scope.isActive = function(path) {
        return $location.path() === path;
    };
    $scope.pages = pages;
}]);