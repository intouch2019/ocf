<?

require_once "lib/messages/clsMessage.php";

class clsSignupMessage extends clsMessage {

	private $signupMessages = array(
	"en" => array(
		"MSG_SIGNUP_INVALIDCOMMAND" => "Incorrect command. Send 'intouch signup code activation-code'",
		"MSG_SIGNUP_CODEEXISTS" => "The code you requested [%s] is already in use. Try a different one.",
		"MSG_SIGNUP_ACTCODE_MISSING" => "Activation code not specified. Send 'intouch signup %s activation-code'",
		"MSG_SIGNUP_INVALID_ACTCODE" => "[%s] is not a valid actvation code. Send 'intouch signup %s activation-code'",
		"MSG_SIGNUP_CODECREATED" => "You have successfully reserved the code [%s]. Send 'intouch owner help' to get a list of commands supported."
		)
	);

	public function __construct($shortcode, $params=false) {
		parent::__construct($shortcode, $params);
	}
	
	public static function invalidCommand() {
		return new clsSignupMessage("MSG_SIGNUP_INVALIDCOMMAND");
	}

	public static function codeExists($code) {
		return new clsSignupMessage("MSG_SIGNUP_CODEEXISTS", array($code));
	}

	public static function activationCodeMissing($code) {
		return new clsSignupMessage("MSG_SIGNUP_ACTCODE_MISSING", array($code));
	}

	public static function invalidActivationCode($code, $act_code) {
		return new clsSignupMessage("MSG_SIGNUP_INVALID_ACTCODE", array($act_code, $code));
	}

	public static function codeCreated($code) {
		return new clsSignupMessage("MSG_SIGNUP_CODECREATED", array($code));
	}

	public function getMessageCodes($locale) {
		return $this->signupMessages[$locale];
	}
}

?>
