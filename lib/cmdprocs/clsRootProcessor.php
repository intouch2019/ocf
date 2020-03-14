<?php

require_once "lib/codes/clsCodes.php";
require_once "lib/user/clsUser.php";
require_once "lib/messages/clsRootMessage.php";
/*
root newactcode
*/
class clsRootProcessor extends clsCmdProcessor {

	public function __construct($command, $commandText) {
		parent::__construct($command, $commandText);
	}

	public function run($incoming, $user) {
		list($rootcmd, $rest) = split(" ", $this->getCommandText(), 2);
		$rootcmd = strtolower($rootcmd);

		if ($incoming->msisdn != "919881064419") {
			return clsRootMessage::notAuthorized()->getMessage($user->locale);
		}

		if ($rootcmd == "newactcode") return $this->newactcode($incoming, $user);
		if ($rootcmd == "offer") return $this->changeoffer($incoming, $user, $rest);

		return clsRootMessage::commandFail()->getMessage($user->locale);
	}

	private function newactcode($incoming, $user) {
		$clsCodes = new clsCodes();
		$new_act_code = $clsCodes->newActivationCode();
		$clsCodes->closeConnection();
		return clsRootMessage::newActivationCode($new_act_code)->getMessage($user->locale);
	}

	private function changeoffer($incoming, $user, $rest) {
		list($code, $offer) = split(" ", $rest, 2);
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		$retMsg = "";
		if ($codeInfo) {
			if (!$offer) {
				$retMsg = $codeInfo->signupmsg;
			} else {
				$clsCodes->updateOffer($codeInfo->id, $offer);
				$retMsg = clsRootMessage::commandSuccess()->getMessage($user->locale);
			}
		} else {
			$retMsg = clsRootMessage::commandFail()->getMessage($user->locale);
		}
		$clsCodes->closeConnection();
		return $retMsg;
	}
}

?>
