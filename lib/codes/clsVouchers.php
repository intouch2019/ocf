<?php

require_once "lib/db/dbobject.php";

class clsVouchers extends dbobject {

	public function makeVoucher($storeid, $offerid, $userid, $offer) {
		$offset = DEF_VCODE_OFFSET;
		$offer = $this->safe($offer);
		$voucherid = $this->execInsert("insert into it_vouchers set storeid=$storeid, offerid=$offerid, userid=$userid, vcode='code-not-set', offer=$offer");
		$vcode = strtoupper(base_convert($voucherid+$offset, 10, 36));
		$this->execUpdate("update it_vouchers set vcode='$vcode' where id=$voucherid");
		return (object) array("id"=>$voucherid, "vcode"=>$vcode);
	}

	public function findVoucher($storeid, $userid, $vcode) {
		$vcode=$this->safe($vcode);
		$query = "select v.* from it_vouchers v, it_storeoffers o where v.storeid=$storeid and v.userid=$userid and v.vcode=$vcode and v.offerid = o.id and o.isactive=1";
		return $this->fetchObject($query);
	}

	public function claimVoucher($voucherid) {
		return $this->execUpdate("update it_vouchers set claim_date=now() where id=$voucherid");
	}

	function __destruct() {
		parent::__destruct();
	}
}

?>
