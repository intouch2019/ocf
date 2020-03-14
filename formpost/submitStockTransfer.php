<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$sup_store_id = $user->dcid;
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {


    $transferid = isset($transferid) && intval($transferid) > 0 ? $transferid : false;
    if (!$transferid) {
        $error["missing_stockTransfer"] = "Not able to get Stock Transfer Id.";
    }

    if ($itemcount == 0) {
        $error["items_not_added"] = "please add atleast 1 item to Transfer";
    }
    
    $totalQty = 0;
    $totalValue = 0;
    $stockTransferStatus = StockTransferStatus::AwaitingIn;
    $StockDiaryReason = StockDiaryReason::StockTransfer;
    $obj_stocktransferitems = $dbl->getStockTransferItems($transferid);
    foreach ($obj_stocktransferitems as $transferitems) {
        $totalQty = $totalQty + $transferitems->qty;
        $totalValue = $totalValue + $transferitems->value * $transferitems->qty;
    }

    if (count($error) == 0) {
        $transfer_id = $dbl->saveStockTransfer($transferid, $stockTransferStatus, $StockDiaryReason, $totalQty, $totalValue, $userid);
        $newStatus = POAllocationStatus::AwaitingIn;
        $result = $dbl->updatePOAllocationStatus($newStatus, $transferid);

        // if(isset($transfer_id)){
        //     $fromname =$transfer_id->fromloc; 
        //     $stateid = 0;
        //     if($transfer_id->from_location_type == LocationType::DC){
        //          $objdc = $dbl->getDCInfo($transfer_id->from_location_id); 
        //          $stateid = $objdc->state; 
        //     }else{
        //         $crdc = $dbl->getCRInfoById($transfer_id->from_location_id);
        //         $stateid = $crdc->state; 
        //     }
        
        //  $stcnum = "STC-".$fromname."/".$dbl->getActiveFinancialYear()."-".$dbl->fetchNextChallanNumber($stateid);
        //  $dbl->insertStockTransferChallan($transferid, $stcnum, StockTransferChallanStatus::BeingCreated, $userid,$stateid);
        // }
    
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'stocktransfer/additem/transferid=' . $transferid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = "stocktransfer/stockstatus=" . $stockTransferStatus;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
