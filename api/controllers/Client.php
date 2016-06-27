<?php
/*
Publicly exposed API functions
*/
if (!defined('ROOT')) define('ROOT', '..');
include_once ROOT.'/models/DBConnect.php';
include_once ROOT.'/controllers/AbstractController.php';

class Client extends AbstractController {
    
    public function requestAction() {
    // returns all entries in a set
        $this->checkParams('name');
        
        // fetch all IOCs in DB
        $result = $this->db->iocFetchList();
        $iocList = array_map(function ($e) {
			$e['value'] = $this->unpackArray($e['value']);
        	return $e;
        }, $result);

        $setEntries = $this->db->setFetchName($this->params['name']);
		if (empty($setEntries)) throw new Exception('Set not found');
        
		$setTree = [];
		foreach ($setEntries as &$entry) {
			$setId = $entry['id'];
			if ($entry['parent_id'] == 0) {
				$setTree[] = &$entry;
 				unset($entry['id'], $entry['parent_id']);
			} else {
				$setEntries[$entry['parent_id']]['children'][] = &$entry;
			}
			
			if ($entry['type'] == 'ioc') {
				$entry = $iocList[$entry['ioc_id']];
				$entry['ioc_id'] = $entry['id'];
			} else {
				$entry['name'] = '#' . $setId;
 				unset($entry['ioc_id']);
			}
			$entry['id'] = $setId;
			
 			if (!isset($this->params['iocids']))
 				unset($entry['ioc_id']);
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
                "dev": x,
                "timestamp": x,
                "set": x,
                "results": [
                    {
                        "id": x,
                        "result": x,
                        "data":["x", "xx"]
                    },
                    {
                        "id": x,
                        "result": x,
                        "data":["x", "xx"]
                    },
                    ...
                ]
            }
        */
        $missing = $this->checkArrayEntries($report, 'org', 'dev', 'timestamp', 'set', 'results');
        if ($missing != null)
            throw new Exception('Report missing entries: ' . $missing);
        
        foreach($report['results'] as &$indicator) {
            $missing = $this->checkArrayEntries($indicator, 'id', 'result', 'data');
            if ($missing != null)
                throw new Exception('Indicator entry missing entries: ' . $missing);
            $indicator['data'] = $this->packArray($indicator['data']);
        }

        $setTree = ['type' => 'or', 'id' => $this->db->setFetchRoot($report['set'], 0)['id'], 'children' => $this->setParams(['name' => $report['set']])->requestAction()];
        
        $this->evaluateTree($setTree, $report['results']);
        
//      var_export($report['results']);
        
        $result = $this->db->repAddMulti($report);
        
        return ['added' => $result];
    }
    
    private function evaluateTree(&$node, &$results) {
    	if ($node['type'] == 'and') {
    		// calc from children
    		$res = true;
    		foreach ($node['children'] as &$child) {
    			$this->evaluateTree($child, $results);
    			if ($child['result'] === null) continue; // results were not uploaded
    			$res = $res && $child['result'];
    		}
    		$node['result'] = $res;
    		
    		$results[] = ['id' => $node['id'], 'result' => $node['result'], 'data' => ''];
    	} else  if ($node['type'] == 'or') {
    		// calc from children
			$res = false;
    		foreach ($node['children'] as &$child) {
    			$this->evaluateTree($child, $results);
    			if ($child['result'] === null) continue; // results were not uploaded
    			$res = $res || $child['result'];
    		}
    		$node['result'] = $res;

    		$results[] = ['id' => $node['id'], 'result' => $node['result'], 'data' => ''];
    	} else {
    		// find result
    		foreach ($results as $res) {
    			if ($res['id'] == $node['id']) {
    				$node['result'] = $res['result'];
    				return;
    			}
    		}
    		$node['result'] = null;
    	}
    }
}
?>