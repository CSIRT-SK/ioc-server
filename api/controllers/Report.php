<?php
/*
API functions working with IOCs
*/
if (!defined('ROOT')) define('ROOT', '..');
include_once ROOT.'/controllers/Web.php';
include_once ROOT.'/models/DBConnect.php';

class Report extends Web {

    public function listAllAction() {
        $reps = $this->db->repFetchList();
        foreach ($reps as &$rep) {
        	$rep['data'] = $this->unpackArray($rep['data']);
        }
        return $reps;
    }
    
    public function getAction() {
        $this->checkParams('id');
        $rep = $this->db->repFetchId($this->params['id']);
        $rep['data'] = $this->unpackArray($rep['data']);
        return $rep;
    }
 
    public function getTimeRangeAction() {
        $this->checkParams('from', 'to');
        $reps = $this->db->repFetchTimeRange($this->params['from'], $this->params['to']);
        foreach ($reps as &$rep) {
        	$rep['data'] = $this->unpackArray($rep['data']);
        }
        return $reps;
    }
}
?>