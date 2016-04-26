app.factory('IocService', ['ApiCall', function(ApiCall) {
    var service = {};

    service.iocTypes = function() {
        var data = {
            controller: 'ioc',
            action: 'getTypes'
        };
        return ApiCall(data);
    };
    
    service.listAvailable = function() {
        var data = {
            controller: 'ioc',
            action: 'listAvailable'
        };
        return ApiCall(data);
    };
    
    service.listHidden = function() {
        var data = {
            controller: 'ioc',
            action: 'listHidden'
        };
        return ApiCall(data);
    };
    
    service.get = function(id) {
        var data = {
            controller: 'ioc',
            action: 'get',
            id: id
        };
        return ApiCall(data);
    };
    
    service.add = function(ioc) {
        var data = {
            controller: 'ioc',
            action: 'add',
            name: ioc.name,
            type: ioc.type,
            value: ioc.value,
        };
        return ApiCall(data);
    };
    
    service.update = function(id, ioc) {
        var data = {
            controller: 'ioc',
            action: 'update',
            id: id,
            name: ioc.name,
            type: ioc.type,
            value: ioc.value,
        };
        return ApiCall(data);
    };
    
    service.hide = function(id, hidden) {
        var data = {
            controller: 'ioc',
            action: 'hide',
            id: id,
            hidden: hidden
        };
        return ApiCall(data);
    };
    
    service.tree = function(name) {
        var data = {
            controller: 'client',
            action: 'request',
            name: name
        };
        return ApiCall(data);
    };
    return service;
}]);