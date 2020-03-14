<?php
require_once "lib/itemparsers/clsItemParser.php";

class cls_hanes_knItemParser extends clsItemParser {

	public function __construct() {
	}
	
	public function process($orderInfo) {
		$discount=null;
		$lines = explode("\n", $orderInfo);
		$totalLines = count($lines);
		for ($i=1; $i<$totalLines; $i++) {
			$line = trim($lines[$i]);
			if ($line == ""){ continue; }
			if (preg_match("/NET AMT/", $lines[$i])) { break; }
			if (preg_match("/DOSPrinter/", $lines[$i])) { break; }
			$itemInfo = $this->parseItemLine($line);
			if ($itemInfo) { $items[] = $itemInfo; }
		}
		if (count($items) == 0) { return $this->error(4, "No items found"); }
		return $this->success($items, $discount);
	}

	private function parseItemLine($line) {
		$line = trim($line);
		if (!preg_match("/(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)/", $line, $matches)) { return false; }
		$lineItemName = $matches[2];
		$lineQuantity = $this->parseFloat($matches[3]);
		if ($lineQuantity === false) { return false; }
		$unitPrice = $this->parseFloat($matches[4]);
		if ($unitPrice === false) { return false; }
		$lineTotal = $this->parseFloat($matches[5]);
		if ($lineTotal === false) { return false; }
		return (object) array(
		    "itemName" => $lineItemName,
		    "unitPrice" => $unitPrice,
		    "lineQuantity" => $lineQuantity,
		    "font" => null,
		    "lineTotal" => $lineTotal
		);
	}

}

?>
