app.controller('BackupController', ['$scope', function($scope) {

	$scope.formats = {
			ioc: ['json', 'csv'],
			set: ['json'],
			rep: ['json', 'csv']
	};
	
}]);
