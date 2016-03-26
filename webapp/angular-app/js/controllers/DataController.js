app.controller('DataController', ['$scope', 'IocService', function($scope, IocService) {
    $scope.data = 'data';
    IocService.call('setname').then(function success(response) {
        $scope.tree = {
            name: 'setname',
            children: response
        };
    }, function error(response) {
        $scope.error = response;
    });
}]);
