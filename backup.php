<?php
if (!defined('ROOT')) define('ROOT', './api');
include_once ROOT.'/controllers/Ioc.php';
include_once ROOT.'/controllers/Set.php';
include_once ROOT.'/controllers/Report.php';
include_once ROOT.'/controllers/Client.php';

// var_export($_POST);
// var_export($_FILES);

try {
	if (isset($_POST['action'], $_POST['type'])) {
		if (isset($_POST['iformat']) && $_POST['action'] == 'import') {
			$file = $_FILES['file']; 
			if ($file['size'] != 0) {
				importData($_POST['type'], $_POST['iformat'], $file['tmp_name']);
			} else {
				throw new Exception('No file');
			}
		} else if (isset($_POST['eformat']) && $_POST['action'] == 'export') {
			exportData($_POST['type'], $_POST['eformat']);
		} else {
			throw new Exception('Invalid action');
		}
	} else {
		throw new Exception('No action');
	}
} catch (Exception $e) {
	headerRedirect(0, $e->getMessage());
}

function headerExport($type, $format) {
	header('Content-Disposition: attachment; filename="' . $type . '-' . time() . '.' . $format . '"');
	header('Content-Type: application/octet-stream');
	header('Connection: close');
}

function headerRedirect($success, $message) {
	header('Location: https://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'#/backup?success='.$success.'&message='.$message);
	exit();
}

function exportData($type, $format) {
	switch ($type) {
		case 'ioc':
			$iocApi = new Ioc([]);
			$iocList = array_map(function($e) {
				unset($e['id'], $e['parent']);
				return $e;
			}, array_values($iocApi->listAvailableAction()));
			
			switch ($format) {
				case 'json':
					headerExport($type, $format);
					echo json_encode($iocList, JSON_PRETTY_PRINT);
					exit();
				case 'csv':
					$iocList = array_map(function($e) { // explode values in json
						packValues($e['value']);
						return $e;
					}, $iocList);
					headerExport($type, $format);
					$output = fopen('php://output', 'w');
					fputcsv($output, ['name', 'type', 'value']);
					foreach ($iocList as $row) fputcsv($output, $row);
					fclose($output);
					exit();
				default:
					throw new Exception('Unsupported format');
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
					headerExport($type, $format);
					echo json_encode($setList, JSON_PRETTY_PRINT);
					exit();
				default:
					throw new Exception('Unsupported format');
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
						$key = $row['org'] . $row['dev'] . $row['timestamp'] . $row['set'];
		 				if (!isset($groupedList[$key])) {
			 				$groupedList[$key] = [
			 						'org' => $row['org'],
			 						'dev' => $row['device'],
			 						'timestamp' => $row['timestamp'],
			 						'set' => $row['setname'],
			 						'results' => []
			 				];
		 				}
		 				$groupedList[$key]['results'][] = ['id' => $row['ioc_id'], 'result' => $row['result'], 'data' => $row['data']];
					}
					$groupedList = array_values($groupedList);
					headerExport($type, $format);
					echo json_encode($groupedList, JSON_PRETTY_PRINT);
					exit();
				case 'csv':
					$iocApi = new Ioc([]);
					foreach ($repList as &$report) {
						$result = $report['result'];
						$id = $report['ioc_id'];
						$data = $report['data'];
						packValues($data);
						unset($report['id'], $report['ioc_id'], $report['result'], $report['data']);
						$ioc = $iocApi->setParams(['id' => $id])->getAction();
						packValues($ioc['value']);
						$report['iocname'] = $ioc['name'];
						$report['type'] = $ioc['type'];
						$report['value'] = $ioc['value'];
						$report['result'] = $result? 'found' : 'clear';
						$report['data'] = $data;
					}
					headerExport($type, $format);
					$output = fopen('php://output', 'w');
					fputcsv($output, ['org', 'dev', 'timestamp', 'set', 'ioc_name', 'ioc_type', 'ioc_value', 'result', 'data']);
					foreach ($repList as $row) fputcsv($output, $row);
					fclose($output);
					exit();
				default:
					throw new Exception('Unsupported format');
			}
			break;
		default:
			throw new Exception('Invalid type');
	}
}

function importData($type, $format, $filename) {
	switch ($type) {
		case 'ioc':
			$iocList = [];
			switch ($format) {
				case 'json':
					$iocList = json_decode(file_get_contents($filename), true);
// 					foreach ($iocList as &$ioc) $ioc['value'] = json_encode($ioc['value']);
					break;
				case 'csv':
					$iocList = parseCsv(file($filename));
					foreach ($iocList as &$ioc) unpackValues($ioc['value']);
					break;
				default:
					throw new Exception('Unsupported format');
			}
			
			if (!is_array($iocList) || isIocData($iocList)) $iocList = [$iocList];
			
			$iocApi = new Ioc([]);
			foreach ($iocList as $ioc) {
				if (isIocData($ioc)) {
					$ioc['value'] = json_encode($ioc['value']);
					$iocApi->setParams($ioc)->addAction();
				} else {
					throw new Exception('Bad data');
				}
			}
			headerRedirect(1, 'IOC import successful');
		case 'set':
			$setList = [];
			switch ($format) {
				case 'json':
					$setList = json_decode(file_get_contents($filename), true);
					break;
				default:
					throw new Exception('Unsupported format');
			}

			if (!is_array($setList) || isSetData($setList)) $setList = [$setList];
				
			$iocApi = new Ioc([]);
			$setApi = new Set([]);
			foreach ($setList as $set) {
				if (isSetData($set)) {
					$goodName = $set['name'];
					$iter = 1;
					$namePassed = false;
					while (!$namePassed) {
						$namePassed = true;
						try {
							$setApi->setParams(['name' => $goodName, 'type' => 'root', 'parent' => -1])->addAction();
						} catch (Exception $e) {
							$namePassed = false;
							$iter++;
							$goodName = $set['name'] . ' ' . $iter;
						}
					}
					foreach ($set['data'] as $root) {
						importTree($goodName, $root, 0);
					}
				} else {
					throw new Exception('Bad data');
				}
			}
			headerRedirect(1, 'Set import successful');
		case 'rep':
			$repList = [];
			switch ($format) {
				case 'json':
					$repList = json_decode(file_get_contents($filename), true);
					break;
				default:
					throw new Exception('Unsupported format');
			}
			
			if (!is_array($repList) || isRepData($repList)) $repList = [$repList];
			
			$clientApi = new Client([]);
			foreach ($repList as $report) {
				if (isRepData($report)) {
					$clientApi->setParams(['report' => json_encode($report)])->uploadAction();
				} else {
					throw new Exception('Bad data');
				}
			}
			headerRedirect(1, 'Report import successful');
		default:
			throw new Exception('Invalid type');
	}
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
		$iocApi = new Ioc(['name' => $node['name'], 'type' => $node['type'], 'value' => json_encode($node['value'])]);
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
	if (!isset($data['org'],$data['dev'],$data['timestamp'],$data['set'],$data['results'])) return false;
	foreach ($data['results'] as $element) {
		if (!isset($element['id'], $element['result'], $element['data'])) return false;
		if (gettype($element['id']) != 'integer' || (gettype($element['result']) != 'integer' && gettype($element['result']) != 'boolean' ) || !isValueArray($element['data'])) return false; //fix by LB
	}
	return true;
}

?>
