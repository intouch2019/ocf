<?php

require_once "lib/db/dbobject.php";

class clsCatalog extends dbobject {

	public function loadProducts($codeid, $csvFile) {
		$currVersion = $this->getCurrentVersion($codeid);
		$newVersion = $currVersion + 1;

		if (($handle = fopen($csvFile, "r")) === FALSE) {
			throw new Exception("Unable to open file:$csvFile");
		}
		$count=0;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$product_name = $data[0];
			$this->execInsert("insert into it_products set storeid=$codeid, version=$newVersion, product_name='$product_name', isactive=1");
			$count++;
	    }
	    fclose($handle);
		// inactivate the current version products
		$this->execUpdate("update it_products set isactive=0 where storeid=$codeid and version=$currVersion");
		return $count;
	}

	public function getCurrentVersion($codeid) {
		$query = "select version from it_products where storeid=$codeid and isactive=1";
		$obj = $this->fetchObject($query);
		if ($obj) { return $obj->version; }
		else { return 0; }
	}

	public function lookupProducts($codeid, $searchStr) {
		$searchStr = $this->safe("%".$searchStr."%");
		$query = "select * from it_products where storeid=$codeid and isactive=1 and product_name like $searchStr";
		return $this->fetchObjectArray($query);
	}
}

?>
