<?php
/*
API functions working with IOCs
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/controllers/Web.php';
include_once ROOT.'/models/DBConnect.php';

class Ioc extends Web {

    public function getTypesAction() {
        return $this->db->getIndicatorTypes();
    }

    public function listAvailableAction() {
        return $this->db->iocFetchList();
    }
    
    public function  listUnusedAction() {
    	return $this->db->iocFetchUnused();
    }
    
    public function listHiddenAction() {
        return $this->db->iocFetchHidden();
    }
 
    public function getAction() {
        $this->checkParams('id');
        return $this->db->iocFetchId($this->params['id']);
    }
 
    public function addAction() {
        $this->checkParams('name', 'type');
        if (!isset($this->params['value'])) $this->params['value'] = null;
        if (!isset($this->params['parent'])) $this->params['parent'] = 0;
        return ['id' => $this->db->iocAdd($this->params['name'], $this->params['type'], $this->params['value'], $this->params['parent'])];
    }
    
    public function updateAction() {
        $this->checkParams('id', 'name', 'type');
        if (!isset($this->params['value'])) $this->params['value'] = null;
        if (!isset($this->params['parent'])) $this->params['parent'] = 0;
        return ['changed' => $this->db->iocUpdate($this->params['id'], $this->params['name'], $this->params['type'], $this->params['value'], $this->params['parent'])];
    }
    
    public function changeParentAction() {
        $this->checkParams('id', 'parent');
        return ['changed' => $this->db->iocUpdateParent($this->params['id'], $this->params['parent'])];
    }
    
    public function hideAction() {
        $this->checkParams('id', 'hidden');
        return ['changed' => $this->db->iocSetHidden($this->params['id'], $this->params['hidden'])];
    }
}
?>