<?php
/*
Privately accessible API base
*/
if (!defined('ROOT')) define('ROOT', '..');
include_once ROOT.'/models/DBConnect.php';

abstract class AbstractController {

    protected $params;
    protected $db;

    public function __construct($params) {
        $this->params = $params;
        $this->db = new DBConnect();
    }

    public function __destruct() {
        $this->db->close();
    }
    
    public function setParams($params) {
    	$this->params = $params;
    	return $this;
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
    
    protected function packArray($array) {
    	$string = implode('|', preg_replace('/([`\|])/', '`$1', $array));
    	if ($string != '') $string .= '|';
    	return $string;
    }
    
    protected function unpackArray($string) {
    	$array = preg_replace('/`(.)/', '$1', preg_split("/(?<!`)\|/", $string));
    	array_pop($array);
    	return $array;
    }
    
}
?>