<?php
/*
API functions working with IOCs
*/
if (!defined('ROOT')) define('ROOT', '..');
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
 
    public function getTimeRangeAction() {
        $this->checkParams('from', 'to');
        return $this->db->repFetchTimeRange($this->params['from'], $this->params['to']);
    }
}
?>