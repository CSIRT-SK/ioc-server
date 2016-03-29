<?php
/*
Privately accessible API functions
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/controllers/Client.php';
include_once ROOT.'/models/DBConnect.php';

class Web2 {

    private $db;

    public function __construct() {
        $this->db = new DBConnect();
    }

    public function __destruct() {
        $this->db->close();
    }
    
    // returns array of all nonhidden indicators
    public function iocList() {
        return $this->db->iocFetchList();
    }
 
    // returns ioc with given id or null
    public function iocGet(int $id) {
        $result = $this->db->iocFetchId($id);
        if (count($result) > 0) return $result[0];
    }
 
    // inserts new ioc into database and returns its id
    public function iocAdd(string $name, string $type, $value, int $parent = 0) {
        return $this->db->iocAdd($name, $type, $value, $parent);
    }
 
    // updates ioc with given id, returns affected rows (0 = id does not exist, 1 = ok)
    public function iocUpdate(int $id, string $name, string $type, $value, int $parent_id) {
        return $this->db->iocUpdate($id, $name, $type, $value, $parent_id);
    }
 
    // returns array of hidden indicators
    public function iocListHidden() {
        return $this->db->iocFetchHidden();
    }
 
    // sets hidden status for ioc with given id, returns affected rows
    public function iocHide(int $id, bool $hidden) {
        return $this->db->iocSetHidden($id, $hidden);
    }
 
    // returns array of all set names
    public function setList() {
        return $this->db->setListNames();
    }
 
    // returns all iocs in given set
    public function setGet(string $name) {
        return $this->db->setFetchName($name);
    }
    
    public function setGetTree(string $name) {
        $client = new Client(['name' => $name]);
        return $client->requestAction();
    }
    
    // add new set entry
    public function setAddIoc(string $name, int $ioc_id) {
        return $this->db->setAdd($name, $ioc_id);
    }
 
    // change hidden status for an entry
    public function setHide(string $name, int $ioc_id, bool $hidden) {
        return $this->db->setHide($name, $ioc_id, $hidden);
    }
 
    // change hidden status for a set
    public function setHideAll(string $name, bool $hidden) {
        return $this->db->setHideAll($name, $hidden);
    }
 
    // list all hidden entries
    public function setListHidden() {
        return $this->db->setFetchHidden();
    }
 
    // list all reports
    public function repList() {
        return $this->db->repFetchList();
    }
    
    public function repGet(int $id) {
        return $this->db->repFetchId($id);
    }
}
?>