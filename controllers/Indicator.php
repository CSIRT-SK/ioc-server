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
        $db = new DBConnect();
        
        $result = $db->iocFetchList();
        
        $db->close();
        
        return $result;
    }
    
    public function getAction() {
    // returns one entry based on id
        $db = new DBConnect();
        
        $missingParams = '';
        if (!isset($this->params['id']))
            $missingParams .= 'id ';
        if ($missingParams != '')
            throw new Exception('Parameters required: ' . $missingParams);
        
        $result = $db->iocFetchId($this->params['id']);
        
        $db->close();
        
        return $result;
    }
    
    /*
      == Add & Edit ==
      Adding and Editing should not be available in the client API.
      Clients are not supposed to alter the IOC database, only read from it. Making them accessible is just needless risk.
      
      Question is: Should they remain in the API hidden behind some security check, so that only the admin aplication would
      be able to use them, OR remove them from the API completely and the administration app will call the methods directly
      (the methods could remain the same, they will just not be accessible through URL)
    */
    
    public function addAction() {
    // adds new entry to the database
        $db = new DBConnect();
        
        $missingParams = '';
        if (!isset($this->params['name']))
            $missingParams .= 'name ';
        if (!isset($this->params['type']))
            $missingParams .= 'type ';
        if (!isset($this->params['value']))
            $missingParams .= 'value ';
        // not required
        if (!isset($this->params['value2']))
            $this->params['value2'] = NULL;
        if ($missingParams != '')
            throw new Exception('Parameters required: ' . $missingParams);
        
        $result = $db->iocAdd($this->params['name'], $this->params['type'], $this->params['value'], $this->params['value2']);
        
        $db->close();
        
        return $result;
    }

    public function editAction() {
    // adds new entry to the database
        $db = new DBConnect();
        
        $missingParams = '';
        if (!isset($this->params['id']))
            $missingParams = $missingParams . 'id ';
        if (!isset($this->params['name']))
            $missingParams = $missingParams . 'name ';
        if (!isset($this->params['type']))
            $missingParams = $missingParams . 'type ';
        if (!isset($this->params['value']))
            $missingParams = $missingParams . 'value ';
        if (!isset($this->params['hidden'])) 
            $missingParams = $missingParams . 'hidden ';
        // not required
        if (!isset($this->params['value2'])) 
            $this->params['value2'] = NULL;
        if ($missingParams != '')
            throw new Exception('Parameters required: ' . $missingParams);
        
        $result = $db->iocEdit($this->params['id'], $this->params['name'], $this->params['type'],
                            $this->params['value'], $this->params['value2'], $this->params['hidden']);
        
        $db->close();
        
        return $result;
    }
}
?>