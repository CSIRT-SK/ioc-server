<?php
/*
Privately accessible API base
*/
if (!defined('ROOT')) define('ROOT', '..');
include_once ROOT.'/controllers/AbstractController.php';
include_once ROOT.'/models/DBConnect.php';
include_once ROOT.'/models/dbInfo.php';

abstract class Web extends AbstractController {

    public function __construct($params) {
        if (!isset($_SERVER['SSL_CLIENT_S_DN_CN']) || $_SERVER['SSL_CLIENT_S_DN_CN'] != ADMIN_CERT)
        	throw new Exception('Access denied');
        parent::__construct($params);
    }

}
?>