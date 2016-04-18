<pre>
<?php
if (!defined('ROOT')) define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/ioc-server');
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
			importData($_POST['type'], $_POST['iformat'], $_FILES['tmp_name']);
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
// 	header('Content-Disposition: attachment; filename="' . $type . '-' . time() . $format . '"');
// 	header('Content-Type: application/octet-stream');
// 	header('Connection: close');
	
	switch ($type) {
		case 'ioc':
			$iocApi = new Ioc([]);
			$iocList = array_map(function($e) {
				unset($e['id'], $e['parent']);
				return $e;
			}, array_values($iocApi->listAvailableAction()));
			
			switch ($format) {
				case 'json':
					echo json_encode($iocList, JSON_PRETTY_PRINT);
					break;
				case 'csv':
					$output = fopen('php://output', 'w');
					fputcsv($output, ['name', 'type', 'value']);
					foreach ($iocList as $row) fputcsv($output, $row);
					fclose($output);
					break;
				default:
					// TODO: error - unsupported type
			}
			break;
		
		case 'set':
			$setApi = new Set([]);
			$setNames = array_map(function($e) {
				return $e['name'];
			}, $setApi->listNamesAction());
			$clientApi = new Client([]);
			foreach ($setNames as $name) {
				$clientApi->setParams(['name' => $name]);
				foreach ($clientApi->requestAction() as $root) {
					$set[] = cleanTree($root);
				}
				$setList[] = ['name' => $name, 'data' => $set];
			}
			
			switch ($format) {
				case 'json':
					echo json_encode($setList, JSON_PRETTY_PRINT);
					break;
				default:
					// TODO: error - unsupported type
			}
			break;
		
		case 'rep':
			$repApi = new Report([]);
			$repList = $repApi->listAllAction();
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
			
			switch ($format) {
				case 'json':
					echo json_encode($groupedList, JSON_PRETTY_PRINT);
					break;
				case 'csv':
					$output = fopen('php://output', 'w');
					fputcsv($output, ['org', 'device', 'timestamp', 'setname', 'id', 'result']);
					foreach ($repList as $row) fputcsv($output, $row);
					fclose($output);
					break;
				default:
					// TODO: error - unsupported type
			}
			break;
		default:
			// TODO: error - invalid type
	}
}

function importData($type, $format, $filename) {

}

// util functions

function cleanTree(&$node) {
	unset($node['id']);
	if ($node['value'] == null) unset($node['value']);
	if (isset($node['children'])) 
		foreach($node['children'] as &$child) $child = cleanTree($child);
	return $node;
}

?>
</pre>