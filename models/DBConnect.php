<?php
/*
Set of functions to interact with the database
*/
include_once 'dbInfo.php';

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
    
// ========== IOC ==========
    
    public function iocFetchList() {
        // fetch all indicator entries from the `indicators` table
        $sql = 'SELECT `id`, `name`, `type`, `value`, `value2`, `parent` '.
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

    public function iocFetchId($id) {
        // fetch one indicator from the `indicators` table
        $sql = 'SELECT * '.
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

        return $result->fetch_all(MYSQLI_ASSOC)[0];
    }
    
    public function iocAdd($name, $type, $value, $value2, $parent) {
        // add new indicator to the `indicators` table
        // table structure: id name type value value2
        // returns the newly generated id
        if ($value == '') $value = NULL;
        if ($value2 == '') $value2 = NULL;
        
        $sql = 'INSERT INTO `indicators` '.
               '(`name`, `type`, `value`, `value2`, `parent`) '.
               'VALUES (?, ?, ?, ?, ?);';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('ssssi', $name, $type, $value, $value2, $parent)) 
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
    
    public function iocEdit($id, $name, $type, $value, $value2, $hidden) {
        // edit an existing indicator in the `indicators` table
        // table structure: id name type value value2
        if ($value2 == '') $value2 = NULL;
        
        $sql = 'UPDATE `indicators` '.
               'SET `name` = ?, `type` = ?, `value` = ?, `value2` = ?, `hidden` = ? '.
               'WHERE `id` = ?;';
        
        if (!$stmt = $this->mysqli->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->mysqli->error . ']');
        
        if (!$stmt->bind_param('ssssii', $name, $type, $value, $value2, $hidden, $id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        $res = ['rows' => $stmt->affected_rows];

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $res;
    }
// ========== IOC SET ==========

    public function setFetchId($id) {
        // fetch an indicator set from the `sets` table
        $sql = 'SELECT * '.
               'FROM `sets` '.
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

        return $result->fetch_all(MYSQLI_ASSOC)[0];
    }

    public function setFetchName($name) {
        // fetch an indicator set from the `sets` table
        $sql = 'SELECT * '.
               'FROM `sets` '.
               'WHERE `name` = ?;';
        
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

        return $result->fetch_all(MYSQLI_ASSOC)[0];
    }


// ========== REPORT ==========

    public function repFetchList() {
        // fetch all reports from the database
        $sql = 'SELECT * '.
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
        $sql = 'SELECT * '.
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

        return $result->fetch_all(MYSQLI_ASSOC)[0];
    }
    
    public function repAdd($org, $device, $timestamp, $setname, $ioc_id, $result) {
        $sql = 'INSERT INTO `reports` '.
               '(`org`, `device`, `timestamp`, `setname`, `ioc_id`, `result`) '.
               'VALUES (?, ?, ?, ?, ?, ?);';
        
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
            $sql .= '(?, ?, ?, ?, ?, ?), ';
            
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

        return ['added' => $rows];
    
    }
 
}
?>