<?php

require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";
require_once "lib/talk/clsTalk.php";
require_once "lib/talk/clsBaseTalk.php";

class clsTalkback extends dbobject {

	public function processCmd($storecode, $msg) {
		$msg = strtolower(trim(preg_replace('/\s+/', ' ', $msg)));
		list($cmd, $cmdArgs) = explode(" ", $msg);
		$clsName = "cls_".$storecode."Talk";
		if (@include("lib/talk/$clsName.php")) {
			require_once "lib/talk/$clsName.php";
			$clsStoreTalk = new $clsName();
		} else {
			$clsStoreTalk = new clsBaseTalk($storecode);
		}
		$methodname = "cmd_$cmd";
		if (method_exists($clsStoreTalk,$methodname)) {
			return $clsStoreTalk->$methodname($cmdArgs);
		} else {
			return "Command [$cmd] not supported.";
		}
	}

	public function presence($username) {
		return "Welcome $username to IntouchTalk";
	}
}

?>
