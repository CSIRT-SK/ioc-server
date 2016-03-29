var app = angular.module('iocApp', ['ngRoute', 'ngAnimate', 'ui.bootstrap']);

app.constant('pages', [
    {
        name: 'Home',
        path: '/',
        controller: 'HomeController',
        template: 'views/home.html'
    },
    {
        name: 'IOC',
        path: '/ioc',
        controller: 'IocController',
        template: 'views/ioc.html'
    },
    {
        name: 'Data',
        path: '/data',
        controller: 'DataController',
        template: 'views/data.html'
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

app.config(function($uibTooltipProvider) {
    $uibTooltipProvider.options({
        appendToBody: true,
        popupDelay: 500
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

var esc = '`';

app.filter('escape', function() {
    return function(input, special) {
        if (typeof(input) != 'string') {
            return input;
        } else {
            var output = '';
            for (var i = 0; i < input.length; i++) {
                var c = input.charAt(i);
                if (c == special) output += esc + c;
                else if (c == esc) output += esc + esc;
                else output += c;
            }
            return output;
        }
    };
});

app.filter('merge', ['$filter', function($filter) {
    return function(input, separator) {
        if (input instanceof Array) {
            var output = '';
            for (var i = 0; i < input.length; i++) {
                output += $filter('escape')(input[i], separator) + separator;
            }
            return output;
        } else {
            return input;
        }
    };
}]);

app.filter('unescape', function() {
    return function(input, special) {
        if (typeof(input) != 'string') {
            return input;
        } else {
            var output = '';
            var escaped = false;
            for (var i = 0; i < input.length; i++) {
                var c = input.charAt(i);
                if (!escaped) {
                    if (c == esc) escaped = true;
                    else output += c;
                } else {
                    output += c;
                    escaped = false;
                }
            }
            return output;
        }
    };
});

app.filter('split', ['$filter', function($filter) {
    return function(input, separator) {
        if (typeof(input) != 'string') {
            return input;
        } else {
            var output = [];
            var word = '';
            var escaped = false;
            for (var i = 0; i < input.length; i++) {
                var c = input.charAt(i);
                if (!escaped) {
                    if (c == separator) {
                        output.push($filter('unescape')(word, separator));
                        word = '';
                    } else if (c == esc) escaped = true;
                    else word += c;
                } else {
                    if (c == esc) word += esc + esc;
                    else word += c;
                    escaped = false;
                }
            }
            return output;
        }
    };
}]);
