<?php
if (!defined('ROOT')) define('ROOT', './api');
include_once ROOT.'/controllers/Ioc.php';
include_once ROOT.'/controllers/Set.php';
include_once ROOT.'/controllers/Report.php';
include_once ROOT.'/controllers/Client.php';

// var_export($_POST);
// var_export($_FILES);

if (isset($_POST['action'], $_POST['type'])) {
	if (isset($_POST['iformat']) && $_POST['action'] == 'import') {
		$file = $_FILES['file']; 
		if ($file['size'] != 0) {
			importData($_POST['type'], $_POST['iformat'], $file['tmp_name']);
		} else {
			// TODO: error - no file
		}
	} else if (isset($_POST['eformat']) && $_POST['action'] == 'export') {
		exportData($_POST['type'], $_POST['eformat']);
	} else {
		// TODO: error - invalid action
	}
} else {
	// TODO: error - no action
}

function exportData($type, $format) {
	header('Content-Disposition: attachment; filename="' . $type . '-' . time() . '.' . $format . '"');
	header('Content-Type: application/octet-stream');
	header('Connection: close');
	
	switch ($type) {
		case 'ioc':
			$iocApi = new Ioc([]);
			$iocList = array_map(function($e) {
				unset($e['id'], $e['parent']);
				return $e;
			}, array_values($iocApi->listAvailableAction()));
			
			switch ($format) {
				case 'json':
					$iocList = array_map(function($e) { // explode values in json
						unpackValues($e['value']);
						return $e;
					}, $iocList);
					echo json_encode($iocList, JSON_PRETTY_PRINT);
					break;
				case 'csv':
					$output = fopen('php://output', 'w');
					fputcsv($output, ['name', 'type', 'value']);
					foreach ($iocList as $row) fputcsv($output, $row);
					fclose($output);
					break;
				default:
					// TODO: error - unsupported format
			}
			break;
		
		case 'set':
			$setApi = new Set([]);
			$setNames = array_map(function($e) {
				return $e['name'];
			}, $setApi->listNamesAction());
			$clientApi = new Client([]);
			foreach ($setNames as $name) {
				foreach ($clientApi->setParams(['name' => $name])->requestAction() as $root) {
					$set[] = cleanTree($root);
				}
				$setList[] = ['name' => $name, 'data' => $set];
				$set = [];
			}
			
			switch ($format) {
				case 'json':
					echo json_encode($setList, JSON_PRETTY_PRINT);
					break;
				default:
					// TODO: error - unsupported format
			}
			break;
		
		case 'rep':
			$repApi = new Report([]);
			$repList = $repApi->listAllAction();
			
			switch ($format) {
				case 'json':
					$groupedList = [];
					foreach ($repList as &$row) {
						unset($row['id']);
						$key = $row['org'] . $row['device'] . $row['timestamp'] . $row['setname'];
		 				if (!isset($groupedList[$key])) {
			 				$groupedList[$key] = [
			 						'org' => $row['org'],
			 						'device' => $row['device'],
			 						'timestamp' => $row['timestamp'],
			 						'setname' => $row['setname'],
			 						'indicators' => []
			 				];
		 				}
		 				$groupedList[$key]['indicators'][] = ['id' => $row['ioc_id'], 'result' => $row['result']];
					}
					$groupedList = array_values($groupedList);
					echo json_encode($groupedList, JSON_PRETTY_PRINT);
					break;
				case 'csv':
					$iocApi = new Ioc([]);
					foreach ($repList as &$report) {
						$result = $report['result'];
						$id = $report['ioc_id'];
						unset($report['id'], $report['ioc_id'], $report['result']);
						$report['iocname'] = $iocApi->setParams(['id' => $id])->getAction()['name'];
						$report['result'] = $result? 'found' : 'clear';
					}
					$output = fopen('php://output', 'w');
					fputcsv($output, ['org', 'device', 'timestamp', 'set_name', 'ioc_name', 'result']);
					foreach ($repList as $row) fputcsv($output, $row);
					fclose($output);
					break;
				default:
					// TODO: error - unsupported format
			}
			break;
		default:
			// TODO: error - invalid type
	}
}

function importData($type, $format, $filename) {
	switch ($type) {
		case 'ioc':
			$iocList = [];
			switch ($format) {
				case 'json':
					$iocList = json_decode(file_get_contents($filename), true);
					foreach ($iocList as &$ioc) packValues($ioc['value']);
					break;
				case 'csv':
					$iocList = parseCsv(file($filename));
					break;
				default:
					// TODO: error - unsupported format
			}
			
			if (!is_array($iocList) || isIocData($iocList)) $iocList = [$iocList];
			
			$iocApi = new Ioc([]);
			foreach ($iocList as $ioc) {
				if (isIocData($ioc)) {
					$iocApi->setParams($ioc)->addAction();
				} else {
					// TODO: error - bad data
				}
			}
			break;
		case 'set':
			$setList = [];
			switch ($format) {
				case 'json':
					$setList = json_decode(file_get_contents($filename), true);
					break;
				default:
					// TODO: error - unsupported format
			}

			if (!is_array($setList) || isSetData($setList)) $setList = [$setList];
				
			$iocApi = new Ioc([]);
			$setApi = new Set([]);
			foreach ($setList as $set) {
				if (isSetData($set)) {
					foreach ($set['data'] as $root) {
						importTree($set['name'], $root, 0);
					}
				} else {
					// TODO: error - bad data
				}
			}
			break;
		case 'rep':
			$repList = [];
			switch ($format) {
				case 'json':
					$repList = json_decode(file_get_contents($filename), true);
					break;
				default:
					// TODO: error - unsupported format
			}
			
			if (!is_array($repList) || isRepData($repList)) $repList = [$repList];
			
			$clientApi = new Client([]);
			foreach ($repList as $report) {
				if (isRepData($report)) {
					$clientApi->setParams(['report' => json_encode($report)])->uploadAction();
				} else {
					// TODO: error - bad data
				}
			}
			break;
		default:
			// TODO: error - invalid type
	}
	header('Location: https://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'#/backup');
}

// util functions

function cleanTree(&$node) {
	unset($node['id']);
	if (isset($node['children'])) 
		foreach($node['children'] as &$child) $child = cleanTree($child);
	return $node;
}

function importTree($name, $node, $parentId) {
	if ($node['type'] == 'and' || $node['type'] == 'or') {
		$setApi = new Set(['name' => $name, 'type' => $node['type'], 'parent' => $parentId]);
		$id = $setApi->addAction()['id'];
		if (isset($node['children'])) {
			foreach ($node['children'] as $child) {
				importTree($name, $child, $id);
			}
		}
	} else {
		packValues($node['value']);
		$iocApi = new Ioc(['name' => $node['name'], 'type' => $node['type'], 'value' => $node['value']]);
		$id = $iocApi->addAction()['id'];
		$setApi = new Set(['name' => $name, 'type' => 'ioc', 'parent' => $parentId, 'ioc' => $id]);
		$setApi->addAction();
	}
}

function packValues(&$values) {
	$values = implode('|', preg_replace('/([`\|])/', '`$1', $values));
	if ($values != '') $values .= '|';
}

function unpackValues(&$values) {
	$values = preg_replace('/`(.)/', '$1', preg_split("/(?<!`)\|/", $values));
	array_pop($values);
}

function parseCsv($lines) {
    $csv = array_map('str_getcsv', $lines);
        
    // find indices of relevant fields
    for ($i = 0; $i < count($csv[0]); $i++) {
        // names of relevant fields for different input formats should be stored in some constant (instead of hardcoded)
        if ($csv[0][$i] === 'name')
            $iName = $i;
    	if ($csv[0][$i] === 'type')
            $iType = $i;
        if ($csv[0][$i] === 'value')
            $iValue = $i;
    }
    
    // extract the relevant fields
    $list = [];
    for ($i = 1; $i < count($csv); $i++) {
        $indicator = [
            'name' => $csv[$i][$iName],
        	'type' => $csv[$i][$iType],
            'value' => $csv[$i][$iValue],
        ];
        $list[] = $indicator;
    }
    return $list;
}

function isValueArray($data) {
	if (!is_array($data)) return false;
	foreach ($data as $element) {
		if (gettype($element) != 'string') return false;
	}
	return true;
}

function isIocData($data) {
	return isset($data['name'],$data['type'],$data['value']) && isValueArray($data['value']);
}

function isIocTree($data) {
	if (isIocData($data)) return true;
	if (!isset($data['name'], $data['type'], $data['children'])) return false;
	foreach ($data['children'] as $element) {
		if (!isIocTree($element)) return false;
	}
	return true;
}

function isSetData($data) {
	if (!isset($data['name'], $data['data']) || !is_array($data['data'])) return false;
	foreach ($data['data'] as $element) {
		if (!isIocTree($element)) return false;
	}
	return true;
}

function isRepData($data) {
	if (!isset($data['org'],$data['device'],$data['timestamp'],$data['setname'],$data['indicators'])) return false;
	foreach ($data['indicators'] as $element) {
		if (!isset($element['id'], $element['result'])) return false;
		if (gettype($element['id']) != 'integer' || gettype($element['result']) != 'integer') return false;
	}
	return true;
}

?>
