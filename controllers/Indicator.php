<?php
/*
Controller for various API calls related to the IOC database
*/
class Indicator {
    private $_params;
    
    public function __construct($params) {
        $this->_params = $params;
    }
    
    public function listAction() {
    // returns all entries in the IOC database
        $db = new DBView();
        
        $result = $db->fetchIOCList();
        
        $db->close();
        
        return $result;
    }
}
?>