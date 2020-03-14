<?php

require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

/*
itype = 0 for smsIncoming (default)
itype = 1 for storeIncoming
*/

class clsIncoming extends dbobject {

	var $col_names = array("keyword"=>1,"phonecode"=>1,"location"=>1,"carrier"=>1,"content"=>1,"msisdn"=>1,"timestamp"=>1);
	public function insert($getParams) {
		$insert_arr = array_intersect_key($getParams, $this->col_names);
		if (count($insert_arr) == 0) {
			$logger = new clsLogger();
			$logger->logError("Invalid incoming:".print_r($getParams,true));
			return;
		}
		$query = "insert into it_incoming set ";
		$first=true;
		foreach ($insert_arr as $key=>$value) {
			if (!$first) $query .= ", "; $first=false;
			$query .= "$key = ".$this->safe($value);
		}
		$incoming_id = $this->execInsert($query);
		$obj = $this->fetchObject("select * from it_incoming where id=$incoming_id");
		$this->closeConnection();
		return $obj;
	}

	public function storeIncoming($storeid, $keyword, $phoneno, $content, $userid=false) {
		$keyword = $this->safe($keyword);
		$phoneno = $this->safe($phoneno);
		$content = $this->safe($content);
		$query = "insert into it_incoming set itype=1, storeid=$storeid, keyword=$keyword, msisdn=$phoneno, content=$content";
		if ($userid) { $query .= ", userid=$userid"; }
		return $this->execInsert($query);
	}

	public function updateUser($incomingid, $userid) {
		$this->execUpdate("update it_incoming set userid=$userid where id=$incomingid");
		$this->closeConnection();
	}
}

?>
