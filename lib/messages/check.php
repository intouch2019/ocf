<?php

require_once "../../../it_config.php";

$list = array(
	"clsSignupMessage",
	"clsRootMessage",
	"clsFanMessage",
	"clsOwnerMessage"
	);

foreach ($list as $cls) {
	require_once "lib/messages/$cls.php";
	$msg = new $cls("dummy");
	print "$cls<br />=========================<br />";
	$msgcodes = $msg->getMessageCodes("en");
	foreach ($msgcodes as $code=>$msgText) {
		$len = strlen($msgText);
		if ($len >= 140) print "****** ";
		print "$len, $code, $msgText<br />";
	}
}

?>
