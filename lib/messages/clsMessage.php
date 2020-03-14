<?php
require_once "lib/logger/clsLogger.php";

abstract class clsMessage {

	var $shortcode, $params;
	protected function __construct($shortcode, $params=false) {
		$this->shortcode = $shortcode;
		$this->params = $params;
	}
	
	protected abstract function getMessageCodes($locale);

	public function getMessage($locale="en") {
//print "getMessage:"+$this->shortcode+":"+print_r($this->params,true)+"<br />";
		$msgCodes = $this->getMessageCodes($locale);
		if (!$msgCodes) { // default to "en"
			$msgCodes = $this->getMessageCodes("en");
		}
		$msg = false;
		if (!$msgCodes) {
			$clsLogger = new clsLogger();
			$clsLogger->logError("Empty msgcode list in class:".get_class($this));
			$msg = $this->shortcode;
			if ($this->params) { $msg .= print_r($this->params,true); }
		} else {
			$msg = $msgCodes[$this->shortcode];
			if (!$msg) {
				$clsLogger = new clsLogger();
				$clsLogger->logError("Missing msgcode:".$this->shortcode);
				$msg = $this->shortcode;
				if ($this->params) { $msg .= print_r($this->params,true); }
			} else if ($this->params) {
				$msg = vsprintf($msg, $this->params);
			}
		}
		return $msg;
	}
}

?>