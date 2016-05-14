<?php
/*
API functions working with IOCs
*/
if (!defined('ROOT')) define('ROOT', '..');
include_once ROOT.'/controllers/Web.php';
include_once ROOT.'/models/DBConnect.php';

class Ioc extends Web {

    public function getTypesAction() {
        $types = $this->db->getIndicatorTypes();
        foreach ($types as &$type) {
        	$type['values_desc'] = $this->unpackArray($type['values_desc']);
        }
    	return $types;
    }

    public function listAvailableAction() {
        $iocs = $this->db->iocFetchList();
		foreach ($iocs as &$ioc) {
			$ioc['value'] = $this->unpackArray($ioc['value']);
		}
		return $iocs;
    }
    
    public function listHiddenAction() {
        $iocs = $this->db->iocFetchHidden();
		foreach ($iocs as &$ioc) {
			$ioc['value'] = $this->unpackArray($ioc['value']);
		}
		return $iocs;
    }
 
    public function getAction() {
        $this->checkParams('id');
        $ioc = $this->db->iocFetchId($this->params['id']);
        $ioc['value'] = $this->unpackArray($ioc['value']);
        return $ioc;
    }
 
    public function addAction() {
        $this->checkParams('name', 'type', 'value');
        $this->params['value'] = $this->packArray(json_decode($this->params['value'], true));
        return ['id' => $this->db->iocAdd($this->params['name'], $this->params['type'], $this->params['value'])];
    }
    
    public function updateAction() {
        $this->checkParams('id', 'name', 'type', 'value');
        $this->params['value'] = $this->packArray(json_decode($this->params['value'], true));
        return ['changed' => $this->db->iocUpdate($this->params['id'], $this->params['name'], $this->params['type'], $this->params['value'])];
    }
    
    public function hideAction() {
        $this->checkParams('id', 'hidden');
        return ['changed' => $this->db->iocSetHidden($this->params['id'], $this->params['hidden'])];
    }
}
?>