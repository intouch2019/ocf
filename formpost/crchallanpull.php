<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";

$error = array();
extract($_POST);
// print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {


    $challanid = isset($challanid) && intval($challanid) > 0 ? $challanid : false;
    if (!$challanid) {
        $error["missing_challan"] = "Not able to get Challan Id.";
    }
    $transferid = isset($transferid) && intval($transferid) > 0 ? $transferid : false;
    if (!$transferid) {
        $error["missing_stockTransfer"] = "Not able to get Stock Transfer Id.";
    }
    
    $totalQty = 0;
    $totalValue = 0;
    $stockTransferChallanStatus = StockTransferChallanStatus::Completed;
    $StockDiaryReason = StockDiaryReason::ChallanIn;
    
    if (count($error) == 0) {
        $transfer_id = $dbl->pullChallan($challanid, $stockTransferChallanStatus, $StockDiaryReason, $userid,$transferid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'cr/challan/pull/challanid=' . $challanid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = "challans/in/challanstatus=" . $stockTransferChallanStatus;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
