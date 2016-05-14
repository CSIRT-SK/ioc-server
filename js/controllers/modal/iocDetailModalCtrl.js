app.controller('IocDetailModalCtrl', ['$scope', '$filter', '$uibModalInstance', 'data', function($scope, $filter, $uibModalInstance, data) {
    $scope.ioc = data.ioc;
    $scope.types = data.types;
    
    var valArray = $scope.ioc.value;
    var nameArray = [];
    for (var i = 0; i < $scope.types.length; i++) {
        var type = $scope.types[i];
        if (type.type == $scope.ioc.type) {
            nameArray = $scope.types[i].values_desc;
            break;
        }
    }
    $scope.values = []
    for (var i = 0; i < valArray.length; i++) {
        $scope.values.push({value: valArray[i], name: nameArray[i]});
    }
    
    $scope.ok = function () {
        $uibModalInstance.close();
    };
}]);