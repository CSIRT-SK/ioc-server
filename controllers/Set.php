<?php
/*
API functions working with IOCs
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
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
        $this->checkParams('name', 'ioc');
        $exists = $this->db->setExists($this->params['name'], $this->params['ioc'])['ioc_id'];
        if ($exists == null) {
            return ['id' => $this->db->setAdd($this->params['name'], $this->params['ioc'])];
        } else {
            $this->db->setHide($this->params['name'], $this->params['ioc'], 0);
            return ['id' => $exists];
        }
    }
    
    public function hideAction() {
        $this->checkParams('name', 'ioc', 'hidden');
        return ['changed' => $this->db->setHide($this->params['name'], $this->params['ioc'], $this->params['hidden'])];
    }
}
?>