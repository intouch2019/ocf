<?php

require_once "lib/messages/clsGeneralMessage.php";
require_once "lib/logger/clsLogger.php";
/*
signup <retailcode> <retailinfo>
*/
class clsGeneralProcessor extends clsCmdProcessor {

	public function __construct($command, $commandText) {
		parent::__construct($command, $commandText);
	}

	public function run($incoming, $user) {
		$cmd = strtolower($this->getCommand());
		$cmdText = $this->getCommandText();
		list($tok1, $rest) = split(" ", $cmdText, 2);
		$tok1 = strtolower($tok1);

		if ($cmd == "default" || $cmd == "info") {
			return clsGeneralMessage::showInfo()->getMessage($user->locale);
		}

		if ($cmd == "trial") {
			return $this->trial($incoming, $user);
		}

		return clsGeneralMessage::showInfo()->getMessage($user->locale);
	}

	private function trial($incoming, $user) {
		$clsLogger = new clsLogger();
		$clsLogger->logTrial($incoming->id, "Trial request from:".$incoming->msisdn);

		return clsGeneralMessage::trial($incoming->msisdn)->getMessage($user->locale);
	}
}

?>
