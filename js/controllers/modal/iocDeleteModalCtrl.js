app.controller('IocDeleteModalCtrl', ['$scope', '$uibModalInstance', 'data', function($scope, $uibModalInstance, data) {
    $scope.ioc = data.ioc;
    
    $scope.ok = function () {
        $uibModalInstance.close();
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss();
    };
}]);