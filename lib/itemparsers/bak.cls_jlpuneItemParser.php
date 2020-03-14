<?php
require_once "lib/itemparsers/clsItemParser.php";

class cls_jlpuneItemParser extends clsItemParser {

	public function __construct() {
	}
	
	public function process($orderInfo) {
		$lines = explode("\n", $orderInfo);
		$totalLines = count($lines);
		$i=0;
		while($i<$totalLines && !preg_match("/.*Details\s+Price\s+Sold\s+Rs.*/", $lines[$i])) {
			$i++;
		}
		if ($i == $totalLines) { return $this->error(1, "invalid receipt format"); }
		$i++;
		$line = trim($lines[$i]);
		if (!preg_match("/^----/",$line)) { return $this->error(2, "invalid receipt format"); }
		$items = array();
		$discount=null;
		while ($i++<$totalLines) {
			if (preg_match("/---------/", $lines[$i])) { break; }
			if ($this->isDiscount($lines[$i], $discount)) { continue; }
			$itemInfo = $this->parseItemLine($lines[$i]);
			if (!$itemInfo) { continue; }
			if ($itemInfo->lineTotal < 0) { continue; } // ignore negative itemlines - these are cancelled items
			$items[] = $itemInfo;
		}
		if ($i == $totalLines) { return $this->error(4, "invalid receipt format"); }
		return $this->success($items, $discount);
	}

	private function parseItemLine($line) {
		$line = trim($line);
		$line = strrev($line);
//		if (!preg_match("/(\d+).(\d+,*\d+\-*)\s+(\d+\-*)\s+(\d+).(\d+)(.*)/", $line, $matches)) { return false; }
//		$lineTotal = strrev($matches[2]).".".strrev($matches[1]);
//		$lineTotal = floatval(str_replace(",","",$lineTotal));
//		$lineQuantity = strrev($matches[3]);
//		$lineQuantity = floatval(str_replace(",","",$lineQuantity));
//		$unitPrice = strrev($matches[5]).".".strrev($matches[4]);
//		$unitPrice = floatval(str_replace(",","",$unitPrice));
//		$lineItemName = trim(strrev($matches[6]));
		if (!preg_match("/(\S+)\s+(\S+)\s+(\S+) (.*)/", $line, $matches)) { return false; }
		$lineTotal = $this->parseFloat(strrev($matches[1]));
		if ($lineTotal === false) { return false; }
		$lineQuantity = $this->parseFloat(strrev($matches[2]));
		if ($lineQuantity === false) { return false; }
		$unitPrice = $this->parseFloat(strrev($matches[3]));
		if ($unitPrice === false) { return false; }
		$lineItemName = trim(strrev($matches[4]));
		return (object) array(
		    "itemName" => $lineItemName,
		    "unitPrice" => $unitPrice,
		    "lineQuantity" => $lineQuantity,
		    "lineTotal" => $lineTotal
		);
	}

	private function ignoreLine($line) {
		if (preg_match("/Account Payment/", $line)) return true;
		if (preg_match("/Party Bill/i", $line)) return true;
		return false;
	}

	private function isDiscount($line, &$discount) {
		if (!preg_match("/(\S+)% off (.*)/", $line, $matches)) {
			return false;
		}
		$percent = $this->parseFloat($matches[1]);
		$value = $this->parseFloat($matches[2]);
		$discount = (object) array(
			"percent" => $percent,
			"value" => $value
		);
		return true;
	}
}

?>
