<?php

static $s_CmdProcessors = array(
"root" => array("lib/cmdprocs/clsRootProcessor.php", "clsRootProcessor"),

"signup" => array("lib/cmdprocs/clsSignupProcessor.php", "clsSignupProcessor"),

"owner" => array("lib/cmdprocs/clsOwnerProcessor.php", "clsOwnerProcessor"),
"stats" => array("lib/cmdprocs/clsOwnerProcessor.php", "clsOwnerProcessor"),
"publish" => array("lib/cmdprocs/clsOwnerProcessor.php", "clsOwnerProcessor"),

"help" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),
"add" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),
"addd" => array("lib/cmdprocs/clsAddProcessor.php", "clsAddProcessor"),
"remove" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),
"mystores" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),
"mypoints" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),
"myinfo" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),
"review" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),
"redeem" => array("lib/cmdprocs/clsFanProcessor.php", "clsFanProcessor"),

"default" => array("lib/cmdprocs/clsGeneralProcessor.php", "clsGeneralProcessor"),
"info" => array("lib/cmdprocs/clsGeneralProcessor.php", "clsGeneralProcessor"),
"trial" => array("lib/cmdprocs/clsGeneralProcessor.php", "clsGeneralProcessor"),
);

abstract class clsCmdProcessor {

	var $command;
	var $commandText;

	protected function __construct($command, $commandText) {
		$this->command = $command;
		$this->commandText = $commandText;
	}

	public static function getCmdProcessor($cmd) {
		global $s_CmdProcessors;
		list($firsttoken,$commandText) = split(" ", $cmd, 2);
		$firsttoken = strtolower($firsttoken);
		$arr = $s_CmdProcessors[$firsttoken];
		if (!$arr) {
			$arr = $s_CmdProcessors['default'];
		}
		require_once $arr[0]; // include the class file
		$proc = new $arr[1]($firsttoken, $commandText); // instantiate the class
		return $proc;
	}

	public function getCommand() {
		return $this->command;
	}

	public function getCommandText() {
		return $this->commandText;
	}

	protected abstract function run($incoming, $user);
}

?>
