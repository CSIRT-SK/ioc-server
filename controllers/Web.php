<?php
/*
Privately accessible API base
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/controllers/AbstractController.php';
include_once ROOT.'/models/DBConnect.php';

abstract class Web extends AbstractController {

    public function __construct($params) {
        // TODO key check
        parent::__construct($params);
    }

}
?>