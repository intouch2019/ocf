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

    $polineid = isset($polineid) && trim($polineid) != "" ? intval($polineid) : false;
    if(!$polineid){ $error['missing_polineid'] = "Missing PO Line Id"; }

    $poid = isset($poid) && trim($poid) != "" ? intval($poid) : false;
    if(!$poid){ $error['missing_poid'] = "Missing PO Id"; }
    
    $prodid = isset($prodid) && trim($prodid) != "" ? intval($prodid) : false;
    if(!$prodid){ $error['missing_prodid'] = "Missing Product Id"; }
    
    $poqty = isset($poqty) && trim($poqty) != "" ? trim($poqty) : false;
    if(!$poqty){ $error['missing_poqty'] = "Missing PO Qty"; }

    $porate = isset($porate) && trim($porate) != "" ? trim($porate) : false;
    if(!$porate){ $error['missing_porate'] = "Missing PO Rate"; }

    $poexdate = isset($poexdate) && trim($poexdate) != "" ? yymmdd($poexdate) : false;
    if(!$poexdate){ $error['missing_poexdate'] = "Missing PO Expected Date"; }

    $receivedqty = isset($receivedqty) && trim($receivedqty) != "" ? trim($receivedqty) : false;
    if(!$receivedqty){ $error['missing_receivedqty'] = "Missing Received Qty"; }

    $receivedrate = isset($receivedrate) && trim($receivedrate) != "" ? trim($receivedrate) : false;
    if(!$receivedrate){ $error['missing_receivedrate'] = "Missing Received Rate"; }

    $receiveddate = isset($receiveddate) && trim($receiveddate) != "" ? yymmdd($receiveddate) : false;
    if(!$receiveddate){ $error['missing_receiveddate'] = "Missing Received Date"; }

    if(count($error) == 0){
       $id = $dbl->insertSupplierBillItem($billid,$polineid,$prodid,$poqty,$porate,$poexdate,$receivedqty,
               $receivedrate,$receiveddate,$poid,$userid);
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
    $redirect = "supplier/bill/item/entry/billid=".$billid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;