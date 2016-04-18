app.directive('backupForm', function() {
	return {
		restrict: 'AE',
		templateUrl: 'templates/backupForm.html',
		scope: {
			type: '=',
			formats: '='
		}
	};
});