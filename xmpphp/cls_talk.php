<?php
require_once 'XMPPHP/XMPP.php';
require_once 'lib/logger/clsLogger.php';

class cls_talk {

	var $conn;
	var $logger;

	public function __construct() {
		#Use XMPPHP_Log::LEVEL_VERBOSE to get more logging for error reports
		#If this doesn't work, are you running 64-bit PHP with < 5.2.6?
		$this->conn = new XMPPHP_XMPP('talk.google.com', 5222, 'data', 'int0uchd@t@', 'xmpphp', 'intouchrewards.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
		$this->logger = new clsLogger();
		try {
			$this->conn->connect();
			$this->conn->processUntil('session_start');
			$this->conn->presence();
		} catch(XMPPHP_Exception $xcp) {
			$this->logError("Failed to initialize xmpp connection:".$xcp->getMessage());
		}
	}

	public function sendMessage($users, $message) {
		try {
			foreach ($users as $username) {
				$this->conn->message($username, $message);
			}
		} catch(XMPPHP_Exception $xcp) {
			$this->logError("Failed to send message $message:".$xcp->getMessage());
		}
	}

	public function closeConnection() {
		if ($this->conn) { $this->conn->disconnect(); }
	}

	public function __destruct() {
		$this->closeConnection();
	}
}
?>
