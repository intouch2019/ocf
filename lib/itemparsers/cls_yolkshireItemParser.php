<?php
require_once "lib/itemparsers/clsItemParser.php";

class cls_yolkshireItemParser extends clsItemParser {

	public function __construct() {
	}
	
	public function process($orderInfo) {
		$lines = explode("\n", $orderInfo);
		$totalLines = count($lines);
		$i=0;
		while($i<$totalLines && !preg_match("/.*MENU\s+DESCRIPTION.*/", $lines[$i])) {
			$i++;
		}
		if ($i == $totalLines) { return $this->error(1, "invalid receipt format"); }
		$i++;
		$line = trim($lines[$i]);
		if (!preg_match("/^----/",$line)) { return $this->error(2, "invalid receipt format"); }
		$i++;
		$items = array();
		$discount=false;
		$itemsDone=false;
		while ($i<$totalLines) {
			if (preg_match("/------/", $lines[$i])) { $itemsDone=true; }
			else
			if (!$discount && $this->isDiscount($lines[$i], $discount)) { ; }
			else
			if (!$itemsDone) {
				$itemInfo = $this->parseItemLine($lines[$i]);
				if ($itemInfo) { $items[] = $itemInfo; }
			}
			$i++;
		}
		if (count($items) == 0) { return $this->error(4, "No items found"); }
		return $this->success($items, $discount);
	}

	private function parseItemLine($line) {
		$line = trim($line);
		$line = strrev($line);
		if (!preg_match("/(\S+)\s+(\S+)\s+(\S+) (.*)/", $line, $matches)) { return false; }
		$lineTotal = $this->parseFloat(strrev($matches[1]));
		if ($lineTotal === false) { return false; }
		$unitPrice = $this->parseFloat(strrev($matches[2]));
		if ($unitPrice === false) { return false; }
		$lineQuantity = $this->parseFloat(strrev($matches[3]));
		if ($lineQuantity === false) { return false; }
		$lineItemName = trim(strrev($matches[4]));
		return (object) array(
		    "itemName" => $lineItemName,
		    "unitPrice" => $unitPrice,
		    "lineQuantity" => $lineQuantity,
		    "font" => null,
		    "lineTotal" => $lineTotal
		);
	}

	private function ignoreLine($line) {
		if (preg_match("/Account Payment/", $line)) return true;
		if (preg_match("/Party Bill/i", $line)) return true;
		return false;
	}

	private function isDiscount($line, &$discount) {
		if (!preg_match("/DISCOUNT\(Rs\.\) :\s+(\S+)/", $line, $matches)) {
			return false;
		}
		$value = $this->parseFloat($matches[1]);
		$discount = (object) array(
			"percent" => null,
			"value" => $value
		);
		return true;
	}
}

?>
