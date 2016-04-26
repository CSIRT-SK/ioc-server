app.controller('SetAddModalCtrl', ['$scope', '$uibModal', '$uibModalInstance', 'IocService', 'data', function($scope, $uibModal, $uibModalInstance, IocService, data) {
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
    
    $scope.ok = function (type, id) {
        $uibModalInstance.close({
        	type: type,
        	id: id
        });
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss();
    };
    
    $scope.addNew = function() {
        var modalInstance = $uibModal.open({
            templateUrl: 'templates/modal/iocEditTemplate.html',
            controller: 'IocEditModalCtrl',
            resolve: {
                data: function() {
                    return {
                        ioc: {
                            type: 'file-name',
                            value: '|',
                            parent: 0
                        },
                        types: $scope.iocTypes,
                        action: 'New'
                    };
                }
            }
        });
        
        modalInstance.result.then(function success(ioc) {
            IocService.add(ioc).then(function success(data) {
            	$uibModalInstance.close(data.id);
            }, function error(msg) {
            	$uibModalInstance.dismiss(msg);
            });
        });
    }
    
    $scope.loadTypes = function() {
        IocService.iocTypes().then(function success(data) {
            $scope.iocTypes = data;
        });
    };
    
    $scope.loadTypes();
    
}]);