<?php
/*
Set of functions to interact with the database
*/
if (!defined('ROOT')) define('ROOT', '..');
include_once ROOT.'/models/dbInfo.php';

class DBConnect {

    private $mysqli;

    public function __construct() {
        // open new MYSQLi connection
        @$this->mysqli = new mysqli(HOST, USER, PASS, DATABASE);
        
        if ($this->mysqli->connect_errno > 0) {
            throw new Exception('Unable to connect to database [' . $this->mysqli->connect_error . ']');
        }
    }

    public function close() {
        // close the connection
        $this->mysqli->close();
    }
    
    private function lastInsertId() {
        $sql = 'SELECT LAST_INSERT_ID();';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');

        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');
            
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_all(MYSQLI_ASSOC)[0]['LAST_INSERT_ID()'];
    }

// ========== UTIL ==========

    public function getIndicatorTypes() {
        $sql = 'SELECT `type`, `values_count`, `values_desc` '.
               'FROM `types`;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
// ========== IOC ==========
    
    public function iocFetchList() {
        // fetch all indicator entries from the `indicators` table
        $sql = 'SELECT `id`, `name`, `type`, `value` '.
               'FROM `indicators` '.
               'WHERE `hidden` = 0;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        $ret = [];
        while ($row = $result->fetch_assoc()) {
            $ret[$row['id']] = $row;
            //unset($ret[$row['id']]['id']);
        }
            
        //return $result->fetch_all(MYSQLI_ASSOC);
        return $ret;
    }

    public function iocFetchHidden() {
        // fetch all indicator entries from the `indicators` table
        $sql = 'SELECT `id`, `name`, `type`, `value` '.
               'FROM `indicators` '.
               'WHERE `hidden` = 1;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        $ret = [];
        while ($row = $result->fetch_assoc()) {
            $ret[$row['id']] = $row;
            //unset($ret[$row['id']]['id']);
        }
            
        //return $result->fetch_all(MYSQLI_ASSOC);
        return $ret;
    }
    
    public function iocFetchId($id) {
        // fetch one indicator from the `indicators` table
        $sql = 'SELECT `id`, `name`, `type`, `value` '.
               'FROM `indicators` '.
               'WHERE `id` = ?;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('i', $id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_assoc();
    }
    
    public function iocAdd($name, $type, $value) {
        // add new indicator to the `indicators` table
        // table structure: id name type value value2
        // returns the newly generated id
        if ($value == '') $value = NULL;
        
        $sql = 'INSERT INTO `indicators` '.
               '(`name`, `type`, `value`) '.
               'VALUES (?, ?, ?);';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('sss', $name, $type, $value)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $this->lastInsertId();
    }
    
    public function iocUpdate($id, $name, $type, $value) {
        // edit an existing indicator in the `indicators` table
        // table structure: id name type value value2
        if ($value == '') $value = NULL;
        
        $sql = 'UPDATE `indicators` '.
               'SET `name` = ?, `type` = ?, `value` = ? '.
               'WHERE `id` = ?;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('sssi', $name, $type, $value, $id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        $res = $stmt->affected_rows;

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $res;
    }
    
    public function iocSetHidden($id, $hidden) {
        // set hidden status
        
        $sql = 'UPDATE `indicators` '.
               'SET `hidden` = ? '.
               'WHERE `id` = ?;';
               
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('ii', $hidden, $id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        $res = $stmt->affected_rows;

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $res;
    }
    
// ========== IOC SET ==========

    public function setListNames() {
        $sql = 'SELECT `name` '.
               'FROM `sets` '.
               'WHERE `hidden` = 0 '.
               'GROUP BY `name`;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

#        $ret = [];
#        while ($row = $result->fetch_assoc()) {
#            $ret[$row['id']] = $row;
#        }
            
#        return $ret;
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function setFetchName($name) {
        // fetch an indicator set from the `sets` table
        $sql = 'SELECT `id`, `parent_id`, `type`, `ioc_id` '.
               'FROM `sets` '.
               'WHERE `name` = ? AND `hidden` = 0;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('s', $name)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        $ret = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
        	$ret[$row['id']] = $row;
        }
        return $ret;
    }

    public function setAdd($name, $parent_id, $type, $ioc_id) {
        $sql = 'INSERT INTO `sets` '.
               '(`name`, `parent_id`, `type`, `ioc_id`) '.
               'VALUES (?, ?, ?, ?);';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('sisi', $name, $parent_id, $type, $ioc_id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');
        
        return $this->lastInsertId();
    }
    
    public function setHide($id, $hidden) {
        $sql = 'UPDATE `sets` '.
               'SET `hidden` = ? '.
               'WHERE `id` = ?;';

        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('ii', $hidden, $id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        $res = $stmt->affected_rows;

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $res;
    }
    
    public function setGetChildren($parentId) {
    	$sql = 'SELECT `id` '.
    		   'FROM `sets` '.
    		   'WHERE `parent_id` = ?;';
    	
    	if (!$stmt = $this->mysqli->prepare($sql))
    		throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
    	
    	if (!$stmt->bind_param('i', $parentId))
    		throw new Exception('Error binding parameters [' . $stmt->error . ']');
    	
    	if (!$stmt->execute())
    		throw new Exception('Error executing statement [' . $stmt->error . ']');
    	
    	if (!$result = $stmt->get_result())
    		throw new Exception('Error getting result [' . $stmt->error . ']');
    		
    	if (!$stmt->close())
    		throw new Exception('Error closing statement [' . $stmt->error . ']');
    	
    	return array_map(function ($e) {
    		return $e['id'];
    	}, $result->fetch_all(MYSQLI_ASSOC));
    }
    
    public function setHideChildren($parentId, $hidden) {
    	$sql = 'UPDATE `sets` '.
			   'SET `hidden` = ? '.
    		   'WHERE `parent_id` = ?;';
    	
    	if (!$stmt = $this->mysqli->prepare($sql))
    		throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
    	
    	if (!$stmt->bind_param('ii', $hidden, $parentId))
    		throw new Exception('Error binding parameters [' . $stmt->error . ']');
    	
		if (!$stmt->execute())
			throw new Exception('Error executing statement [' . $stmt->error . ']');
    	
		$res = $stmt->affected_rows;
    	
		if (!$stmt->close())
			throw new Exception('Error closing statement [' . $stmt->error . ']');
    	
		return $res;
    }
    
// ========== REPORT ==========

    public function repFetchList() {
        // fetch all reports from the database
        $sql = 'SELECT `id`, `org`, `device`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `setname`, `ioc_id`, `result` '.
               'FROM `reports`;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function repFetchId($id) {
        // fetch one report
        $sql = 'SELECT `id`, `org`, `device`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `setname`, `ioc_id`, `result` '.
               'FROM `reports` '.
               'WHERE `id` = ?;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('i', $id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function repFetchTimeRange($from, $to) {
        $sql = 'SELECT `id`, `org`, `device`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp`, `setname`, `ioc_id`, `result` '.
               'FROM `reports` '.
               'WHERE `timestamp` >= FROM_UNIXTIME(?) AND `timestamp` <= FROM_UNIXTIME(?);';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('ii', $from, $to)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function repAdd($org, $device, $timestamp, $setname, $ioc_id, $result) {
        $sql = 'INSERT INTO `reports` '.
               '(`org`, `device`, `timestamp`, `setname`, `ioc_id`, `result`) '.
               'VALUES (?, ?, FROM_UNIXTIME(?), ?, ?, ?);';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('ssssii', $org, $device, $timestamp, $setname, $ioc_id, $result)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        $sql = 'SELECT LAST_INSERT_ID();';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');

        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');
            
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return ['id' => $result->fetch_all(MYSQLI_ASSOC)[0]['LAST_INSERT_ID()']];
    }
 
    public function repAddMulti($report) {
        // expects valid report structure as parsed from json
        $sql = 'INSERT INTO `reports` '.
               '(`org`, `device`, `timestamp`, `setname`, `ioc_id`, `result`) '.
               'VALUES ';
               
        $params = [];
        $types = '';
        foreach($report['indicators'] as $indicator) { // create sql query, parameters array and types string
            $sql .= '(?, ?, FROM_UNIXTIME(?), ?, ?, ?), ';
            
            $params[] = $report['org'];
            $params[] = $report['device'];
            $params[] = $report['timestamp'];
            $params[] = $report['setname'];
            $params[] = $indicator['id'];
            $params[] = $indicator['result'];
            
            $types .= 'ssssii';
        }
        $sql = rtrim($sql, ', ');
        $sql .= ';';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param($types, ...$params)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        $rows = $stmt->affected_rows;
        
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $rows;
    
    }
 
}
?>