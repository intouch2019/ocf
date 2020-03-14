<?php

require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

class clsDistributor extends dbobject {

	public function getDistributorById($distid) {
		$user = $this->fetchObject("select * from it_distributors where id=$distid");
		$this->closeConnection();
		return $user;
	}

}

?>
