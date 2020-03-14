<?php

require_once "lib/messages/clsMessage.php";

class clsFanMessage extends clsMessage {

	private $fanMessages = array(
	"en" => array(
		"MSG_FAN_HELP" => "Supported cmds: 'intouch myinfo', 'intouch mystores', 'intouch add store-code', 'intouch remove store-code'",
		"MSG_FAN_ADD_INVALID_COMMAND" => "Incorrect command. Send:intouch add code",
		"MSG_FAN_REMOVE_INVALID_COMMAND" => "Incorrect command. Send:intouch remove code",
		"MSG_FAN_CODEDOESNOTEXIST" => "The code '%s' does not exist. Please check the store code and try again.",
		"MSG_FAN_ALREADYAFAN" => "You are already a fan of '%s'. Your personal intouch no:%s. Send 'intouch help' for a list of supported commands.",
		"MSG_FAN_NOTAFAN" => "You are currently not a fan of '%s'. Your personal intouch no:%s. Send 'intouch help' for a list of supported commands.",
		"MSG_FAN_ADD_SUCCESSFUL" => "You are now a fan of '%s'. Your personal intouch no:%s. Send 'intouch help' for a list of supported commands.",
		"MSG_FAN_ADD_SUCCESSFUL_SIGNUPMSG" => "You are now a fan of '%s'. Your personal intouch no:%s. Claim '%s'",
		"MSG_FAN_REMOVE_SUCCESSFUL" => "You will stop receiving messages from '%s' shortly. To start receiving offers/promotions again send 'intouch add %s' to 09220092200",
		"MSG_FAN_NOSTORES" => "You donot have any stores listed as favorites. Send 'intouch help' for a list of supported commands.",
		"MSG_FAN_MYINFO" => "Your personal intouch no:%s. Send 'intouch help' for a list of supported commands.",

		"MSG_REVIEW_INVALIDCOMMAND" => "Incorrect review command. Send 'intouch review review-code your-message'",
		"MSG_REVIEW_INCORRECT_CODE" => "Incorrect review-code:%s. Please check and send again 'intouch review review-code your-message'",
		"MSG_REVIEW_NOT_AUTHORIZED" => "You are not authorized to use the review-code:%s. Please check the review-code and try again.",
		"MSG_REVIEW_SUCCESSFUL" => "Thank you for submitting your review for '%s'. If you wish to update your review, you may do so by re-sending the message.",

		"MSG_CHECKIN_THANKYOU" => "Thank you for visiting '%s'. %s will be serving you today.",
		"MSG_REDEEM_THANKYOU" => "Thank you for claiming the offer at '%s'. Make sure you earn points on future visits by mentioning your InTouch Number:%s",
		"MSG_ADDPOINTS_DONE" => "Thank you for visiting '%s'. Points earned today:%s, Total points:%s. Tell us about your visit by sending 'intouch review %s your-message' to %s",

		"MSG_ADD_CUSTOMER_SIGNUP_OFFER" => "Claim '%s' at %s. Use your loyalty no:%s and earn points on your purchases. To stop following, send 'intouch remove %s' to %s",
		"MSG_ADD_CUSTOMER" => "You are now a fan of '%s'. Your personal intouch no:%s. Earn points on your purchases, receive special offers/discounts. To stop following '%s', send 'intouch remove %s' to %s",
		"MSG_STORE_ADDFAN_SIGNUP_OFFER" => "Welcome to the IntouchRewards program. Your Intouch no is %s. On your next visit to '%s', use voucher code '%s' to claim '%s'",
		"MSG_STORE_ADDFAN_NOSIGNUP_OFFER" => "Welcome to the IntouchRewards program. Mention your Intouch No %s on your future visits to '%s' to Earn points on purchases.",
		"MSG_STORE_VOUCHER_REDEEMED" => "Thank you for redeeming your voucher '%s' for '%s' at '%s'",
		"MSG_MYPOINTS_INVALIDCOMMAND" => "Incorrect command. Send 'intouch mypoints storecode'",
		"MSG_REDEEM_INVALIDCOMMAND" => "Incorrect command. e.g. Send 'intouch redeem storecode 45' to redeem 45 points.",
		"MSG_REDEEM_NOT_ALLOWED" => "'%s' is not setup for redeeming points yet. Please contact the store owner to find out when this will be activated.",
		"MSG_REDEEM_NOT_ENOUGH_POINTS" => "You only have %s points available for redeeming at '%s'",
		)
	);

	public function __construct($shortcode, $params=false) {
		parent::__construct($shortcode, $params);
	}
	
	public static function showHelp() {
		return new clsFanMessage("MSG_FAN_HELP");
	}

	public static function invalidAddCommand() {
		return new clsFanMessage("MSG_FAN_ADD_INVALID_COMMAND");
	}

	public static function invalidRemoveCommand() {
		return new clsFanMessage("MSG_FAN_REMOVE_INVALID_COMMAND");
	}

	public static function codeDoesnotExists($code) {
		return new clsFanMessage("MSG_FAN_CODEDOESNOTEXIST", array($code));
	}

	public static function alreadyAFan($code, $intouchno) {
		return new clsFanMessage("MSG_FAN_ALREADYAFAN", array($code, $intouchno));
	}

	public static function notAFan($code, $intouchno) {
		return new clsFanMessage("MSG_FAN_NOTAFAN", array($code, $intouchno));
	}

	public static function addSuccessful($code, $intouchno) {
		return new clsFanMessage("MSG_FAN_ADD_SUCCESSFUL", array($code,$intouchno));
	}

	public static function addSuccessfulSignupmsg($code, $intouchno, $signupmsg) {
		return new clsFanMessage("MSG_FAN_ADD_SUCCESSFUL_SIGNUPMSG", array($code,$intouchno,$signupmsg));
	}

	public static function removeSuccessful($code, $intouchno) {
		return new clsFanMessage("MSG_FAN_REMOVE_SUCCESSFUL", array($code,$code));
	}

	public static function noStores() {
		return new clsFanMessage("MSG_FAN_NOSTORES");
	}

	public static function myinfo($intouchno) {
		return new clsFanMessage("MSG_FAN_MYINFO", array($intouchno));
	}

	public static function reviewInvalidCommand() {
		return new clsFanMessage("MSG_REVIEW_INVALIDCOMMAND");
	}

	public static function reviewIncorrectReviewCode($pointsid) {
		return new clsFanMessage("MSG_REVIEW_INCORRECT_CODE", array($pointsid));
	}

	public static function reviewNotAuthorized($checkinid) {
		return new clsFanMessage("MSG_REVIEW_NOT_AUTHORIZED", array($checkinid));
	}

	public static function reviewSuccessful($storecode) {
		return new clsFanMessage("MSG_REVIEW_SUCCESSFUL", array($storecode));
	}

	public static function checkinThankYou($storename, $servername) {
		return new clsFanMessage("MSG_CHECKIN_THANKYOU", array($storename, $servername));
	}

	public static function redeemThankYou($storename, $intouchno) {
		return new clsFanMessage("MSG_REDEEM_THANKYOU", array($storename, $intouchno));
	}

	public static function addpointsDone($storename, $currPoints, $totalPoints, $checkinid, $phoneno) {
		return new clsFanMessage("MSG_ADDPOINTS_DONE", array($storename, $currPoints, $totalPoints, $checkinid, $phoneno));
	}

	public static function addCustomerSignupOffer($code, $storename, $intouchno, $signupmsg, $phoneno) {
		return new clsFanMessage("MSG_ADD_CUSTOMER_SIGNUP_OFFER", array($signupmsg, $storename, $intouchno, $code, $phoneno));
	}

	public static function addCustomer($code, $storename, $intouchno, $phoneno) {
		return new clsFanMessage("MSG_ADD_CUSTOMER", array($storename, $intouchno, $storename, $code, $phoneno));
	}

	public static function addFanSignupOffer($storename, $intouchno, $offer_text, $vcode) {
		return new clsFanMessage("MSG_STORE_ADDFAN_SIGNUP_OFFER",  array($intouchno, $storename, $vcode, $offer_text));
	}

	public static function addFanNoSignupOffer($storename, $intouchno) {
		return new clsFanMessage("MSG_STORE_ADDFAN_NOSIGNUP_OFFER",  array($intouchno, $storename));
	}

	public static function voucherRedeemed($storename, $vcode, $offer_text) {
		return new clsFanMessage("MSG_STORE_VOUCHER_REDEEMED",  array($vcode, $offer_text, $storename));
	}

	public static function mypointsInvalidCommand() {
		return new clsFanMessage("MSG_MYPOINTS_INVALIDCOMMAND");
	}

	public static function redeemInvalidCommand() {
		return new clsFanMessage("MSG_REDEEM_INVALIDCOMMAND");
	}

	public static function redeemNotAllowed($store_name) {
		return new clsFanMessage("MSG_REDEEM_NOT_ALLOWED",  array($store_name));
	}

	public static function redeemNotEnoughPoints($totalPoints, $store_name) {
		return new clsFanMessage("MSG_REDEEM_NOT_ENOUGH_POINTS",  array($totalPoints, $store_name));
	}

	public function getMessageCodes($locale) {
		return $this->fanMessages[$locale];
	}
}

?>
