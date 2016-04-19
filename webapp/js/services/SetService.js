app.factory('SetService', ['ApiCall', function(ApiCall) {
    var service = {};

    service.listNames = function() {
        var data = {
            controller: 'set',
            action: 'listNames'
        };
        return ApiCall(data);
    };
    
    service.getName = function(name) {
        var data = {
            controller: 'set',
            action: 'get',
            name: name
        };
        return ApiCall(data);
    };

    service.addIoc = function(name, iocId) {
        var data = {
            controller: 'set',
            action: 'add',
            name: name,
            ioc: iocId
        };
        return ApiCall(data);
    };
    
    service.hideIoc = function(name, iocId) {
        var hidden = 1;
        var data = {
            controller: 'set',
            action: 'hide',
            name: name,
            ioc: iocId,
            hidden: hidden
        };
        return ApiCall(data);
    };
    
    return service;
}]);