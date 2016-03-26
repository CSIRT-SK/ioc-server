var app = angular.module('iocApp', ['ngRoute']);

app.config(function($routeProvider) {
  $routeProvider
    .when('/', {
      controller: 'HomeController',
      templateUrl: 'views/home.html'
    })
  	.when('/data', {
    	controller: 'DataController',
    	templateUrl: 'views/data.html'
  	})
    .otherwise({
      redirectTo: '/'
    });
});

app.config(function($httpProvider) {
    $httpProvider.defaults.transformRequest = function(data) {
        var str = [];
        for (var param in data) {
            str.push(encodeURIComponent(param) + '=' + encodeURIComponent(data[param]));
        }
        return str.join('&');
    };
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
    $httpProvider.defaults.headers.post['charset'] = 'utf8';
});