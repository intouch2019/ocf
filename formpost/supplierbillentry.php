<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $supplierid = 0;
    
    $gateentryid = isset($gateentryid) && trim($gateentryid) != "" ? intval($gateentryid) : false;
    if(!$gateentryid){ $error['missing_gate_entry'] = "Missing Gate Entry Id"; }else{
        $obj_gateentry = $dbl->getGateEntryDetails($gateentryid);
        if($obj_gateentry != NULL){
            $supplierid = $obj_gateentry->supplier_id;
        }
    }

    $poid = 0;
    $pono = isset($pono) && trim($pono) != "" ? trim($pono) : false;
    if(!$pono){ $error['missing_pono'] = "Enter PO No"; }else{
        $obj_po = $dbl->getPODetailsByPONo($pono);
        if($obj_po == NULL){
            $error['missing_pono'] = "Entered PO No does not exist";
        }else{
            if($obj_po->supplier_id != $obj_gateentry->supplier_id){
                $error['missing_supplier'] = "PO No entered does not belong to this supplier";
            }
            if($obj_po->po_status != POStatus::Submitted){
                $error['missing_pono'] = "Entered PO No is not yet submitted";
            }
            $poid = $obj_po->id;
        }
    }
    
    $billno = isset($billno) && trim($billno) != "" ? trim($billno) : false;
    if(!$billno){ $error['missing_billno'] = "Enter Bill No"; }

    $billdate = isset($billdate) && trim($billdate) != "" ? yymmdd($billdate) : false;
    if(!$billdate){ $error['missing_billdate'] = "Select Bill Date"; }
    
    $status = SupplierBillStatus::Open;
    
    //print_r($error);
    
    if(count($error) == 0){
       $supplierbillid = $dbl->insertSupplierBill($gateentryid,$supplierid,$poid,$billno,$billdate,$status,$userid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = "supplier/bill/entry/gateentryid=".$gateentryid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = "supplier/bill/item/entry/billid=".$supplierbillid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;