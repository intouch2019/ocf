<?php
require_once("../it_config.php");
require_once("session_check.php");
require_once("lib/codes/clsCodes.php");
require_once ("lib/core/Constants.php");
require_once "lib/db/DBConn.php";
require_once("lib/db/DBLogic.php");

extract($_POST);
$errors=array();
$db = new DBConn();
$dbl = new DBLogic();
$clsLogger = new clsLogger();
try {
	$storecode=trim($storecode);
	$_SESSION['form_storecode']=$storecode;
	$password=urldecode($password);
	if (!$storecode) { $errors['storecode']='Enter Username'; }
	if (!$password) { $errors['password']='Enter Password'; }
	if (count($errors) == 0) {
            $clsCodes = new clsCodes();
            $codeInfo = $clsCodes->isAuthentic($storecode, $password);
	    //echo 'Ishan'.$codeInfo;
            //return;
            //exit;
            if (!$codeInfo) {
                    $errors['password']='Incorrect Username or Password';
            }
            $_SESSION['currStore'] = $codeInfo;
            $clsLogger->logInfo("Login:$storecode");
	}
} catch (Exception $xcp) {
	$clsLogger->logError("Failed to login $storecode:".$xcp->getMessage());
	$errors['status']="There was a problem processing your request. Please try again later";
}
if (count($errors) > 0) {
	$_SESSION['form_errors'] = $errors;
} else {
    unset($_SESSION['form_errors']);
}
session_write_close();
header("Location: ".DEF_SITEURL);
exit;

?>
