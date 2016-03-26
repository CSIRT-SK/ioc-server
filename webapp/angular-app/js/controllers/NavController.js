app.controller('NavController', ['$scope', '$location', function($scope, $location) {
    $scope.isActive = function(path) {
        return $location.path() === path;
    };
    $scope.pages = [
        {
            name: 'Home',
            path: '/'
        },
        {
            name: 'Data',
            path: '/data'
        }
    ];
}]);