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
        
        $report = json_decode($this->params['report'], true);
        
        if ($report == null)
            throw new Exception('Not a valid JSON');
        
        /* Report format
            {
                "org": x,
                "device": x,
                "timestamp": x,
                "setname": x,
                "indicators": [
                    {
                        "id": x,
                        "result": x
                    },
                    {
                        "id": x,
                        "result": x
                    },
                    ...
                ]
            }
        */
        $missing = $this->checkArrayEntries($report, 'org', 'device', 'timestamp', 'setname', 'indicators');
        if ($missing != null)
            throw new Exception('Report missing entries: ' . $missing);
        
        foreach($report['indicators'] as $indicator) {
            $missing = $this->checkArrayEntries($indicator, 'id', 'result');
            if ($missing != null)
                throw new Exception('Indicator entry missing entries: ' . $missing);
        }

        $db = new DBConnect();
        
//        foreach($report['indicators'] as $indicator) {
//            $db->repAdd($report['org'], $report['device'], $report['timestamp'], $report['setname'], $indicator['id'], $indicator['result']);
//        }
        
        $result = $db->repAddMulti($report);
        
        return $result;
        //return count($report['indicators']);
    }
    
    protected function checkParams(...$entries) {
        $missingParams = $this->checkArrayEntries($this->params, ...$entries);
        if ($missingParams != null)
            throw new Exception('Action requires parameters: ' . $missingParams);
    }
    
    protected function checkArrayEntries($array, ...$entries) {
    // check if params are set
        $missing = '';
        foreach ($entries as $e) {
            if (!isset($array[$e]))
                $missing .= $e . ', ';
        }
        if ($missing != '')
            return rtrim($missing, ', ');
    }
}
?>