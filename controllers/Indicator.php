<?php
/*
Controller for various API calls related to the IOC database
*/
class Indicator {
    private $params;
    
    public function __construct($params) {
        $this->params = $params;
    }
    
    public function listAction() {
    // returns all entries in the IOC database
        $db = new DBView();
        
        $result = $db->iocFetchList();
        
        $db->close();
        
        return $result;
    }
    
    public function getAction() {
    // returns one entry based on id
        $db = new DBView();
        
        $result = $db->iocFetchId($this->params['id']);
        
        $db->close();
        
        return $result;
    }
}
?>