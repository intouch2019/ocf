<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = "stocktransfer";
$_SESSION['form_post'] = $_POST;
$success = "";

$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();


$fromLoctype = 1;
$toLoctype = 2;
$transferdate = date("Y-m-d");
$dccode = 1;
$crcode = $user->crid;
 
try {

    $fromLoctype = isset($fromLoctype) && trim($fromLoctype) != "" ? trim($fromLoctype) : false;
    if ($fromLoctype < 0) {
        $error["missing_pono"] = "Please select from location type";
    }

    $toLoctype = isset($toLoctype) && trim($toLoctype) != "" ? trim($toLoctype) : false;
    if ($toLoctype < 0) {
        $error["missing_pono"] = "Please select to location type";
    }

    $poid = null;
    $suppid = null;

    $stateid = 0;
    $dcccode = "";
    
    if ($fromLoctype == LocationType::DC && $toLoctype == LocationType::CR) {
        $dccode = isset($dccode) && trim($dccode) != "" ? trim($dccode) : false;
        $crcode = isset($crcode) && trim($crcode) != "" ? trim($crcode) : false;
    } else if ($fromLoctype == LocationType::DC && $toLoctype == LocationType::DC) {
        $dccode = isset($dccode1) && trim($dccode1) != "" ? trim($dccode1) : false;
        $crcode = isset($crcode1) && trim($crcode1) != "" ? trim($crcode1) : false;
        if($dccode == $crcode){
            $error["same_DC"] = "You Cannot transfer Stock within Same DC's.";
        }
    } else if ($fromLoctype == LocationType::CR && $toLoctype == LocationType::DC) {
        $dccode = isset($dccode2) && trim($dccode2) != "" ? trim($dccode2) : false;
        $crcode = isset($crcode2) && trim($crcode2) != "" ? trim($crcode2) : false;
    } else if ($fromLoctype == LocationType::CR && $toLoctype == LocationType::CR) {
        $dccode = isset($dccode3) && trim($dccode3) != "" ? trim($dccode3) : false;
        $crcode = isset($crcode3) && trim($crcode3) != "" ? trim($crcode3) : false;
        if($dccode == $crcode){
            $error["same_CR"] = "You Cannot transfer Stock within Same CR's.";
        }
    }

    $transferdate = isset($transferdate) && trim($transferdate) != "" ? yymmdd($transferdate) : false;
    if(!$transferdate){ $error["missing_transferdate"] = "Please Stock Transfer date"; }

    if ($fromLoctype == LocationType::DC) {
        $objdc = $dbl->getDCInfo($dccode);
        if ($objdc == null) {
            $error['missing_dccode'] = "DC does not exist";
        } else {
            $stateid = $objdc->state;
            $dcccode = $objdc->dc_name;
        }
    } else {
        $objdc = $dbl->getCRInfoById($dccode);
        if ($objdc == null) {
            $error['missing_cr_exist'] = "CR does not exist";
        } else {
            $stateid = $objdc->state;
            $dcccode = strtoupper($objdc->dispname);
        }
    }
    
    if($crcode < 0){
           $error['missing_cr'] = "please Select CR";
    }

    $crstateid = 0;


    $statenumber = 0;
    if ($stateid == 0) {
        $error['missing_dccode'] = "Not able to find state";
    } else {
        $objstate = $dbl->getStateInfo($stateid);
        if ($objstate != null) {
            $statenumber = $objstate->TIN;
        }
    }

    $stocktransferStatus = StockTransferStatus::BeingCreated;
    $stocknum = "PO-" . $dcccode . "/" . $dbl->getActiveFinancialYear() . "-" . $dbl->fetchNextstockTransferNumber($stateid);

    if (count($error) == 0) {
        $stocktransferid = $dbl->insertStockTransfer($fromLoctype, $toLoctype, $dccode, $crcode, $stocknum, $stocktransferStatus, $userid,$transferdate, $stateid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'stocktransfer/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "stocktransfer/additem/transferid=" . $stocktransferid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
