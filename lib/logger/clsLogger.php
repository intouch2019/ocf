<?php
require_once "lib/db/dbobject.php";

class clsLogger extends dbobject {

	public function logError($msg, $incomingid=false) {
		$msg = $this->safe($msg);
		$query = "insert into it_logs set msgtype=".LOG_MSGTYPE_ERROR.", message=$msg";
		if ($incomingid) {
			$query .= ", incomingid=$incomingid";
		}
		$this->_insert($query);
	}

	public function logException($xcp, $incomingid=false) {
		$msg = $xcp->getMessage();
		$query = "insert into it_logs set msgtype=".LOG_MSGTYPE_EXCEPTION.", message=$msg";
		if ($incomingid) {
			$query .= ", incomingid=$incomingid";
		}
		$this->_insert($query);
	}

	public function logInfo($msg, $incomingid=false,$apiname=false,$android_id=false) { 
               // $msg = $this->safe($msg);
		$query = "insert into it_logs set msgtype=".LOG_MSGTYPE_INFO.", message='$msg'";
		if ($incomingid) {
			$query .= ", incomingid=$incomingid";
		}
                if($apiname){
                    $apiname = $this->safe($apiname);
                    $query .= " , apiname = $apiname";
                }
                if($android_id){
                    $android_id = $this->safe($android_id);
                    $query .= " , android_id = $android_id";
                }
		$this->_insert($query);
	}

	public function logReply($incomingid=false, $msg,$apiname=false) {
		$msg = $this->safe($msg);                
		//$query = "insert into it_logs set incomingid=$incomingid, msgtype=".LOG_MSGTYPE_REPLY.", message=$msg";
                $query = "insert into it_logs set msgtype=".LOG_MSGTYPE_REPLY.", message=$msg";
		if($apiname){
                    $apiname = $this->safe($apiname);
                    $query .= " , apiname = $apiname";
                }
                if ($incomingid) {
			$query .= ", incomingid=$incomingid";
		}
                $this->_insert($query);
	}

	public function logTrial($incomingid, $msg) {
		$msg = $this->safe($msg);
		$query = "insert into it_logs set incomingid=$incomingid, msgtype=".LOG_MSGTYPE_TRIAL.", message=$msg";
		$this->_insert($query);
	}

	public function _insert($query) {
		$ipaddr = isset($_SERVER['REMOTE_ADDR']) ? $this->safe($_SERVER['REMOTE_ADDR']) : false;
		if ($ipaddr) {
			$query .= ", ipaddr=$ipaddr";
		}      
//                error_log("\nLOG  Query:- $query\n",3,"../../../ajax/tmp.txt");
		$this->execInsert($query);
	}

}
