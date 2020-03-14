<?php 

require_once "lib/logger/clsLogger.php";
require_once "lib/email/EmailSender.php";

class EmailHelper {

private static function sendEmail($email,$template,$params,$user=null) {
	try {
		$emailSender = new EmailSender();
		$emailSender->sendEmail($email, $template, $params, $user);
	} catch (Exception $xcp) {
		$logger = new clsLogger();
		$logger->logException($xcp);
	}
}

public static function signupValidate($email,$vkey) {
	EmailHelper::sendEmail($email, "signupvalidate.tpl.html", array("%INC_VKEY%" => $vkey, "%INC_EMAIL%" => $email));
}

}

?>
