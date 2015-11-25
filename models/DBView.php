<?php
/*
Set of functions to interact with the database
*/
include_once 'dbInfo.php';

class DBView {

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
    
    public function iocFetchList() {
        // fetch all entries from the `indicators` table
        $sql = 'SELECT *'.
               'FROM `indicators`';
        
        if (!$stmt = $this->db->prepare($sql))
            throw new Exception('Error preparing statement [' . $this->db->error . ']');
        
        if (!$stmt->execute())
            throw new Exception('Error executing statement [' . $stmt->error . ']');
        
        if (!$result = $stmt->get_result())
            throw new Exception('Error getting result [' . $stmt->error . ']');

        if (!$stmt->close())
            throw new Exception('Error closing statement [' . $stmt->error . ']');

        return $result->fetch_all(MYSQLI_ASSOC);
        #return $this->runQuery($sql);
    }

    public function iocFetchId($id) {
        // fetch one entry from the `indicators` table
        $sql = 'SELECT *'.
               'FROM `indicators`'.
               'WHERE `id` = ?';
        
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

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
}
?>