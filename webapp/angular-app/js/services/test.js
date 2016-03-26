app.factory('test', ['$http', '$q', function($http, $q) {
    var service = {};
    service.api = 'https://localhost/ioc-server/api.php';
    service.call = function(name) {
        this.data = {
            controller: 'client',
            action: 'request',
            name: name
        };
        return $http.post(this.api, this.data, this.config).then(function success(response) {
            return $q(function(resolve, reject){
                if (response.data.success) {
                    resolve(response.data.data);
                } else {
                    reject(response.data.errormsg);
                }
            });
        }, function error(response) {
            return 'Internal Error';
        });
    }
    return service;
}]);