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

    $poid = isset($poid) && trim($poid) != "" ? intval($poid) : false;
    if(!$poid){ $error['missing_poid'] = "Not able to get PO referance"; }

    $freightamt = isset($freightamt) && trim($freightamt) != "" ? $freightamt : false;
    $transportsel = isset($transportsel) && trim($transportsel) != "" ? $transportsel : false;
    $gstsel = isset($gstsel) && trim($gstsel) != "" ? $gstsel : false;
    
    $object_gst = $dbl->getGSTbyid($gstsel);
    
    $fright_taxableAmt = round($freightamt,3) / (1 + round($object_gst->rate,3));
    
    $fright_GST = round($freightamt,3) - round($fright_taxableAmt,3);
    
    if(count($error) == 0){
       $poitem_id = $dbl->insertPOFright($poid,$freightamt,$transportsel,$gstsel,round($fright_taxableAmt,3),$fright_GST);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'po/additem/poid='.$poid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "po/additem/poid=".$poid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;