<?php
require_once "lib/codes/clsCodes.php";
require_once "lib/codes/clsVouchers.php";
require_once "lib/messages/clsFanMessage.php";
require_once "lib/sms/clsSMSHelper.php";
/*
signup <retailcode> <retailinfo>
*/
class clsAddProcessor extends clsCmdProcessor {

	public function __construct($command, $commandText) {
		parent::__construct($command, $commandText);
	}

	public function run($incoming, $user) {
		$cmd = strtolower($this->getCommand());
		$cmdText = $this->getCommandText();
		list($tok1, $rest) = split(" ", $cmdText, 2);
		$tok1 = strtolower($tok1);

		if ($cmd == "help") {
			return clsFanMessage::showHelp()->getMessage($user->locale);
		}

		if ($cmd == "myinfo") {
			return $this->myinfo($user);
		}

		if ($cmd == "mystores") {
			return $this->mystores($user);
		}

		if ($cmd == "mypoints") {
			return $this->mypoints($user, $tok1);
		}

		if ($cmd == "addd" && $tok1 == "jlpune") {
			return $this->jlpune_add($incoming, $user, $rest);
		} else if ($cmd == "add") {
			return $this->add($incoming, $user, $tok1);
		}

		if ($cmd == "remove") {
			return $this->remove($incoming, $user, $tok1);
		}

		if ($cmd == "review") {
			return $this->review($incoming, $user, $tok1, $rest);
		}

		if ($cmd == "redeem") {
			return $this->redeem($incoming, $user, $tok1, $rest);
		}

		return clsFanMessage::showHelp()->getMessage($user->locale);
	}

	private function myinfo($user) {
		$intouchno = $user->intouchno;
		if (!$intouchno) {
			$clsUser = new clsUser();
			$intouchno = $clsUser->generateIntouchno($user->id);
			$user->intouchno = $intouchno;
		}
		return clsFanMessage::myinfo($intouchno)->getMessage($user->locale);
	}

	private function mystores($user) {
		$clsCodes = new clsCodes();
		$mystores = $clsCodes->getMyStores($user->id);
		if (!$mystores || count($mystores) == 0) {
			return clsFanMessage::noStores()->getMessage($user->locale);
		}

		$count = count($mystores);
		$msg = "You are a fan of $count stores:";
		$first=true;
		foreach ($mystores as $codeInfo) {
			if (!$first) { $msg .= ","; }
			else { $first=false; }
			$msg .= $codeInfo->code;
		}
		return $msg;
	}

	private function mypoints($user, $code) {
		if (!$code) {
			return clsFanMessage::mypointsInvalidCommand()->getMessage($user->locale);
		}
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		if (!$codeInfo) {
			return clsFanMessage::codeDoesnotExists($code)->getMessage($user->locale);
		}

		$points = $clsCodes->getTotalPoints($user->id, $codeInfo->id);
		return "You have $points redeemable points at '$codeInfo->store_name'";
	}

	private function jlpune_add($incoming, $user, $rest) {
		if (!$rest || trim($rest) == "") {
			return "Receipt number is missing. Please send the message as 'intouch add JLPUNE your-receipt-number'";
		}
		list($receiptno, $ignore) = split(" ", $rest, 2);
		if (!ctype_digit($receiptno)) {
			return "The receipt number you entered '$receiptno' is incorrect. Please correct it and send the message again";
		}

		$code="jlpune";
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		if (!$codeInfo) {
			return clsFanMessage::codeDoesnotExists($code)->getMessage($user->locale);
		}

		if (!$clsCodes->fanExists($codeInfo->id, $user->id)) {
			$intouchno = $user->intouchno;
			if (!$intouchno) {
			$clsUser = new clsUser();
				$intouchno = $clsUser->generateIntouchno($user->id);
				$user->intouchno = $intouchno;
			}
	
			$msg = $clsCodes->addFanProcMsg($codeInfo, $user, $incoming->id);
			$smsHelper = new clsSMSHelper();
			$smsHelper->sendOne($user->phoneno, $msg, $incoming->id);
		}

		return "Thank you for entering the New Year Sweepstakes at Juice Lounge. Please bring your receipt '$receiptno' on Sat, Jan 1st, 2011 at 7pm to the store for the lucky draw.";
	}

	private function add($incoming, $user, $code) {
		if (!$code) {
			return clsFanMessage::invalidAddCommand()->getMessage($user->locale);
		}
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		if (!$codeInfo) {
			return clsFanMessage::codeDoesnotExists($code)->getMessage($user->locale);
		}

		if ($clsCodes->fanExists($codeInfo->id, $user->id)) {
			return clsFanMessage::alreadyAFan($code, $user->intouchno)->getMessage($user->locale);
		}

		$intouchno = $user->intouchno;
		if (!$intouchno) {
			$clsUser = new clsUser();
			$intouchno = $clsUser->generateIntouchno($user->id);
			$user->intouchno = $intouchno;
		}

		return $clsCodes->addFanProcMsg($codeInfo, $user, $incoming->id);
	}

	private function remove($incoming, $user, $code) {
		if (!$code) {
			return clsFanMessage::invalidRemoveCommand()->getMessage($user->locale);
		}
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		if (!$codeInfo) {
			return clsFanMessage::codeDoesnotExists($code)->getMessage($user->locale);
		}

		if (!$clsCodes->fanExists($codeInfo->id, $user->id)) {
			return clsFanMessage::notAFan($code, $user->intouchno)->getMessage($user->locale);
		}

		$clsCodes->removeFan($codeInfo->id, $user->id);

		return clsFanMessage::removeSuccessful($code, $user->intouchno)->getMessage($user->locale);
	}

	private function review($incoming, $user, $pointsid, $reviewtext) {
		if (!$pointsid || !$reviewtext) {
			return clsFanMessage::reviewInvalidCommand()->getMessage($user->locale);
		}
		$clsCodes = new clsCodes();
		$pointsInfo = $clsCodes->getPointsInfo($pointsid);
		if (!$pointsInfo) {
			return clsFanMessage::reviewIncorrectReviewCode($pointsid)->getMessage($user->locale);
		}

		if ($user->id != $pointsInfo->userid) {
			return clsFanMessage::reviewNotAuthorized($pointsid)->getMessage($user->locale);
		}

		$clsCodes->updateReview($pointsid, $reviewtext);

		$codeInfo = $clsCodes->getCodeInfoById($pointsInfo->storeid);
		$msgParam = $pointsid;
		if ($codeInfo) { $msgParam = $codeInfo->code; }

		return clsFanMessage::reviewSuccessful($msgParam)->getMessage($user->locale);
	}

	private function redeem($incoming, $user, $code, $numpoints) {
		if (!$code || !is_numeric($numpoints)) {
			return clsFanMessage::redeemInvalidCommand()->getMessage($user->locale);
		}
		$numpoints = intval($numpoints);
		$clsCodes = new clsCodes();
		$codeInfo = $clsCodes->getCodeInfo($code);
		if (!$codeInfo) {
			return clsFanMessage::codeDoesnotExists($code)->getMessage($user->locale);
		}
		if (!$codeInfo->redeem_offerid) {
			return clsFanMessage::redeemNotAllowed($codeInfo->store_name)->getMessage($user->locale);
		}
		$redeemOffer = $clsCodes->getOffer($codeInfo->redeem_offerid);
		if (!$redeemOffer->isactive) {
			return clsFanMessage::redeemNotAllowed($codeInfo->store_name)->getMessage($user->locale);
		}

		$totalPoints = $clsCodes->getTotalPoints($user->id, $codeInfo->id);
		if ($numpoints > $totalPoints) {
			return clsFanMessage::redeemNotEnoughPoints($totalPoints, $codeInfo->store_name)->getMessage($user->locale);
		}

		$clsVouchers = new clsVouchers();
		$voucher = $clsVouchers->makeVoucher($codeInfo->id, $codeInfo->redeem_offerid, $user->id, "Rs. $numpoints off your purchase");
		$clsCodes->redeemPoints($codeInfo->id, $user->id, $numpoints, $voucher->id);

		return "Use voucher '$voucher->vcode' to claim 'Rs. $numpoints off your purchase' at '$codeInfo->store_name'";
	}
}

?>
