<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";

$error = array();
extract($_POST);
print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic(); 
try {


    $cnid = isset($cnid) && intval($cnid) > 0 ? $cnid : false;
    if (!$cnid) {
        $error["missing_cn"] = "Not able to get CN Id.";
    }

    if ($itemcount == 0) {

        $error["items_not_added"] = "please add atleast 1 item";
    }
    $totalQty = 0;
    $totalValue = 0;
    $cnStatus = GRNStatus::Created;
    $StockDiaryReason = StockDiaryReason::CreditNote;
    $obj_cn = $dbl->getCNDetails($cnid); // dcid
    $obj_cnitems = $dbl->getCNItems($cnid);
    foreach ($obj_cnitems as $cnitems){
        $totalQty =  $totalQty + $cnitems->qty;
        $totalValue = $totalValue + $cnitems->total;
    }
//    $ponum = "SA" . $dbl->fetchNextPONumber();
    //print_r($error);


    //$obj_grn = $dbl->getPODetails($pid);
    //$objDirector = $dbl->getUserInfoByType(UserType::Director);
    if (count($error) == 0) {
        $grn_id = $dbl->saveCN($cnid,$cnStatus,$totalQty,$totalValue,$StockDiaryReason);
        //echo $grn_id;
        
    }

    /*if (count($error) == 0) {
        //if ($objpo->po_status == $postatus) {
        if (isset($objDirector->email) && trim($objDirector->email) != "") {
            $arr_to = explode(",", $objDirector->email);

            //$arr_to = $objpo->email;
            foreach ($arr_to as $to) {
                //echo $to;
                $subject = "New Purchase Order No " . $obj_po->pono . " is created. Waiting For Approval"; //." Date ". ddmmyy_date($objpo->submitdate);
                $body = '<p>New Purchasse Order No ' . $obj_po->pono . ' is Created. Kindly Login and Approve or Reject This PO.</p>
                     <p>Total Quantity :' . $obj_po->totalqty . ' &nbsp;&nbsp;&nbsp;&nbsp; Total Amount :' . $obj_po->totalvalue . '</p>
                                <p>Thanks & Regards,</p>
                                <p>Sarotam</p>
                                <p><b>Note : This is computer generated email do not reply. Kindly reply to '.$user->email.' </b></p>';

                $emailHelper = new EmailHelper();
                $success = $emailHelper->send(array($to), $subject, $body);
                //$dbl->updateEmailStatus($objpo->id);
            }
        }
    }*/
    // }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'creditnote/additem/cnid=' . $cnid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = "creditnote/status=".$cnStatus;
    //$redirect = "po/awaiting/approvals";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
