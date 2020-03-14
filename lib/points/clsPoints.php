<?php
require_once "lib/db/dbobject.php";

class clsPoints extends dbobject {

	public function __construct($commit=true) {
		parent::__construct($commit);
	}
        
        public function assignLeadTrackPoints($custid){
           $query = "insert into it_points set userid = $custid , bill_no='LEADTRACK',points=10";  
           return $this->execInsert($query);
        }
        
        /*public function removeInvoicePoints($distid, $invoiceid) {
            $query = "delete from it_dealer_points where and dist_id=$distid and invoice_id=$invoiceid";
            $this->execQuery($query);
        }
	
	public function getPointsEarnedByUserId($userid) {
		$query = "select p.*,sum(p.points_earned) as sum,c.store_name as store_name from it_points p, it_codes c where p.userid = $userid and p.storeid = c.id and p.points_earned>0 group by storeid";	
		return $this->fetchObjectArray($query);
	}

	public function getPointsRedeemedByUserId($userid) {
		$query = "select p.*,sum(p.points_earned) as sum,c.store_name as store_name from it_points p, it_codes c where p.userid = $userid and p.storeid = c.id and p.points_earned<0 group by storeid";	
		return $this->fetchObjectArray($query);
	}

	public function getPointsEarnedByStoreId($userid,$storeid) {
		$query = "select p.* from it_points p, it_codes c where p.userid = $userid and p.storeid = c.id and p.storeid=$storeid and p.points_earned>0";	
		return $this->fetchObjectArray($query);
	}

	public function getPointsRedeemedByStoreId($userid,$storeid) {
		$query = "select p.* from it_points p, it_codes c where p.userid = $userid and p.storeid = c.id and p.storeid=$storeid and p.points_earned<0";	
		return $this->fetchObjectArray($query);
	}

        public function getStoreNameById($id) {
		//echo "select store_name from it_codes where id = $id";
		return $this->fetchObject("select store_name from it_codes where id = $id");
	}

	public function redeemPoints($shopperid,$storeid,$points) {
		$query="insert into it_points (userid,storeid,points_earned) values($shopperid,$storeid,-$points)";
		return $this->execInsert($query);
	}

	public function getTotalPointsEarnedInStore($userid,$storeid) {
		$query = "select sum(p.points_earned) as sum,c.store_name as store_name from it_points p, it_codes c where p.userid = $userid and p.storeid = c.id and c.id=$storeid and p.points_earned>0";	
		return $this->fetchObject($query);
	}

	public function getTotalPointsRedeemedInStore($userid,$storeid) {
		$query = "select sum(p.points_earned) as sum,c.store_name as store_name from it_points p, it_codes c where p.userid = $userid and p.storeid = c.id  and c.id=$storeid and p.points_earned<0";	
		return $this->fetchObject($query);
	}*/

	function __destruct() {
		parent::__destruct();
	}
}

?>
