<?php

require_once "lib/codes/clsCodes.php";
require_once "lib/messages/clsOwnerMessage.php";
/*
signup <retailcode> <retailinfo>
*/
class clsOwnerProcessor extends clsCmdProcessor {

	public function __construct($command, $commandText) {
		parent::__construct($command, $commandText);
	}

	public function run($incoming, $user) {
		$cmd = strtolower($this->getCommand());
		$cmdText = $this->getCommandText();
		list($tok1, $rest) = split(" ", $cmdText, 2);
		$tok1 = strtolower($tok1);

		if ($cmd == "owner" and $tok1 == "help") {
			return clsOwnerMessage::showHelp()->getMessage($user->locale);
		}

		if ($cmd == "stats") {
			if ($tok1) {
				return $this->stats($incoming, $user, $tok1);
			} else {
				return clsOwnerMessage::showStatsHelp()->getMessage($user->locale);
			}
		}

		if ($cmd == "publish") {
			if ($tok1 && $rest) {
				return $this->publish($incoming, $user, $tok1, $rest);
			} else {
				return clsOwnerMessage::showPublishHelp()->getMessage($user->locale);
			}
		}

		return clsOwnerMessage::showHelp()->getMessage($user->locale);
	}

	private function stats($incoming, $user, $code) {
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		if (!$codeInfo ||
		    !in_array($user->id, array($codeInfo->owner, $codeInfo->subadmin1, $codeInfo->subadmin2))) {
			return clsOwnerMessage::notOwner($code)->getMessage($user->locale);
		}

		$fanSummary = $clsCodes->getFanSummary($codeInfo->id);

		return clsOwnerMessage::showStats($fanSummary->totalFans, $fanSummary->activeFans)->getMessage($user->locale);
	}

	private function publish($incoming, $user, $code, $message) {
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		if (!$codeInfo ||
		    !in_array($user->id, array($codeInfo->owner, $codeInfo->subadmin1, $codeInfo->subadmin2))) {
			return clsOwnerMessage::notOwner($code)->getMessage($user->locale);
		}

		$fanSummary = $clsCodes->getFanSummary($codeInfo->id);
		if ($fanSummary->activeFans == 0) {
			return clsOwnerMessage::noActiveFans($code)->getMessage($user->locale);
		}

		// todo: add code to broadcast the message

		return clsOwnerMessage::messageSent($fanSummary->activeFans)->getMessage($user->locale);
	}
}

?>
