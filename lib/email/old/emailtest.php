<?php

require_once "../../../it_config.php";
require_once "lib/email/EmailSender.php";

$emailSender = new EmailSender();
$emailSender->sendEmail("unmesh@gmail.com", "signupvalidate.tpl.html", array("%INC_VKEY%" => "234sa09udsofoiwqeurosifasf="));

?>
