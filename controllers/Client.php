<?php
/*
Publicly exposed API functions
*/
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
include_once ROOT.'/models/DBConnect.php';
include_once ROOT.'/controllers/AbstractController.php';

class Client extends AbstractController {
    
    public function requestAction() {
    // returns all entries in a set
        $this->checkParams('name');
        
        // fetch all IOCs in DB
        $result = $this->db->iocFetchList();
        $iocList = array_map(function ($e) {
        	$e['value'] = preg_replace('/`(.)/', '$1', preg_split("/(?<!`)\|/", $e['value']));
        	array_pop($e['value']);
        	return $e;
        }, $result);
        $setEntries = $this->db->setFetchName($this->params['name']);
		if (empty($setEntries)) throw new Exception('Set not found');
        foreach ($setEntries as $row) {
        	$iocList[$row['ioc_id']]['parent'] = $row['parent_id'];
        }
        
        $setTree = [];
        foreach ($iocList as $key => &$entry) {
            if (isset ($entry['parent'])) {
            	if ($entry['parent'] == 0)
            		$setTree[] = &$entry;
            	else
            		$iocList[$entry['parent']]['children'][] = &$entry;
            }
            unset($entry['parent']);
        }
              
        return $setTree;
    }
    
    public function uploadAction() {
    // retrieve report from client
        $this->checkParams('report');
        
        $report = json_decode($this->params['report'], true);
        
        if ($report == null)
            throw new Exception('Not a valid JSON: ' . $this->params['report']);
        
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

        $result = $this->db->repAddMulti($report);
        
        return ['added' => $result];
    }
}
?>