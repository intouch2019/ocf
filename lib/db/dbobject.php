<?php

abstract class dbobject {
	var $conn=false;
	var $commit=true;

	public function __construct($commit=true) {
		$this->commit=$commit;
	}

	function getConnection() {
		if (!$this->conn) {
			$this->conn =  new mysqli(DB_SERVER,DB_USR,DB_PWD,DB_NME);
		}
		return $this->conn;
	}

	function closeConnection() {
		if ($this->conn) {
			$this->conn->close();
		}
		$this->conn=false;
	}

	function execInsert($sql) {
		if ($this->commit) {
			$this->getConnection()->query($sql);
			return $this->getConnection()->insert_id;
		} else { return -1; }
	}

	function execUpdate($sql) {
		if ($this->commit) {
			$this->getConnection()->query($sql);
			return $this->conn->affected_rows;
		} else { return 0; }
	}

	function execQuery($sql) {
		return $this->getConnection()->query($sql);
	}

	function fetchObject($query) {
		try {
		$obj=null;
		$result = $this->getConnection()->query($query);
		if ($result) {
			$obj = $result->fetch_object();
			$result->close();
		}
		return $obj;
		} catch(Exception $xcp) {
		}
	}
        
          function fetchAllObjects($query) {
        return $this->fetchObjectArray($query);
    }

	function fetchObjectArray($query) {
		try {
		$result = $this->getConnection()->query($query);
		$arr = false;
		if ($result) {
		$arr = array();
		while ($obj = $result->fetch_object())
			$arr[] = $obj;
		$result->close();
		}
		return $arr;
		} catch(Exception $xcp) {
		}
	}

	function safe($str) {
	    // Stripslashes
	    if (get_magic_quotes_gpc()) {
	        $str = stripslashes($str);
	    }
	    // always quote even for numbers
	    return "'" . $this->getConnection()->real_escape_string($str) . "'";

	}

	function __destruct() {
		$this->closeConnection();
	}
}

?>
