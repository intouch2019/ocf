<?php

require_once "lib/messages/clsMessage.php";

class clsRootMessage extends clsMessage {

	private $rootMessages = array(
	"en" => array(
		"MSG_ROOT_NOTAUTHORIZED" => "You are not authorized to perform this operation.",
		"MSG_ROOT_NEWACTIVATION" => "Here is a new activation code:%s",
		"MSG_ROOT_CMD_FAIL" => "There was a problem with your request.",
		"MSG_ROOT_CMD_SUCCESS" => "Your request has been processed."
		)
	);

	public function __construct($shortcode, $params=false) {
		parent::__construct($shortcode, $params);
	}
	
	public static function notAuthorized() {
		return new clsRootMessage("MSG_ROOT_NOTAUTHORIZED");
	}

	public static function commandSuccess() {
		return new clsRootMessage("MSG_ROOT_CMD_SUCCESS");
	}

	public static function commandFail() {
		return new clsRootMessage("MSG_ROOT_CMD_FAIL");
	}

	public static function newActivationCode($act_code) {
		return new clsRootMessage("MSG_ROOT_NEWACTIVATION", array($act_code));
	}

	public function getMessageCodes($locale) {
		return $this->rootMessages[$locale];
	}
}

?>
