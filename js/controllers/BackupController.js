app.controller('BackupController', ['$scope', '$location', function($scope, $location) {

    // alerts
    $scope.alerts = [];
    
    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };
    
    $scope.addAlert = function(type, message) {
        $scope.alerts.push({type: type, msg: message});
    };

    // formats
    $scope.formats = {
		ioc: {
			i: ['json', 'csv'],
			e: ['json', 'csv']
		},
		set: {
			i: ['json'],
			e: ['json']
		},
		rep: {
			i: ['json'],
			e: ['json', 'csv']
		}
	};
	
    // show alerts based on url params
    var result = $location.search();
    console.log(result);
    if (result.success === '1') {
    	$scope.addAlert('success', result.message);
    } else if (result.success === '0') {
    	$scope.addAlert('danger', 'Error: ' + result.message);
    }
    
}]);
