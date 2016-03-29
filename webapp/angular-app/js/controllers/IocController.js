app.controller('IocController', ['$scope', 'IocService', '$uibModal', function($scope, IocService, $uibModal) {
    
    // data structures
    $scope.alerts = [];
    
    $scope.iocListRaw = [];
    $scope.iocDelListRaw = [];
    $scope.iocList = [];
    $scope.iocDelList = [];
    
    $scope.iocTypes = [];
    
    $scope.iocTable = {
        sort: {
            col: 'name',
            reverse: false
        },
        search: ''
    };

    $scope.delTable = {
        sort: {
            col: 'name',
            reverse: false
        },
        search: ''
    };
    
    // ui functions
    $scope.orderBy = function(col) {
        if (col == $scope.iocTable.sort.col) {
            $scope.iocTable.sort.reverse = !$scope.iocTable.sort.reverse;
        } else {
            $scope.iocTable.sort.col = col;
            $scope.iocTable.sort.reverse = false;
        }
    };
    
    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };
    
    $scope.addAlert = function(type, message) {
        $scope.alerts.push({type: type, msg: message});
    };
    
    // modal dialogs
    $scope.detail = function(id) {
        var modalInstance = $uibModal.open({
            templateUrl: 'views/templates/modal/iocDetailTemplate.html',
            controller: 'IocDetailModalCtrl',
            resolve: {
                data: function() {
                    return {
                        list: $scope.iocListRaw,
                        id: id,
                        detail: $scope.detail,
                        types: $scope.iocTypes
                    };
                }
            }
        });
    }
    
    $scope.edit = function(id) {
        var modalInstance = $uibModal.open({
            templateUrl: 'views/templates/modal/iocEditTemplate.html',
            controller: 'IocEditModalCtrl',
            resolve: {
                data: function() {
                    return {
                        ioc: $scope.iocListRaw[id],
                        types: $scope.iocTypes,
                        action: 'Edit'
                    };
                }
            }
        });
        
        modalInstance.result.then(function success(ioc) {
            IocService.update(id, ioc).then(function success(data) {
                $scope.loadAvailable();
                $scope.addAlert('success', 'IOC data updated');
            }, function error(msg) {
                console.log('[Edit IOC] Error: ' + msg);
                $scope.addAlert('danger', 'Error: ' + msg);
            });
        });
    }
    
    $scope.newIoc = function() {
        var modalInstance = $uibModal.open({
            templateUrl: 'views/templates/modal/iocEditTemplate.html',
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
                $scope.loadAvailable();
                $scope.addAlert('success', 'New IOC added');
            }, function error(msg) {
                console.log('[New IOC] Error: ' + msg);
                $scope.addAlert('danger', 'Error: ' + msg);
            });
        });
    }
    
    $scope.del = function(id) {
        var modalInstance = $uibModal.open({
            templateUrl: 'views/templates/modal/iocDeleteTemplate.html',
            controller: 'IocDeleteModalCtrl',
            size: 'sm',
            windowTopClass: 'small-dialog',
            resolve: {
                data: function() {
                    return {
                        ioc: $scope.iocListRaw[id]
                    };
                }
            }
        });
        
        modalInstance.result.then(function success() {
            IocService.hide(id, 1).then(function success(data) {
                $scope.loadAvailable();
                $scope.loadDeleted();
                $scope.addAlert('success', 'IOC removed');
            }, function error(msg) {
                console.log('[Delete IOC] Error: ' + msg);
                $scope.addAlert('danger', 'Error: ' + msg);
            });
        });
    }
    
    $scope.res = function(id) {
        IocService.hide(id, 0).then(function success(data) {
            $scope.loadAvailable();
            $scope.loadDeleted();
            $scope.addAlert('success', 'IOC restored');
        }, function error(msg) {
            console.log('[Restore IOC] Error: ' + msg);
            $scope.addAlert('danger', 'Error: ' + msg);
        });
    }
    
    // data loaders
    $scope.loadAvailable = function() {
        IocService.listAvailable().then(function success(data) {
            $scope.iocListRaw = data;
            $scope.iocList = Object.keys(data).map(function(k) {
                var d = data[k];
                if (d.value === null) d.value = '';
                return d;
            });
        });
    };
    
    $scope.loadDeleted = function() {
        IocService.listHidden().then(function success(data) {
            $scope.iocDelListRaw = data;
            $scope.iocDelList = Object.keys(data).map(function(k) {
                var d = data[k];
                if (d.value === null) d.value = '';
                return d;
            });
        });
    };
    
    $scope.loadTypes = function() {
        IocService.iocTypes().then(function success(data) {
            $scope.iocTypes = data;
        });
    };
    
    // init
    $scope.loadAvailable();
    $scope.loadDeleted();
    $scope.loadTypes();
}]);