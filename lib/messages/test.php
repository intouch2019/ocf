<?php

require_once "../../../it_config.php";
require_once "clsFanMessage.php";

print clsFanMessage::reviewInvalidCommand()->getMessage("en");
print clsFanMessage::reviewIncorrectCheckinCode("1000")->getMessage("en");
print clsFanMessage::reviewNotAuthorized("1000")->getMessage("en");
print clsFanMessage::reviewSuccessful("jlpune")->getMessage("en");

?>
