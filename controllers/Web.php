<?php
/*
Privately accessible API base
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/controllers/AbstractController.php';
include_once ROOT.'/models/DBConnect.php';

abstract class Web extends AbstractController {

    public function __construct($params) {
        if (!isset($_SERVER['SSL_CLIENT_S_DN_CN']) || $_SERVER['SSL_CLIENT_S_DN_CN'] != 'IOC Checker admin')
        	throw new Exception('Access denied');
        parent::__construct($params);
    }

}
?>