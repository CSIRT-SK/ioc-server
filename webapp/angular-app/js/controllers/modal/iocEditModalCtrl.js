app.controller('IocEditModalCtrl', ['$scope', '$filter', '$uibModalInstance', 'data', function($scope, $filter, $uibModalInstance, data) {
    $scope.ioc = data.ioc;
    $scope.action = data.action;
    $scope.types = data.types;
    
    $scope.values = {};
    for (var i = 0; i < $scope.types.length; i++) {
        var type = $scope.types[i];
        var names = $filter('split')($scope.types[i].values_desc, '|');
        $scope.values[type.type] = [];
        for (var j = 0; j < type.values_count; j++) {
            $scope.values[type.type].push({value: '', name: names[j]});
        }
    }
    
    var valArray = $filter('split')(data.ioc.value, '|');
    for (var i = 0; i < valArray.length; i++)
        $scope.values[$scope.ioc.type][i].value = valArray[i];
    
    $scope.ok = function () {
        var values = $scope.values[$scope.ioc.type];
        var valArray = values.map(function(el){
            return el.value;
        });
        $scope.ioc.value = $filter('merge')(valArray, '|');
        $uibModalInstance.close($scope.ioc);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss();
    };
}]);