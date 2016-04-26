app.controller('SetController', ['$scope', 'IocService', 'SetService', '$uibModal', function($scope, IocService, SetService, $uibModal) {

	// TODO: fix
	
    $scope.iocList = [];
    $scope.iocListRaw = [];
    $scope.iocTypes = [];
    $scope.setNameList = [];
    $scope.tree = {};

    // ui utilities
    $scope.showAdd = function(type) {
        return type == 'and' || type == 'or';
    }
    
    $scope.showAddRoot = function(type) {
        return type == 'root';
    }
    
    // alerts
    $scope.alerts = [];
    
    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };
    
    $scope.addAlert = function(type, message) {
        $scope.alerts.push({type: type, msg: message});
    };

    // modals
    var setAddModalInstance = function() {
    	return $uibModal.open({
            templateUrl: 'templates/modal/setAddTemplate.html',
            controller: 'SetAddModalCtrl',
            resolve: {
                data: function() {
                    return {
                        list: $scope.iocList
                    };
                }
            }
        }); 
    }
    
    $scope.newSet = function() {
        var modalInstance = setAddModalInstance();
        
        modalInstance.result.then(function success(data) {
            SetService.addIoc($scope.newSetName, data.type, 0, data.id).then(function success(data) {
                $scope.addAlert('success', 'Child added');
                $scope.loadSetNames();
                $scope.loadIoc();
                $scope.selectedSet = $scope.newSetName;
            }, function error(msg) {
                $scope.addAlert('danger', 'Error: ' + msg);
            });
        });
    };
    
    $scope.addChild = function(node) {
        node.open = false;
        var modalInstance = setAddModalInstance();
        modalInstance.result.then(function success(data) {
            SetService.addIoc($scope.selectedSet, data.type, node.set_id, data.id).then(function success(data) {
                $scope.addAlert('success', 'Child added');
                $scope.loadIoc();
                $scope.loadTree($scope.selectedSet);
            }, function error(msg) {
                $scope.addAlert('danger', 'Error: ' + msg);
            });
        });
    };
    
    $scope.addRoot = function(node) {
        node.open = false;
        var modalInstance = setAddModalInstance();
        
        modalInstance.result.then(function success(data) {
            SetService.addIoc($scope.selectedSet, data.type, 0, data.id).then(function success(data) {
                $scope.addAlert('success', 'Child added');
                $scope.loadIoc();
                $scope.loadTree($scope.selectedSet);
            }, function error(msg) {
                $scope.addAlert('danger', 'Error: ' + msg);
            });
        });
    };
    
    $scope.detail = function(node) {
        node.open = false;
        var modalInstance = $uibModal.open({
            templateUrl: 'templates/modal/iocDetailTemplate.html',
            controller: 'IocDetailModalCtrl',
            resolve: {
                data: function() {
                    return {
                        ioc: $scope.iocListRaw[node.id],
                        types: $scope.iocTypes
                    };
                }
            }
        });
    };
    
    $scope.edit = function(node) {
    	node.open = false;
        var modalInstance = $uibModal.open({
            templateUrl: 'templates/modal/iocEditTemplate.html',
            controller: 'IocEditModalCtrl',
            resolve: {
                data: function() {
                    return {
                        ioc: $scope.iocListRaw[node.id],
                        types: $scope.iocTypes,
                        action: 'Edit'
                    };
                }
            }
        });
        
        modalInstance.result.then(function success(ioc) {
            IocService.update(node.id, ioc).then(function success(data) {
                $scope.loadAvailable();
                $scope.addAlert('success', 'IOC data updated');
            }, function error(msg) {
                $scope.loadAvailable();
                console.log('[Edit IOC] Error: ' + msg);
                $scope.addAlert('danger', 'Error: ' + msg);
            });
        });
    }
    
    $scope.remove = function(node) {
        node.open = false;
        SetService.hideIoc(node.set_id).then(function success(data) {
            $scope.addAlert('success', 'Child removed');
            $scope.loadSetNames();
            $scope.loadTree($scope.selectedSet);
        }, function error(msg) {
            $scope.addAlert('danger', 'Error: ' + msg);
        });
    };
    
    // data loaders
    $scope.loadIoc = function() {
        IocService.listAvailable().then(function success(data) {
            $scope.iocListRaw = data;
            $scope.iocList = Object.keys(data).map(function(k) {
                var d = data[k];
                if (d.value === null) d.value = '';
                return d;
            });
        });
    };
    
    $scope.loadSetNames = function() {
        SetService.listNames().then(function success(data) {
            $scope.setNameList = data.map(function(v) {
                return v.name;
            });
            if ($scope.selectedSet === undefined) $scope.selectedSet = $scope.setNameList[0];
        });
    };
    
    $scope.loadTypes = function() {
        IocService.iocTypes().then(function success(data) {
            $scope.iocTypes = data;
        });
    };

    $scope.loadTree = function(name) {
        IocService.tree(name).then(function success(data) {
            $scope.tree = {
                name: name,
                type: 'root',
                children: data
            };
        });
    };
    
    $scope.$watch('selectedSet', function(newVal) {
        $scope.loadTree(newVal);
    });
    
    // init
    $scope.loadIoc();
    $scope.loadSetNames();
    $scope.loadTypes();
}]);