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

    $pono = isset($pono) && trim($pono) != "" ? trim($pono) : false;
    if(!$pono){ $error["missing_pono"] = "Please enter PO number"; }
    
    $poid = null;
    $suppid = null;
    $objpo = $dbl->getPODetailsByPONo($pono);
    if($objpo == null){ $error["missing_pono"] = "PO number entered does not exist"; }else{
        if($objpo->po_status != POStatus::Submitted){
            $error["missing_pono"] = "PO number entered is not submitted to the supplier";
        }else{
            $poid = $objpo->id;
            $suppid = $objpo->supplier_id;
        }
    }
    
    $sinvno = isset($sinvno) && trim($sinvno) != "" ? trim($sinvno) : false;
    if(!$sinvno){ $error["missing_sinvno"] = "Please enter Supplier Invoice number"; }
    
    $sinvdate = isset($sinvdate) && trim($sinvdate) != "" ? yymmdd($sinvdate) : false;
    if(!$sinvdate){ $error["missing_sinvdate"] = "Please enter Supplier Invoice date"; }
    
    /*partial implimentation*/
    $grndate = isset($grndate) && trim($grndate) != "" ? yymmdd($grndate) : false;
    if(!$grndate){ $error["missing_grndate"] = "Please enter GRN  date"; }
    
     $uom = isset($uom) && trim($uom) != "" ? trim($uom) : false;
    
    
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

    $grnstatus = GRNStatus::Open;
    $grnnum = "GRN-".$statenumber."/".$dbl->getActiveFinancialYear()."-".$dbl->fetchNextGRNNumber($stateid);
    
    if(count($error) == 0){
       $grnid = $dbl->insertGRN($dccode, $poid, $suppid, $sinvno, $sinvdate, $grnnum, $grnstatus, $userid,$grndate,$uom);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'grn/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "grn/additem/grnid=".$grnid."/uom=".$uom;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect); 
exit;