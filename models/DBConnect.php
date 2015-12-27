<?php
/*
Set of functions to interact with the database
*/
include_once 'dbInfo.php';

class DBConnect {

    private $db;

    public function __construct() {
        // open new MYSQLi connection
        $this->db = new mysqli(HOST, USER, PASS, DATABASE);
        
        if ($this->db->connect_errno > 0) {
            throw new Exception('Unable to connect to database [' . $this->db->connect_error . ']');
        }
    }

    public function close() {
        // close the connection
        $this->db->close();
    }
    
// ========== IOC ==========
    
    public function iocFetchList() {
        // fetch all indicator entries from the `indicators` table
        $sql = 'SELECT * '.
               'FROM `indicators`;';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function iocFetchId($id) {
        // fetch one indicator from the `indicators` table
        $sql = 'SELECT * '.
               'FROM `indicators` '.
               'WHERE `id` = ?;';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
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
    
    public function iocAdd($name, $type, $value, $value2) {
        // add new indicator to the `indicators` table
        // table structure: id name type value value2
        // returns the newly generated id
        if ($value2 == '') $value2 = NULL;
        
        $sql = 'INSERT INTO `indicators` '.
               '(`name`, `type`, `value`, `value2`) '.
               'VALUES (?, ?, ?, ?);';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
        if (!$stmt->bind_param('ssss', $name, $type, $value, $value2)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        $sql = 'SELECT LAST_INSERT_ID();';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');

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
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
        if (!$stmt->bind_param('ssssii', $name, $type, $value, $value2, $hidden, $id)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        $res = ['rows' => $stmt->affected_rows];

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $res;
    }
 
// ========== REPORT ==========

    public function repFetchList() {
        // fetch all reports from the database
        $sql = 'SELECT * '.
               'FROM `reports`;';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
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
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
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
    
    public function repAdd() {
        /*  TODO: No report format yet
        if ($value2 == '') $value2 = NULL;
        
        $sql = 'INSERT INTO `indicators` '.
               '(`name`, `type`, `value`, `value2`) '.
               'VALUES (?, ?, ?, ?);';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
        if (!$stmt->bind_param('ssss', $name, $type, $value, $value2)) 
            throw new Exception('Error binding parameters [' . $stmt->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        $sql = 'SELECT LAST_INSERT_ID();';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');

        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');
            
        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return ['id' => $result->fetch_all(MYSQLI_ASSOC)[0]['LAST_INSERT_ID()']];
        */
    }
    
 
}
?>