<?php

require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

class clsTalk extends dbobject {

	public function getStores($username) {
		$username = $this->safe(trim($username));
		$query = "select c.code from it_talkusers t, it_codes c where t.username=$username and t.storeid=c.id";
		$objs = $this->fetchObjectArray($query);
		$storecodes = array();
		foreach ($objs as $obj) {
			$storecodes[] = $obj->code;
		}
		return $storecodes;
	}

	public function isValidUser($username, $storecode) {
		$username=$this->safe($username);
		$storecode=$this->safe($storecode);
		$query = "select 1 from it_talkusers t, it_codes c where t.username=$username and t.storeid=c.id and c.code=$storecode";
		return $this->fetchObject($query);
	}
}

?>
