<?php
require_once "lib/itemparsers/clsItemParser.php";

class cls_ganeshbhelItemParser extends clsItemParser {

	var $billinfo;

	public function __construct() {
		$this->billinfo = array(
			BILL_NUMBER => null,
			BILL_DATE => null,
			BILL_TIME => null,
			BILL_DATETIME => null,
			BILL_AMOUNT => null,
			BILL_QUANTITY => null,
			BILL_DISCOUNT_VAL => null,
			BILL_DISCOUNT_PCT => null
		);
	}
	
	public function process($orderInfo) {
		$lines = explode("\n", $orderInfo);
		$items = array();
		$itemfound=false;
		$bill_amount=0;
		$bill_quantity=0;
		$bill_date=null;
		$bill_no=null;
		foreach ($lines as $line) {
			if (preg_match("/^(\S+) - (\S+)/", $line, $matches)) {
				$itemfound=true;
				$lineQuantity = $this->parseFloat($matches[1]);
				$lineItemName = trim($matches[2]);
			} else
			if ($itemfound) {
				$itemfound=false;
				preg_match("/Rs. : (\d+).*/",$line,$matches);
				$unitPrice=$this->parseFloat($matches[1]);
				$lineTotal = $lineQuantity * $unitPrice;
				$bill_amount += $lineTotal;
				$bill_quantity += $lineQuantity;
				$items[] = (object) array(
				    "itemName" => $lineItemName,
				    "unitPrice" => $unitPrice,
				    "lineQuantity" => $lineQuantity,
				    "font" => null,
				    "lineTotal" => $lineTotal
				);
			}
			if (!$bill_date && preg_match("/ (\d\d)\/(\d\d)\/(\d\d\d\d)\|(\S+)/", $line, $matches)) {
				$this->billinfo[BILL_DATE] = "$matches[3]-$matches[2]-$matches[1]";
				$this->billinfo[BILL_NUMBER] = $matches[4];
			}	
		}
		$this->billinfo[BILL_AMOUNT] = $bill_amount;
		$this->billinfo[BILL_QUANTITY] = $bill_quantity;
		if (count($items) == 0) { return $this->error(4, "No items found"); }
		return $this->success2(array(
			"items" => $items,
			"billinfo" => $this->billinfo
		));
	}
}

?>
