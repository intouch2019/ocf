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
    // $ewaybill = isset($ewaybill) ? $ewaybill : false;
    // if (!$ewaybill) {
    //     $error["missing_ewaybill"] = "Not able to get E Way Bill No.";
    // }
    $vehicleno = isset($vehicleno)  ? $vehicleno : false;
    if (!$vehicleno) {
        $error["missing_vehicle"] = "Not able to get Vehicle No.";
    }    
    $transferid = isset($transferid) && intval($transferid) > 0 ? $transferid : false;
    if (!$transferid) {
        $error["missing_stocktransfer"] = "Not able to get Stock Transfer Id.";
    }

    if ($itemcount == 0) {
        $error["items_not_added"] = "please add atleast 1 item to Challan";
    }
    $totalQty = 0;
    $totalValue = 0;
    $challanStatus = StockTransferChallanStatus::AwaitingIn;
    $StockDiaryReason = StockDiaryReason::ChallanOut;
    $obj_challanitems = $dbl->getChallanItems($challanid);
    foreach ($obj_challanitems as $challanitems) {
        $result = $dbl->checkSTCQtyInCurrStock($transferid,$challanitems->prodid,$challanitems->batchcode);
        if($result != null && $result != "" && $result->qty >= $challanitems->qty){
            $totalQty = $totalQty + $challanitems->qty;
            $totalValue = $totalValue + $challanitems->rate * $challanitems->qty;
        }else{
            $desc1 = isset($challanitems->desc_1) && trim($challanitems->desc_1) != "" ? " , ".$challanitems->desc_1." mm" : "";
            $desc2 = isset($challanitems->desc_2) && trim($challanitems->desc_2) != "" ? " x ".$challanitems->desc_2." mm" : "";
            $thickness = isset($challanitems->thickness) && trim($challanitems->thickness) != "" ? " , ".$challanitems->thickness." mm" : "";
            $itemname = $challanitems->prod.$desc1.$desc2.$thickness;
            $errorName = "qty_not_available$challanitems->batchcode";
            $error[$errorName] = "Quantity is not avaialable for $itemname in batch $challanitems->batchcode.";
        }
        
    }
    if (count($error) == 0) {
        $transfer_id = $dbl->submitChallan($challanid, $StockDiaryReason,$challanStatus, $totalQty, $totalValue, $userid, $transferid, "-",$vehicleno);
        $status = POAllocationStatus::Completed;
        $obj_po_alloc = $dbl->getPoAllocationDetailsByChallanId($challanid);
        $result = $dbl->updatePoAllocFullfilledQty($totalQty,$obj_po_alloc->id);
        // $updated_id = $dbl->updatePOAllocationStatus($status,$transferid);
        // $dbl->updatetockTransfer($transferid, StockTransferStatus::Completed, $userid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'challan/additem/transferid=' . $transferid .'/challan_id='.$challanid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = "challans/in/challanstatus=" . $challanStatus;
}
session_write_close();
 header("Location: " . DEF_SITEURL . $redirect);
exit;
