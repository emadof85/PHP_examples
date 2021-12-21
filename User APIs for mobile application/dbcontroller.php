<?php

class DBController {

    private $conn = "";
    private $host = "localhost";
    private $user = "super_masam";
    private $password = "frvC}S(*R{mm";
    private $database = "super_masam";

    function __construct() {
        $conn = $this->connectDB();
        if (!empty($conn)) {
            $this->conn = $conn;
        }
    }

    function connectDB() {
        $conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        return $conn;
    }

    function executeSelectQuery($query) {
        $result = mysqli_query($this->conn, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $resultset[] = $row;
            }
            if (!empty($resultset))
                return $resultset;
        }
        return NULL;
    }

    function executeSingleSelectQuery($query) {
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        if (!empty($row))
            return $row;
    }

    function executePLQuery($query) {
        $result = mysqli_query($this->conn, $query);
		return $result;
        //return mysqli_num_rows($result);
    }
	
	function executeInsertQuery($query) {
        $last_id = 0;
		if(mysqli_query($this->conn, $query)){
			$last_id = mysqli_insert_id($this->conn);
		}
		return $last_id;
    }

}

?>
