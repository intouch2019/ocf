<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once("lib/codes/clsCodes.php");
require_once ("lib/core/Constants.php");
require_once "lib/db/DBConn.php";
require_once("lib/db/DBLogic.php");

$clsCodes = new clsCodes();
$errors=array();

$newCodeInfo=null;
$id=isset($_GET['id']) ? $_GET['id'] : false;
if ($id) {
  $newCodeInfo = $clsCodes->getUserById($id);
}
if (!$newCodeInfo) {
  $errors['status'] = "Invalid id:$id";
  $_SESSION['form_errors'] = $errors;
  session_write_close();
  header("Location: ".DEF_SITEURL);
  exit;
}

$_SESSION['hoCodeInfo'] = $_SESSION['currStore'];
$_SESSION['currStore'] = $newCodeInfo;
$_SESSION['form_storecode']=$newCodeInfo->username;
header("Location: ".DEF_SITEURL);
exit;

