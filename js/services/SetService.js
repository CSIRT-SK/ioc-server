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

    service.getId = function(id) {
        var data = {
                controller: 'set',
                action: 'getEntry',
                id: id
            };
            return ApiCall(data);
    };
    
    service.addIoc = function(name, type, parent, iocId) {
        var data = {
            controller: 'set',
            action: 'add',
            name: name,
            type: type,
            parent: parent,
            ioc: iocId
        };
        return ApiCall(data);
    };
    
    service.hideIoc = function(id) {
        var hidden = 1;
        var data = {
            controller: 'set',
            action: 'hide',
            id: id,
            hidden: hidden
        };
        return ApiCall(data);
    };
    
    return service;
}]);