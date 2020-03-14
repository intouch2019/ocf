<?php

require_once "lib/db/dbobject.php";
require_once "lib/orders/ReceiptFormat.class.php";
define("DEF_PASS1_SUCCESS", 1);
define("DEF_PASS2_SUCCESS", 2);
define("DEF_PASS1_ERROR", -1);
define("DEF_PASS2_ERROR", -2);
define("DEF_STATUS_REPRINT", -3);

class clsOrder extends dbobject {

	public function __construct($commit=true) {
		parent::__construct($commit);
	}
	
	public function getOrderById($orderid) {
		return $this->fetchObject("select * from it_orders where id = $orderid");
	}

	public function getOrderByBillno($storeid, $bill_no) {
		$bill_no = $this->safe($bill_no);
		$query = "select * from it_orders where storeid=$storeid and bill_no=$bill_no order by id desc limit 1";
		return $this->fetchObject($query);
	}

	public function saveOrder($storeid, $userid, $orderInfo, $billno=null, $billamount=null, $filename=null) {
		$orderInfo = $this->safe($orderInfo);
		$query = "insert into it_orders set storeid=$storeid, userid=$userid";
		if ($orderInfo) { $query .= ", orderinfo=$orderInfo"; }
		if ($billno) {
			$billno = $this->safe($billno);
			$query .= ", bill_no = $billno";
		}
		if ($billamount) { $query .= ", bill_amount=$billamount"; }
		if ($filename) {
			$filename = $this->safe($filename);
			$query .= ", filename = $filename";
		}

		return $this->execInsert($query);
	}

	public function updateOrder($orderid, $billInfo) {
		$query = "update it_orders set status=".DEF_PASS1_SUCCESS;
		if ($billInfo[BILL_NUMBER]) { $query .= ", bill_no = ".$this->safe($billInfo[BILL_NUMBER]); }
		if ($billInfo[BILL_AMOUNT]) { $query .= ", bill_amount=".$billInfo[BILL_AMOUNT]; }
		if ($billInfo[BILL_QUANTITY]) { $query .= ", bill_quantity=".$billInfo[BILL_QUANTITY]; }
		if ($billInfo[BILL_DISCOUNT_VAL]) { $query .= ", bill_discountval=".$billInfo[BILL_DISCOUNT_VAL]; }
		if ($billInfo[BILL_DISCOUNT_PCT]) { $query .= ", bill_discountpct=".$billInfo[BILL_DISCOUNT_PCT]; }
		if ($billInfo[BILL_DATE]) { $query .= ", bill_datetime = ".$this->safe($billInfo[BILL_DATE].' 00:00:00'); }
		else if ($billInfo[BILL_DATETIME]) { $query .= ", bill_datetime = ".$this->safe($billInfo[BILL_DATETIME]); }
		$query .= " where id=$orderid";
		$this->execUpdate($query);
	}

	public function updateOrderDetails($orderid, $billNo, $billAmount, $billQuantity, $billDatetime) {
		$query = "update it_orders set status=".DEF_PASS1_SUCCESS.", bill_quantity=$billQuantity, bill_amount=$billAmount";
		if ($billNo) { $query .= ", bill_no = ".$this->safe($billNo); }
		if ($billDatetime) {
			$dtime = $this->safe($billDatetime);
			$query .= ", bill_datetime = $dtime";
		}
		$query .= " where id=$orderid";
		$this->execUpdate($query);
	}

	public function updateStatus($orderid, $status, $statusmsg) {
		$statusclause="";
		if ($statusmsg) {
			$statusmsg = $this->safe($statusmsg);
			$statusclause = ", statusmsg=$statusmsg";
		}
		$query = "update it_orders set status=$status $statusclause where id=$orderid";
		$this->execUpdate($query);
	}

	public function saveItems($orderid, $storeid, $items) {
		if (!$items || count($items) == 0) { return; }
		foreach ($items as $itemInfo) {
		    $itemName = $itemInfo->itemName;
		    $unitPrice = $itemInfo->unitPrice;
		    $lineQuantity = $itemInfo->lineQuantity;
		    $lineTotal = $itemInfo->lineTotal;
			$rawitemid = $this->saveRawItem($storeid, $itemName, $itemInfo->font);
			$query = "insert into it_rawitemlines set storeid=$storeid, orderid=$orderid, rawitemid=$rawitemid, unitprice=$unitPrice, linequantity=$lineQuantity, linetotal=$lineTotal<br />";
			$this->execInsert("insert into it_rawitemlines set storeid=$storeid, orderid=$orderid, rawitemid=$rawitemid, unitprice=$unitPrice, linequantity=$lineQuantity, linetotal=$lineTotal");
		}
	}

	public function saveRawItem($storeid, $itemName, $itemFont) {
		$itemName = $this->safe($itemName);
		$itemInfo = $this->fetchObject("select * from it_rawitems where storeid=$storeid and itemname=$itemName");
		if ($itemInfo) { return $itemInfo->id; }
		$query = "insert into it_rawitems set storeid=$storeid, itemname=$itemName";
		if ($itemFont) {
			$itemFont = $this->safe($itemFont);
			$query .= ", font=$itemFont";
		}
		return $this->execInsert($query);
	}

	public function billno_pass2_done_check($order) {
		$bill_no = $order->bill_no;
		if (!$bill_no) { return true; } // dont extract items if the bill_no is missing
		$query = "select * from it_orders where storeid=$order->storeid and status=".DEF_PASS2_SUCCESS." and bill_no='$bill_no'";
		$obj = $this->fetchObject($query);
		if (!$obj) { return false; } // bill_no not processed before
		// bill_no processed, set the status to REPRINT
		$this->updateStatus($order->id, DEF_STATUS_REPRINT, "Reprint of order-id:".$obj->id);
		return true;
	}

	function __destruct() {
		parent::__destruct();
	}
}

?>
