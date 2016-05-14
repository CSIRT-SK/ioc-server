var app = angular.module('iocApp', ['ngRoute', 'ngAnimate', 'ui.bootstrap']);

app.constant('apiUrl', 'https://localhost/ioc-server/api.php');

app.constant('pages', [
    {
        name: 'IOC',
        path: '/ioc',
        controller: 'IocController',
        template: 'views/ioc.html'
    },
    {
        name: 'Sets',
        path: '/set',
        controller: 'SetController',
        template: 'views/set.html'
    },
    {
        name: 'Reports',
        path: '/report',
        controller: 'ReportController',
        template: 'views/report.html'
    },
    {
        name: 'Backup',
        path: '/backup',
        controller: 'BackupController',
        template: 'views/backup.html'
    }
]);

// configs

app.config(function($routeProvider, pages) {
  for (var i = 0; i < pages.length; i++){
    $routeProvider.when(pages[i].path, {
        controller: pages[i].controller,
        templateUrl: pages[i].template
    });
  }
  $routeProvider.otherwise({
    redirectTo: '/ioc'
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

app.config(function($uibTooltipProvider) {
    $uibTooltipProvider.options({
        appendToBody: true
    });
});

// utilities

app.filter('blank', function() {
    return function(input) {
        if (typeof(input) == 'undefined' || input === '') {
            return 'none';
        } else {
            return input;
        }
    };
});
