app.controller('ReportController', ['$scope', '$filter', 'IocService', 'SetService', 'ReportService', '$uibModal', function($scope, $filter, IocService, SetService, ReportService, $uibModal) {
    
    // data structures
    $scope.alerts = [];
    
    $scope.reportList = [];
    
    $scope.iocMap = {};
    
    $scope.iocTypes = [];
    
    $scope.table = {
        sort: {
            col: {
                title: 'Time',
                sort: 'timestamp'
            },
            reverse: true,
        },
        search: {},
        layout: [
            {
                title: 'Organization',
                sort: 'org',
                width: '20%',
            },
            {
                title: 'Device',
                sort: 'device',
                width: '20%',
            },
            {
                title: 'Time',
                sort: 'timestamp',
                width: '20%',
            },
            {
                title: 'Set',
                sort: 'setname',
                width: '20%',
            },
            {
                title: 'Indicator',
                sort: 'iocName',
                width: '20%',
            },
            {
                title: 'Result',
                sort: 'result',
                width: '10%',
            },
        ],
    };

    // ui functions
    $scope.orderBy = function(table, col) {
        if (col.title == table.sort.col.title) {
            table.sort.reverse = !table.sort.reverse;
        } else {
            table.sort.col.title = col.title;
            table.sort.col.sort = col.sort;
            if (col.title == 'Time') table.sort.reverse = true;
            else table.sort.reverse = false;
        }
    };
    
    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };
    
    $scope.addAlert = function(type, message) {
        $scope.alerts.push({type: type, msg: message});
    };
    
    // modal dialogs
    $scope.detail = function(result) {
        var modalInstance = $uibModal.open({
            templateUrl: 'templates/modal/resultDetailTemplate.html',
            controller: 'ResultDetailModalCtrl',
            resolve: {
                data: function() {
                    return {
                        ioc: $scope.iocMap[result.ioc_id],
                        types: $scope.iocTypes,
                        result: result
                    };
                }
            }
        });
    }
    
    // util
    $scope.showDetailButton = function(result) {
    	var id = result.ioc_id;
    	if ($scope.iocMap.hasOwnProperty(id)) {
    		return $scope.iocMap[id].type != 'and' && $scope.iocMap[id].type != 'or' && $scope.iocMap[id].type != 'root';
    	} else {
    		return false;
    	}
    }
    
    $scope.resultTypeClass = function(result) {
    	var id = result.ioc_id;
    	if ($scope.iocMap.hasOwnProperty(id)) {
    		var textClass = '';
    		switch ($scope.iocMap[id].type) {
    			case 'and':
    			case 'or':
    				textClass = 'btn-warning';
    				break;
    			case 'root':
    				textClass = 'btn-primary';
    				break;
    			default:
    				textClass = '';
    		}
    		return textClass;
    	} else {
    		return '';
    	}
    }
    
    $scope.resultTypeText = function(result) {
    	var id = result.ioc_id;
    	if ($scope.iocMap.hasOwnProperty(id)) {
    		var text = '';
    		switch ($scope.iocMap[id].type) {
    			case 'and':
    				text = 'and';
    				break;
    			case 'or':
    				text = 'or';
    				break;
    			case 'root':
    				text = 'set';
    				break;
    			default:
    				text = '';
    		}
    		return text;
    	} else {
    		return '';
    	}
    }
    
    // pagination
    
    $scope.pagination = {
    		size: 20,
    		page: 1
    };
    
    $scope.$watch('pagination.pageSize', function(val) {console.log(val);});
    
    // date picker
    $scope.dateRange = "";
    var weekAgo = new Date();
    weekAgo.setDate(weekAgo.getDate() - 7);
    $scope.date = {
        start: weekAgo,
        end: new Date()
    };
    
    $scope.date.startOpened = false;
    $scope.date.endOpened = false;
    
    $scope.date.openStart = function() {
        $scope.date.startOpened = true;
        $scope.date.endOpened = false;
    };
    
    $scope.date.openEnd = function() {
        $scope.date.startOpened = false;
        $scope.date.endOpened = true;
    };
    
    $scope.date.options = {
        maxDate: new Date(),
        showWeeks: false
    };
      
    $scope.$watch('date', function() {
        $scope.dateRange = $filter('date')($scope.date.start, 'd.M.yy') + ' - ' + $filter('date')($scope.date.end, 'd.M.yy');
        $scope.loadReports();
    }, true);
    
    // data loaders
    $scope.loadIoc = function(id, reportId) {
    	if ($scope.iocMap.hasOwnProperty(id))
            $scope.reportList[reportId].iocName = $scope.iocMap[id].name;
            
    	SetService.getId(id).then(function success(data) {
    		if (data) {
	    		if (data.type == 'ioc') {
	    			IocService.get(data.ioc_id).then(function success(data) {
	    				$scope.iocMap[id] = data;
	    				$scope.reportList[reportId].iocName = data.name;
	    			});
	    		} else if (data.type == 'root') {
	    			var name = data.name;
	    			$scope.iocMap[id] = {
	    				id: null,
	    				name: name,
	    				type: data.type,
	    				value: ''
	    			};
	    			$scope.reportList[reportId].iocName = name;
	    		} else { // and & or
	    			var name = data.type + ' #' + data.id;
	    			$scope.iocMap[id] = {
	    				id: null,
	    				name: name,
	    				type: data.type,
	    				value: ''
	    			};
	    			$scope.reportList[reportId].iocName = name;
	    		}
    		} else {
    			var name = 'Unknown IOC';
    			$scope.iocMap[id] = {
    				id: null,
    				name: name,
    				type: 'unknown',
    				value: ''
    			};
    			$scope.reportList[reportId].iocName = name;
    		}
    	});
    };
    
    $scope.loadReports = function() {
        ReportService.timeRange($scope.date.start, $scope.date.end).then(function success(data){
            $scope.reportList = data;
            for (var i = 0; i < $scope.reportList.length; i++) {
                $scope.loadIoc($scope.reportList[i].ioc_id, i);
            }
        });
    };
    
    $scope.loadTypes = function() {
        IocService.iocTypes().then(function success(data) {
            $scope.iocTypes = data;
        });
    };
    
    $scope.$watch('table.search.result', function(newVal) {
        if (newVal == null) $scope.table.search.result = '';
    });
    
    // init
    $scope.loadTypes();
}]);

app.factory('ReportService', ['ApiCall', function(ApiCall) {
    var service = {};

    service.listAll = function() {
        var data = {
            controller: 'report',
            action: 'listAll'//action: 'getTimeRange'
        };
        return ApiCall(data);
    };
    
    service.timeRange = function(from, to) {
        var data = {
            controller: 'report',
            action: 'getTimeRange',
            from: from / 1000,
            to: to / 1000
        }
        return ApiCall(data);
    };
    
    return service;
}]);
