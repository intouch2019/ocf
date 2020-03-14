<?php

require_once "lib/db/dbobject.php";

class clsItems extends dbobject {

	public function addCategory($storeid, $scenarioid, $ctgname) {
		$ctgname = $this->safe($ctgname);
		return $this->execInsert("insert into it_categories set storeid=$storeid, scenarioid=$scenarioid, name=$ctgname");
	}

	public function getUnassignedItems($scenarioid, $storeid, $searchtext=false) {
		$allitems = array();
		$search="";
		if ($searchtext) { $search = " and itemname like '%$searchtext%' "; }
		$objs = $this->fetchObjectArray("select id from it_rawitems where storeid=$storeid $search");
		foreach ($objs as $obj) {
			$allitems[] = $obj->id;
		}

		$assigneditems = array();
		$objs = $this->fetchObjectArray("select distinct ci.itemid from it_categories c, it_catitems ci where c.scenarioid = $scenarioid and c.id = ci.catid");
		foreach ($objs as $obj) {
			$assigneditems[] = $obj->itemid;
		}

		$unassigned = array_diff($allitems, $assigneditems);
		$idstr = implode(",", $unassigned);
		return $this->fetchObjectArray("select * from it_rawitems where id in ($idstr)");
	}

	public function getAllCategories($scenarioid) {
		return $this->fetchObjectArray("select * from it_categories where scenarioid=$scenarioid and inactive=0");
	}

	public function assignItems($storeid, $ctgid, $itemids) {
		if (count($itemids) == 0) { return; }
		$idstr = implode(",", $itemids);
		foreach ($itemids as $itemid) {
			$query = "insert into it_catitems set catid=$ctgid, itemid=$itemid";
			$this->execUpdate($query);
		}
	}


	public function getScenarios($storeid) {
		return $this->fetchObjectArray("select * from it_scenarios where storeid=$storeid");
	}

	public function addScenario($storeid, $title) {
		$title = $this->safe($title);
		return $this->execInsert("insert into it_scenarios set storeid=$storeid, title=$title");
	}
}

?>
