<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $billid = isset($billid) && trim($billid) != "" ? intval($billid) : false;
    if(!$billid){ $error['missing_billid'] = "Missing Gate Entry Id"; }

    $status = SupplierBillStatus::Submit;
    
    if(count($error) == 0){
       $id = $dbl->suppBillSubmit($billid,$status,$userid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = "supplier/bill/item/entry/billid=".$billid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = "supplier/bills/status=".$status;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;