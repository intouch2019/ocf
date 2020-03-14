<?

require_once "lib/messages/clsMessage.php";

class clsOwnerMessage extends clsMessage {

	private $ownerMessages = array(
	"en" => array(
		"MSG_OWNER_HELP" => "Support commands are 'intouch stats code', 'intouch publish code promotional-message'",
		"MSG_OWNER_STATS_HELP" => "Send 'intouch stats your-code' to get a summary for your store.",
		"MSG_OWNER_PUBLISH_HELP" => "Send 'intouch publish your-code promotional-message' to send a message to all your active fans",
		"MSG_OWNER_SHOW_STATS" => "You have %s active fans out of a total of %s",
		"MSG_OWNER_NOT_OWNER" => "You dont have owner privileges for code '%s'",
		"MSG_OWNER_MESSAGE_SENT" => "Your message is being sent to %s active fans",
		"MSG_OWNER_NOACTIVEFANS" => "There are no active fans of '%s'. Message not sent."
		)
	);

	public function __construct($shortcode, $params=false) {
		parent::__construct($shortcode, $params);
	}
	
	public static function showHelp() {
		return new clsOwnerMessage("MSG_OWNER_HELP");
	}

	public static function showStatsHelp() {
		return new clsOwnerMessage("MSG_OWNER_STATS_HELP");
	}

	public static function showPublishHelp() {
		return new clsOwnerMessage("MSG_OWNER_PUBLISH_HELP");
	}

	public static function showStats($totalFans, $activeFans) {
		return new clsOwnerMessage("MSG_OWNER_SHOW_STATS", array($activeFans, $totalFans));
	}

	public static function notOwner($code) {
		return new clsOwnerMessage("MSG_OWNER_NOT_OWNER", array($code));
	}

	public static function messageSent($activeFans) {
		return new clsOwnerMessage("MSG_OWNER_MESSAGE_SENT", array($activeFans));
	}

	public static function noActiveFans($code) {
		return new clsOwnerMessage("MSG_OWNER_NOACTIVEFANS", array($code));
	}

	public function getMessageCodes($locale) {
		return $this->ownerMessages[$locale];
	}
}

?>
