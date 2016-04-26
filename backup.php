<?php
if (!defined('ROOT')) define('ROOT', '..');
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
// 	echo '<pre>';
	switch ($type) {
		case 'ioc':
			$iocList = [];
			switch ($format) {
				case 'json':
					$iocList = json_decode(file_get_contents($filename), true);
					break;
				case 'csv':
					// TODO: csv parsing
					break;
				default:
					// TODO: error - unsupported format
			}
			
			$iocApi = new Ioc([]);
			foreach ($iocList as $ioc) {
				packValues($ioc['value']);
				$iocApi->setParams($ioc)->addAction();
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

			$iocApi = new Ioc([]);
			$setApi = new Set([]);
			foreach ($setList as $set) {
				foreach ($set['data'] as $root) {
					packValues($root['value']);
					$id = $iocApi->setParams($root)->addAction()['id'];
					$setApi->setParams(['name' => $set['name'], 'ioc' => $id])->addAction();
					if (isset($root['children'])) {
						foreach ($root['children'] as $child) {
							$child['parent'] = $id;
							importTree($child, $iocApi);
						}
					}
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
			
			$clientApi = new Client([]);
			foreach ($repList as $report) {
				$clientApi->setParams(['report' => json_encode($report)])->uploadAction();
			}
			break;
		default:
			// TODO: error - invalid type
	}
// 	echo '</pre>';
}

// util functions

function cleanTree(&$node) {
	unset($node['id']);
	if (isset($node['children'])) 
		foreach($node['children'] as &$child) $child = cleanTree($child);
	return $node;
}

function importTree($node, $iocApi) {
	packValues($node['value']);
	$id = $iocApi->setParams($node)->addAction();
	if (isset($node['children'])) {
		foreach ($node['children'] as $child) {
			$child['parent'] = $id;
			importTree($child, $iocApi);
		}
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

?>
