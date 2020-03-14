<?php
require_once "lib/itemparsers/clsItemParser.php";

class cls_jlpuneItemParser extends clsItemParser {

	public function __construct() {
	}
	
	public function process($orderInfo) {
		$lines = explode("\n", $orderInfo);
		$totalLines = count($lines);
		$i=0;
		while($i<$totalLines && !preg_match("/-------/", $lines[$i])) {
			$i++;
		}
		if ($i == $totalLines) { return $this->error(1, "invalid receipt format"); }
		$i++;
		$items = array();
		$discount=null;
		for (; $i<$totalLines; $i++) {
			$lines[$i]=trim($lines[$i]);
			if (preg_match("/---------/", $lines[$i])) { break; }
			if ($this->isDiscount($lines[$i], $discount)) { continue; }
			$itemInfo = $this->parseItemLine($lines[$i]);
			if (!$itemInfo) { continue; }
			if ($itemInfo->lineTotal < 0) { continue; }
			$items[] = $itemInfo;
		}
		if ($i == $totalLines) { return $this->error(4, "invalid receipt format"); }
		return $this->success($items, $discount);
	}

	private function parseItemLine($line) {
		$line = trim($line);
		$line = strrev($line);
		if (!preg_match("/(\S+)\s+(\S+)\s+(\S+) (.*)/", $line, $matches)) { return false; }
		$lineTotal = $this->parseFloat(substr(strrev($matches[1]),3));
		if ($lineTotal === false) { return false; }
		$lineQuantity = $this->parseInt(substr(strrev($matches[2]),1));
		if ($lineQuantity === false) { return false; }
		$unitPrice = $this->parseFloat(substr(strrev($matches[3]),3));
		if ($unitPrice === false) { return false; }
		$lineItemName = trim(strrev($matches[4]));
		return (object) array(
		    "itemName" => $lineItemName,
		    "unitPrice" => $unitPrice,
		    "lineQuantity" => $lineQuantity,
		    "lineTotal" => $lineTotal,
		    "font" => null
		);
	}

	private function isDiscount($line, &$discount) {
		return false;
	}
}

?>
