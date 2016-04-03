app.controller('SetAddModalCtrl', ['$scope', '$uibModalInstance', 'data', function($scope, $uibModalInstance, data) {
    $scope.iocList = data.list;

    $scope.iocTable = {
        sort: {
            col: 'name',
            reverse: false
        },
        search: ''
    };

    $scope.orderBy = function(col) {
        if (col == $scope.iocTable.sort.col) {
            $scope.iocTable.sort.reverse = !$scope.iocTable.sort.reverse;
        } else {
            $scope.iocTable.sort.col = col;
            $scope.iocTable.sort.reverse = false;
        }
    };
    
    $scope.ok = function (id) {
        $uibModalInstance.close(id);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss();
    };
}]);