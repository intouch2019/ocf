<?php
require_once "lib/itemparsers/clsItemParser.php";

class cls_popularItemParser extends clsItemParser {

	public function __construct() { }
	
	public function process($orderInfo) {
		$lines = explode("\n", $orderInfo);
		$totalLines = count($lines);
//		print $totalLines."<br />";
		$i=0;
		while($i<$totalLines && !preg_match("/.*NO.\s+QTY\s+AUTHOR\s+TITLE\s+PRICE\s+AMT/", $lines[$i])) {
//				print "$i>>>".$lines[$i]."<br />";
			$i++;
		}
		if ($i == $totalLines) { return $this->error(1, "invalid receipt format"); }
		$i++;
		$line = trim($lines[$i]);
		if ($line == "") { $i++; } // skip empty line
		$items = array();
		for (;$i<$totalLines;$i++) {
//			print "$i>>>".$lines[$i]."<br />";
			$itemLine = trim($lines[$i]);
			if (trim($itemLine) == "") { break; } // break on empty line - end of line items
			// process if line item - identification is the \d\d\. pattern on the reverse string
			if (preg_match('/^\d\d\./', strrev($itemLine))) {
				$itemInfo = $this->parseItemLine($itemLine);
				if (!$itemInfo) { return $this->error(3, "error parsing line $i"); }
				$items[] = $itemInfo;
			} else { // else its a continuation of the itemname
				$items[count($items)-1]->itemName .= " $itemLine";
			}
		}
		if (count($items) == 0) { return $this->error(4, "invalid receipt format"); }
		return $this->success($items);
	}

	private function parseItemLine($line) {
		$line=trim($line);
		list($part2,$part1) = explode("SR", strrev($line),2);
		$part1 = strrev(trim($part1)); $part2 = strrev(trim($part2));
//		print "$part1<>$part2<br />";
		if (!preg_match('/(\d+)\s+(\d+)\s+(.*)/', $part1, $p1matches)) { return false; }
		if (!preg_match('/(\d+)\.(\d\d)\s*(\S+)/', $part2, $p2matches)) { return false; }

		$itemArr = array();

		$lineQuantity = $this->parseFloat($p1matches[2]);
		if ($lineQuantity === false) { return false; }
		$lineItemName = trim($p1matches[3]);
		$itemFont = null;
		if (preg_match('/\b[a-z]\w*[A-Z]\w*\b/', $lineItemName)) { // popular font - Shivaji01
			$itemFont = "Shivaji01";
		}


		$unitPrice = $this->parseFloat($p2matches[1].".".$p2matches[2]);
		if ($unitPrice === false) { return false; }
		$lineTotal = $this->parseFloat($p2matches[3]);
		if ($lineTotal === false) { return false; }

		return (object) array(
			"lineQuantity" => $lineQuantity,
			"itemName" => $lineItemName,
			"font" => $itemFont,
			"unitPrice" => $unitPrice,
			"lineTotal" => $lineTotal
		);
	}
}

?>
