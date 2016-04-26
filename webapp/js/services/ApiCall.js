app.factory('ApiCall', ['$http', '$q', '$location', function($http, $q, $location){
    return function(data) {
    	var apiUrl = $location.absUrl().split('#')[0] + '../api.php';
        return $http.post(apiUrl, data).then(function success(response) {
            return $q(function(resolve, reject){
                if (response.data.success) {
                    resolve(response.data.data);
                } else {
                    reject(response.data.errormsg);
                }
            });
        }, function error(response) {
            return $q(function(resolve, reject) {
                reject(response);
            });
        });
    };
}]);