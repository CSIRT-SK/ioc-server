<?php
/*
API functions working with IOCs
*/
if (!defined('ROOT')) define('ROOT', '..');
include_once ROOT.'/controllers/Web.php';
include_once ROOT.'/models/DBConnect.php';

class Set extends Web {

    public function listNamesAction() {
        return $this->db->setListNames();
    }
    
    public function getAction() {
        $this->checkParams('name');
        return $this->db->setFetchName($this->params['name']);
    }
 
    public function addAction() {
        $this->checkParams('name', 'type', 'parent');
        if ($this->params['type'] == 'ioc') {
        	$this->checkParams('ioc');
        	return ['id' => $this->db->setAdd($this->params['name'], $this->params['parent'], $this->params['type'], $this->params['ioc'])];
        } else if ($this->params['type'] == 'and' || $this->params['type'] == 'or') {
        	return ['id' => $this->db->setAdd($this->params['name'], $this->params['parent'], $this->params['type'], null)];
        } else {
        	throw new Exception('Unsupported type "' . $this->params['type'] . '"');
        }
    }
    
    public function hideAction() {
        $this->checkParams('id', 'hidden');
        return ['changed' => $this->db->setHide($this->params['id'], $this->params['hidden'])];
    }
}
?>