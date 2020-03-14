<?php
require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

class clsSupplier extends dbobject {
	public function addSupplier($arr) {
		$str="";
		foreach($arr as $key => $value) {
			$value=$this->safe($value);
			$str .="$key=$value,";
		}
		$query="insert into it_suppliers set $str createtime=now()";
		return $this->execInsert($query);
	}
	
	public function getAllActiveSuppliers() {
		$query="select * from it_suppliers where is_active = 1 ";
		return $this->fetchObjectArray($query);
	}
	
	public function getSupplierById($id) {
		$query="select * from it_suppliers where id=$id";
		return $this->fetchObject($query);
	}
}

?>
