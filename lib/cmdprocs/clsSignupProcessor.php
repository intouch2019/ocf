<?php

require_once "lib/codes/clsCodes.php";
require_once "lib/messages/clsSignupMessage.php";
/*
signup <retailcode> <retailinfo>
*/
class clsSignupProcessor extends clsCmdProcessor {

	public function __construct($command, $commandText) {
		parent::__construct($command, $commandText);
	}

	public function run($incoming, $user) {
		$clsCodes = new clsCodes();
		list($code, $act_code) = split(" ", $this->getCommandText(), 2);
		if (!$code) {
			return clsSignupMessage::invalidCommand()->getMessage($user->locale);
		}
		$codeInfo = $clsCodes->getCodeInfo($code);
		if ($codeInfo) {
			$clsCodes->closeConnection();
			return clsSignupMessage::codeExists($code)->getMessage($user->locale);
		}
		if (!$act_code || trim($act_code) == "") {
			$clsCodes->closeConnection();
			return clsSignupMessage::activationCodeMissing($code)->getMessage($user->locale);
		}

		$act_code_row = $clsCodes->getActivationCode($act_code);
		// if act_code does not exist or it has already been assigned to a store
		if (!$act_code_row || $act_code_row->codeid) {
			$clsCodes->closeConnection();
			return clsSignupMessage::invalidActivationCode($code, $act_code)->getMessage($user->locale);
		}

		$clsCodes->insert($code, $act_code_row->id, $user->id, $user->id, $incoming->id);
		$clsCodes->closeConnection();

		return clsSignupMessage::codeCreated($code)->getMessage($user->locale);
	}

}

?>
