<?php

abstract class clsItemParser {

	protected function __construct() {
	}
	
	protected abstract function process($orderInfo);

	protected function error($code, $msg) {
		return (object) array(
			"errorcode" => $code,
			"errormsg" => $msg
		);
	}

	protected function success($items, $discount=null) {
		$values = array(
			"errorcode" => 0,
			"items" => $items
		);
		if ($discount) {
			$values["discount"] = $discount;
		};
		return (object) $values;
	}

	protected function success2($retvals) {
		$retvals["errorcode"] = 0;
		return (object) $retvals;
	}

	protected function parseInt($num) {
		$num = str_replace(",","",$num);
		if (!is_numeric($num)) { return false; }
		return intval($num);
	}

	protected function parseFloat($num) {
		$num = str_replace(",","",$num);
		if (!is_numeric($num)) { return false; }
		return floatval($num);
	}
}

?>
