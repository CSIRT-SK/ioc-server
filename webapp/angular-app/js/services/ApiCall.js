app.factory('ApiCall', ['$http', '$q', function($http, $q){
    return function(data) {
        return $http.post('https://localhost/ioc-server/api.php', data).then(function success(response) {
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