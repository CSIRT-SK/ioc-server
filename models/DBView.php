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
            exit('Unable to connect to database [' . $this->db->connect_error . ']');
        }
    }

    public function close() {
        // close the connection
        $this->db->close();
    }
    
    private function runQuery($query) {
        // run a given query
        $result = $this->db->query($query);
        if (!$result) {
            exit('There was an error running the query [' . $this->db->error . ']');
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function fetchIOCList() {
        // fetch all entries from the `indicators` table
        $sql = 'SELECT *'.
            'FROM `indicators`';
        
        return $this->runQuery($sql);
    }

}
?>