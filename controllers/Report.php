<?php
/*
API functions working with IOCs
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/controllers/Web.php';
include_once ROOT.'/models/DBConnect.php';

class Report extends Web {

    public function listAllAction() {
        return $this->db->repFetchList();
    }
    
    public function getAction() {
        $this->checkParams('id');
        return $this->db->repFetchId($this->params['id']);
    }
 
    public function queryAction() {
        // TODO variable query on the report list, should take query string as parameter
        return null;
    }
}
?>