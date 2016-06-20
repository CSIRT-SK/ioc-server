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
        } else  if (in_array($this->params['type'], ['and', 'or', 'root'])) {
        	$this->params['ioc'] = null;
		} else {
        	throw new Exception('Unsupported type "' . $this->params['type'] . '"');
        }
        
        if ($this->params['type'] == 'root' && $this->db->setFetchRoot($this->params['name'], 0)) throw new Exception('Set "' . $this->params['name'] . '" already exists');
        
        $exists = $this->db->setHiddenRowExists($this->params['name'], $this->params['parent'], $this->params['type'], $this->params['ioc']);
        if ($exists == null) {
        	return ['id' => $this->db->setAdd($this->params['name'], $this->params['parent'], $this->params['type'], $this->params['ioc'])];
        } else {
        	$this->db->setHide($exists['id'], false);
        	return $exists;
        }
    }
    
    public function hideAction() {
        $this->checkParams('id', 'hidden');
        if ($this->params['hidden']) {
        	$changed = $this->hideRec($this->params['id']);

        	$entry = $this->db->setFetchId($this->params['id']);
        	if (count($this->db->setFetchName($entry['name'])) == 0) { // removed last node - is empty
        		$rootId = $this->db->setFetchRoot($entry['name'], 0)['id'];
        		$changed += $this->db->setHide($rootId, true); // hide root
        	}
        } else {
        	$changed = $this->db->setHide($this->params['id'], false);

        	$entry = $this->db->setFetchId($this->params['id']);
        	if (count($this->db->setFetchName($entry['name'])) > 0) { // readded first node - readd root
        		$rootId = $this->db->setFetchRoot($entry['name'], 1)['id'];
        		$changed += $this->db->setHide($rootId, false);
        	}
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