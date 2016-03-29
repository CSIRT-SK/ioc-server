app.controller('DataController', ['$scope', 'IocService', function($scope, IocService) {
    $scope.data = 'data';
    IocService.test().then(function success(data) {
        $scope.tree = {
            name: 'setname',
            children: data
        };
    }, function error(errormsg) {
        $scope.error = errormsg;
    });
}]);
