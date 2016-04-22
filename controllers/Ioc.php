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
        return ['id' => $this->db->iocAdd($this->params['name'], $this->params['type'], $this->params['value'])];
    }
    
    public function updateAction() {
        $this->checkParams('id', 'name', 'type');
        if (!isset($this->params['value'])) $this->params['value'] = null;
        return ['changed' => $this->db->iocUpdate($this->params['id'], $this->params['name'], $this->params['type'], $this->params['value'])];
    }
    
    public function hideAction() {
        $this->checkParams('id', 'hidden');
        return ['changed' => $this->db->iocSetHidden($this->params['id'], $this->params['hidden'])];
    }
}
?>