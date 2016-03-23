<?php
/*
Extensible endpoint for remote API interaction
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/models/DBConnect.php';

// all errors encountered during the API call evaluation will be thrown out of the try block
try{
    $params = $_REQUEST;
    
    // get controller name from the request
    if (isset($params['controller'])) {
        $controller = ucfirst(strtolower($params['controller']));
    } else {
        throw new Exception('Controller not set.');
    }
    
    // get action name from the request
    if (isset($params['action'])) {
        $action = strtolower($params['action']) . 'Action';
    } else {
        throw new Exception('Action not set.');
    }
    
    // check if controller exists
    if (file_exists(ROOT."/controllers/{$controller}.php")) {
        include_once ROOT."/controllers/{$controller}.php";
    } else {
        throw new Exception('Invalid controller.');
    }
    
    // create the controller object
    $controller = new $controller($params);
    
    // check if action exists
    if (!method_exists($controller, $action)) {
        throw new Exception('Invalid action.');
    }
    
    // execute the action and return results
    $result = array();
    $result['success'] = true;
    $result['data'] = $controller->$action();
    
} catch (Exception $e) {
    // return error message
    $result = array();
    $result['success'] = false;
    $result['errormsg'] = $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);
exit();
?>