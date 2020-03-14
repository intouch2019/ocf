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

    $suppsel = isset($suppsel) && trim($suppsel) != "" ? intval($suppsel) : false;
    if(!$suppsel){ $error['missing_supplier'] = "Select Supplier"; }

//    $suppcontractno = isset($suppcontractno) && trim($suppcontractno) != "" ? trim($suppcontractno) : false;
//    if(!$suppcontractno){ $error['missing_suppcontractno'] = "Enter Supplier Contract No"; }

    $paymentterms = isset($paymentterms) && trim($paymentterms) != "" ? intval($paymentterms) : false;
    if(!$paymentterms){ $error['missing_paymentterms'] = "Select Payment Terms"; }

    $deliveryterms = isset($deliveryterms) && trim($deliveryterms) != "" ? intval($deliveryterms) : false;
    if(!$deliveryterms){ $error['missing_deliveryterms'] = "Select Delivery Terms"; }

    $transitinsurance = isset($transitinsurance) && trim($transitinsurance) != "" ? intval($transitinsurance) : false;
    if(!$transitinsurance){ $error['missing_transitinsurance'] = "Select Transit Insurance Type"; }    
    
    $uom = isset($uom) && trim($uom) != "" ? trim($uom) : false;
//    $buyercode = isset($buyercode) && trim($buyercode) != "" ? trim($buyercode) : false;
//    if(!$buyercode){ $error['missing_buyercode'] = "Enter Buyer Code "; }
//
//    $buyername = isset($buyername) && trim($buyername) != "" ? trim($buyername) : false;
//    if(!$buyername){ $error['missing_buyername'] = "Enter Buyer Name "; }
    
//    $referance1 = isset($referance1) && trim($referance1) != "" ? trim($referance1) : false;
//
//    $referance2 = isset($referance2) && trim($referance2) != "" ? trim($referance2) : false;

    $stateid = 0;
    $dccode = isset($dccode) && trim($dccode) != "" ? trim($dccode) : false;
    if(!$dccode){ $error['missing_dccode'] = "Select DC "; }else{
        $objdc = $dbl->getDCInfo($dccode);
        if($objdc == null){
            $error['missing_dccode'] = "DC does not exist";
        }else{
            $stateid = $objdc->state;
        }
    }
    
    $statenumber = 0;
    if($stateid == 0){ $error['missing_dccode'] = "Not able to find DC state";}else{
        $objstate = $dbl->getStateInfo($stateid);
        if($objstate != null){
            $statenumber = $objstate->TIN;
        }
    }
//    $deliveryname = isset($deliveryname) && trim($deliveryname) != "" ? trim($deliveryname) : false;
//    if(!$deliveryname){ $error['missing_deliveryname'] = "Enter Delivery Name "; }
    $postatus = POStatus::Open;
    $ponum = "PO-".$statenumber."/".$dbl->getActiveFinancialYear()."-".$dbl->fetchNextPONumber($stateid);
    
    if(count($error) == 0){
       $po_id = $dbl->insertPO($suppsel,$paymentterms,$deliveryterms,$transitinsurance,$dccode,$postatus,$ponum,$userid,$uom);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'po/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "po/additem/poid=".$po_id."/uom=".$uom;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;