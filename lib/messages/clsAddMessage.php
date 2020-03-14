<?

require_once "lib/messages/clsMessage.php";

class clsAddMessage extends clsMessage {

	private $addMessages = array(
	"en" => array(
		"MSG_ADD_INVALIDCOMMAND" => "Incorrect command. Send:intouch add code",
		"MSG_ADD_CODEDOESNOTEXIST" => "The code [%s] does not exist. Please check the store code and try again.",
		"MSG_ADD_ALREADYAFAN" => "You are already a fan of [%s]. Your personal intouch no:%s. Send [intouch help] to get a list of supported commands.",
		"MSG_ADD_SUCCESSFUL" => "You are now a fan of [%s]. Your personal intouch no:%s. Send [intouch help] to get a list of supported commands."
		)
	);

	public function __construct($shortcode, $params=false) {
		parent::__construct($shortcode, $params);
	}
	
	public static function invalidCommand() {
		return new clsAddMessage("MSG_ADD_INVALIDCOMMAND");
	}

	public static function codeDoesnotExists($code) {
		return new clsAddMessage("MSG_ADD_CODEDOESNOTEXIST", array($code));
	}

	public static function alreadyAFan($code, $intouchno) {
		return new clsAddMessage("MSG_ADD_ALREADYAFAN", array($code, $intouchno));
	}

	public static function addSuccessful($code, $intouchno) {
		return new clsAddMessage("MSG_ADD_SUCCESSFUL", array($code,$intouchno));
	}

	public function getMessageCodes($locale) {
		return $this->addMessages[$locale];
	}
}

?>
