<?php

require_once "lib/messages/clsMessage.php";

class clsGeneralMessage extends clsMessage {

	private $fanMessages = array(
	"en" => array(
		"MSG_GENERAL_INFO" => "inTouch is a loyalty program for stores/restaurants/etc. If you wish to try this out for your store, send 'intouch trial'",
		"MSG_GENERAL_TRIAL" => "Thank you for your interest in inTouch. We will contact you at '%s' shortly."
		)
	);

	public function __construct($shortcode, $params=false) {
		parent::__construct($shortcode, $params);
	}
	
	public static function showInfo() {
		return new clsGeneralMessage("MSG_GENERAL_INFO");
	}

	public static function trial($phoneno) {
		return new clsGeneralMessage("MSG_GENERAL_TRIAL", array($phoneno));
	}

	public function getMessageCodes($locale) {
		return $this->fanMessages[$locale];
	}
}

?>
