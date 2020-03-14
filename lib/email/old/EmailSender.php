<?php 

require_once "htmlMimeMail5.php";
require_once "lib/logger/clsLogger.php";

class EmailSender {

var $mimemail;
var $fromemail=DEF_SUPPORT_EMAIL;
var $bcc = array();
var $logger;

function EmailSender($copysupport=true) {
	$this->mimemail = new htmlMimeMail5();
	if ($copysupport) { $this->bcc[] = DEF_SUPPORT_EMAIL; }
	$this->logger = new clsLogger();
}

function setFromEmail($email) {
	$this->fromemail = $email;
}

function setBcc($arr) {
	$this->bcc = array_unique(array_merge($this->bcc, $arr));
}

function sendEmail($emailAddr, $templateFile, $params, $user=null) {

	if (!$emailAddr) { $emailAddr = $this->getCurrUserEmail($user); }
	if (!$emailAddr) {
		return;
	}

	$emailMsg = $this->processTemplate($templateFile, $params, $user);
if (!$emailMsg) {
$this->logger->logError("Failed to process email template:$emailAddr, $templateFile, $params, $user");
return;
}

	/**
	* Set the from address
	*/
	$this->mimemail->setFrom($this->fromemail);

	if (count($this->bcc) > 0) {
		$this->mimemail->setBcc(implode("; ", $this->bcc));
	}

	/**
	* Set the subject
	*/
	$this->mimemail->setSubject($emailMsg->getSubject());

	/**
	* Set the HTML of the email
	*/
	$this->mimemail->setHTML($emailMsg->getMsgbody());

	/**
	* Send the email
	*/
	$this->mimemail->send(array($emailAddr), "mail");
	$this->logger->logInfo("Email sent to:$emailAddr,".implode("; ", $this->bcc)." Subject:".$emailMsg->getSubject());
}

private function processTemplate($templateFile, $params, $user) {

	$tFile = DEF_DIR_EMAILTEMPLATES."/$templateFile";
	if (file_exists($tFile)) { $tplcontents = file_get_contents($tFile); }
	else { $this->logger->logError("template file not found:$tFile,$params"); return null; }
	$idx = strpos($tplcontents, "\n");
	$subject = substr($tplcontents, 0, $idx);
	$msgbody = substr($tplcontents, $idx+1);

	$commonparams = array(
		"%INC_APPURL%" => DEF_APPURL,
		"%INC_SITENAME%" => DEF_SITENAME,
		"%INC_USERNAME%" => $this->getCurrUsername($user),
		"%INC_USERID" => $this->getCurrUserid($user),
		"%INC_USER_DISPLAYNAME" => $this->getCurrUserDisplayname($user)
	);

	$params = array_merge($params, $commonparams);

	$searchArr = array_keys($params);
	$replaceArr = array_values($params);

	$content = file_get_contents(DEF_DIR_EMAILTEMPLATES."/header.tpl.html");
	$content .= $msgbody;
	$content .= file_get_contents(DEF_DIR_EMAILTEMPLATES."/footer.tpl.html");

	$subject = str_replace($searchArr, $replaceArr, $subject);
	$content = str_replace($searchArr, $replaceArr, $content);

	return new EmailMessage($subject, $content);
}

function getCurrUsername($user) {
	if ($user) { return $user->username; }
	else { return ""; }
}

function getCurrUserid($user) {
	if ($user) { return $user->id; }
	else { return ""; }
}

function getCurrUserEmail($user) {
	if ($user) { return $user->email; }
	else { return false; }
}

function getCurrUserDisplayName($user) {
	if ($user) {
		if (!$user->name) { return $this->getCurrUsername($user); }
		else return $user->name;
	}
	else { return ""; }
}

}

class EmailMessage {
	var $subject;
	var $msgbody;

	function EmailMessage($subject, $msgbody) {
		$this->subject = $subject;
		$this->msgbody = $msgbody;
	}

	function getSubject() { return $this->subject; }
	function getMsgbody() { return $this->msgbody; }
}

?>
