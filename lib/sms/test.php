<?php

require_once "../../../it_config.php";
require_once "clsSMSHelper.php";

$smsHelper = new clsSMSHelper();

$retval = $smsHelper->sendOne("919881064419", "This is a test message for Unmesh");
print "[$retval]";

?>
