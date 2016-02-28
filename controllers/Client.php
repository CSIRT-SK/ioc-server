<?php
/*
Controller for various API calls related to the IOC database
*/
class Client {
    protected $params;
    
    public function __construct($params) {
        $this->params = $params;
    }
    
    public function requestAction() {
    // returns all entries in a set
        $this->checkParams('name');
        
        $db = new DBConnect();
        
        // fetch all IOCs in DB
        $result = $db->iocFetchList();
        $iocList = $result;
        
        foreach ($iocList as $key => &$entry) {
            if ($entry['parent'] != 0)
                $iocList[$entry['parent']]['children'][] = &$entry;
            unset($entry['parent']);
        }
        
        $result = $db->setFetchName($this->params['name']);
        
        $root = $iocList[$result['ioc_id']];
        //$root = $this->expandIocTree($root, $iocList);
        
        return $root;
    }
    
    public function uploadAction() {
    // retrieve report from client
        $this->checkParams('report');
        
        // parse data from JSON
        // throw errormessage exceptions if somethig is wrong
        // put them into database
        // return success message
        
        return $this->params['report']; // for now just reply with same data
    }
    
    protected function checkParams(...$params) {
    // check if params are set
        $missingParams = '';
        foreach ($params as $p) {
            if (!isset($this->params[$p]))
                $missingParams .= $p . ', ';
        }
        if ($missingParams != '')
            throw new Exception('Parameters required: ' . rtrim($missingParams, ', '));
    }
}
?>