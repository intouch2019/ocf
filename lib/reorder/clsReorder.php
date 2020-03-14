<?php
require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

class clsReorder extends dbobject {
	public function addReorder($supid) {
		$query="insert into it_reorder set supplierid=$supid, createtime=now()";
		return $this->execInsert($query);
	}
	
	public function addReorderItems($arr) {
		$str="";
		foreach($arr as $key => $value) {
			$str.="$key=".$value.",";	
		}
		$query="insert into it_reorder_items set $str createtime=now()";
		return $this->execInsert($query);
	}
	
	public function getAllReorder() {
		$query="select r.*,s.suppliername from it_reorder r,it_supplier s where r.supplierid=s.id order by createtime desc";
		return $this->fetchObjectArray($query);
	}
	
	public function getReorderInfoById($id) {
		$query="select r.*,s.suppliername from it_reorder r,it_supplier s where r.supplierid=s.id and r.id=$id";
		return $this->fetchObject($query);
	}

	public function getItemsByReorderId($id) {
		$query="select ri.*,r.itemname from it_reorder_items ri, it_rawitems r where ri.reorderid=$id and ri.rawitemid=r.id";
		return $this->fetchObjectArray($query);
	}	
}
?>
