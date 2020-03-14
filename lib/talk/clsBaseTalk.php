<?php

require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

class clsBaseTalk extends dbobject {

	var $storecode;
	function __construct($storecode) {
		$this->storecode=$storecode;
	}

	// sales summary
	function cmd_1($params=null) {
		$storecode=$this->safe($this->storecode);
		if ($params) {
			$params=trim($params);
			$usage = "Incorrect parameter. valid values are 2011 or 201103 or 20110319";
			if ($params == "") {
				return $usage;
			}
			if (!ctype_digit($params)) {
				return $usage;
			}	
			if (strlen($params) != 4 && strlen($params) != 6 && strlen($params) != 8) {
				return $usage;
			}
			$year=substr($params,0,4);
			$bdate=$year;
			$year=intval($year);
			$datequery = "year(o.bill_datetime)=$year";
			if (strlen($params) > 4) {
				$month=substr($params,4,2);
				$bdate.="-$month";
				$month=intval($month);
				$datequery .= " and month(o.bill_datetime)=$month";
			}
			if (strlen($params) > 6) {
				$day=substr($params,6,2);
				$bdate.="-$day";
				$day=intval($day);
				$datequery .= " and day(o.bill_datetime)=$day";
			}
		} else {
			// set to today
			$bdate=$this->safe(date("Y-m-d"));
			$datequery = "date(o.bill_datetime)=$bdate";
		}
		$query = "select count(*) as numorders, sum(bill_amount) as totalamt, sum(bill_quantity) totalqty from it_orders o, it_codes c where c.code=$storecode and c.id=o.storeid and o.status>0 and $datequery";
		$obj = $this->fetchObject($query);
		if (!$obj) { return "No sales yet for $bdate"; }
		return "Sales for $bdate: $obj->numorders Orders, Rs. ".intval($obj->totalamt).", Qty: $obj->totalqty";
	}

	// last sale
	function cmd_2($params=null) {
		$storecode=$this->safe($this->storecode);
		$day_start=$this->safe(date("Y-m-d 00:00:00"));
		$query = "select o.orderinfo from it_orders o, it_codes c where c.code=$storecode and c.id=o.storeid and o.status>0 order by o.id desc limit 1";
		$obj = $this->fetchObject($query);
		if (!$obj) { return "No sales yet for the day"; }
		return "Last Sale\n$obj->orderinfo";
	}
}

?>
