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
 
    public function getEntryAction() {
    	$this->checkParams('id');
    	return $this->db->setFetchId($this->params['id']);
    }
    
    public function getChildrenAction() {
    	$this->checkParams('id');
    	return $this->db->setGetChildren($this->params['id']);
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
        if ($this->params['hidden']) {
        	$changed = $this->hideRec($this->params['id']);
        } else {
        	$changed = $this->db->setHide($id, false);
        }
        return ['changed' => $changed];
    }
    
    private function hideRec($id) {
    	$chg = 0;
    	$chg += $this->db->setHide($id, true);
    	$children = $this->db->setGetChildren($id);
    	$chg += $this->db->setHideChildren($id, true);
    	foreach ($children as $child) {
    		$chg += $this->hideRec($child);
    	}
    	return $chg;
    }
    
}
?>