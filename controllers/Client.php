<?php
/*
Publicly exposed API functions
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/models/DBConnect.php';

class Client {
    private $params;
    
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
        
        $results = $db->setFetchName($this->params['name']);
        //echo var_export($iocList);
        foreach ($results as $row) {
            $root[] = $iocList[$row['ioc_id']];
        }
        
        $db->close();
        
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
        
        $result = $db->repAddMulti($report);
        
        $db->close();
        
        return ['added' => $result];
    }
    
    private function checkParams(...$entries) {
        $missingParams = $this->checkArrayEntries($this->params, ...$entries);
        if ($missingParams != null)
            throw new Exception('Action requires parameters: ' . $missingParams);
    }
    
    private function checkArrayEntries($array, ...$entries) {
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