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
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {


    $pid = isset($pid) && intval($pid) > 0 ? $pid : false;
    if (!$pid) {
        $error["missing_po"] = "Not able to get PO Id.";
    }

    if ($itemcount == 0) {

        $error["items_not_added"] = "please add atleast 1 item";
    }

//    if (isset($remarks) && trim($remarks) != "") {
//        $remarks = preg_replace("/\r\n|\r|\n/",'<br/>',$remarks);
//    } else {
//        $error["remarks_not_added"] = "please write remarks for this PO";
//    }
//    
//    
////    if (strpos($remarks, "\n") !== FALSE) {
////        echo 'New line break found';
////    } else {
////        echo 'not found';
////    }
//    if (isset($deliveryofitems) && trim($deliveryofitems) != "") {
//        $deliveryofitems = $deliveryofitems;
//    } else {
//        $error["Deleiverofitemsnote_not_added"] = "please write Delivery of items note for this PO";
//    }
//
//    if (isset($headernote) && trim($headernote) != "") {
//        $headernote = $headernote;
//    } else {
//        $error["headernote_not_added"] = "please write Header Note to Supplier note for this PO";
//    }



    if (isset($freightamt)) {
        $freightamt = $freightamt;
    } else {
        $freightamt = 0;
    }//freightgst
    if (isset($freightgst)) {
        $freightgst = $freightgst;
    } else {
        $freightgst = 0;
    }//totalfreight
    if (isset($totalfreight)) {
        $totalfreight = $totalfreight;
    } else {
        $totalfreight = 0;
    }//gstsel
    if (isset($gstsel)) {
        $gstsel = $gstsel;
    } else {
        $gstsel = 0;
    }
    if (isset($transportsel)) {
        $transportsel = $transportsel;
    } else {
        $transportsel = "null";
    }

    if (isset($supoffers)) {
        $supoffers = $supoffers;
    } else {
        $error["supplyersOffers_not_select"] = "please select one Suppliers Offers";
    }

    if (isset($offerref)) {
        $offerref = $offerref;
    } else {
        $offerref = "";
    }

    if (isset($datepicker)) {
        $datepicker = $datepicker;
        $dateArr = explode("/", $datepicker);
        if(count($dateArr) == 3){
            $month = $dateArr[0];
            $day = $dateArr[1];
            $year = $dateArr[2];
        }
//        list($month, $day, $year) = split('[/.-]', $datepicker);
        $datepicker = $day.'/'.$month.'/'.$year;
    } else {
        $error["Picking_Date_Not_Found"] = "please select Picking Date";
    }

    if (isset($nodays)) {
        $nodays = $nodays;
    } else {
        $error["Picking_Date_Not_Found"] = "please select Picking Date";
    }

    if (isset($refval)) {
        $refval = $refval;
    } else {
        $refval = "";
    }

    if (isset($enddate)) {
        $enddate = $enddate;
    } else {
        $enddate = "";
    }

    $sno = 1;    
    if (isset($remark1)) {
        $remark1 = $sno.". With ref to your Offer Reference " . $offerref . " Dated " . $datepicker . " we are pleased to issue this Purchase Order for the supply of following Items<br/>";
        $sno++;
    } else {
        $remark1 = "";
    }

    if (isset($remark2)) {
        $remark2 = $sno.". ".$remark2 . '<br/>';
        $sno++;
    } else {
        $remark2 = "";
    }

    if (isset($remark3)) {
        $remark3 = $sno.". ".$remark3 . '<br/>';
        $sno++;
    } else {
        $remark3 = "";
    }

    if (isset($remark4)) {
        $remark4 = $sno.". ".$remark4;
        $sno++;
    } else {
        $remark4 = "";
    }

    $r2no = 2;
    $lsno = 1;
    
    if (isset($li1)) {
        $li1 = "    ".$r2no.".".$lsno.". ".$li1 . '<br/>';
        $lsno++;
    } else {
        $li1 = "";
    }

    if (isset($li2)) {
        $li2 = "    ".$r2no.".".$lsno.". ".$li2 . '<br/>';
        $lsno++;
    } else {
        $li2 = "";
    }

    if (isset($li3)) {
        $li3 = "    ".$r2no.".".$lsno.". ".$li3 . '<br/>';
        $lsno++;
    } else {
        $li3 = "";
    }

    if (isset($li4)) {
        $li4 = "    ".$r2no.".".$lsno.". ".$li4 . '<br/>';
        $lsno++;
    } else {
        $li4 = "";
    }

    if (isset($li5)) {
        $li5 = "    ".$r2no.".".$lsno.". ".$li5 . '<br/>';
        $lsno++;
    } else {
        $li5 = "";
    }

    $po_remark = $remark1 . $remark2 . $li1 . $li2 . $li3 . $li4 . $li5 . $remark3 . $remark4;
    //echo $po_remark;

    $postatus = POStatus::Created;
//    $ponum = "SA" . $dbl->fetchNextPONumber();
    //print_r($error);
    $obj_po = $dbl->getPODetails($pid);
    $objDirector = $dbl->getUserInfoByType(UserType::Director);
    if (count($error) == 0) {
        $po_id = $dbl->savePO($pid, $postatus, $userid, $freightamt, $freightgst, $totalfreight, $gstsel, $transportsel, $supoffers, $offerref, $datepicker, $nodays, $po_remark);
    }

    if (count($error) == 0) {
        //if ($objpo->po_status == $postatus) {
        if (isset($objDirector->email) && trim($objDirector->email) != "") {
            $arr_to = explode(",", $objDirector->email);

            //$arr_to = $objpo->email;
            foreach ($arr_to as $to) {
                //echo $to;
                $totalQty = round($obj_po->totalqty, 4, PHP_ROUND_HALF_UP);
                $roundTotalQty = sprintf("%.4f", $totalQty);
                $totalVal = round($obj_po->totalvalue, 2, PHP_ROUND_HALF_UP);
                $roundTotalVal = sprintf("%.2f", $totalVal);
                
                $subject = "New Purchase Order No " . $obj_po->pono . " is created. Waiting For Approval"; //." Date ". ddmmyy_date($objpo->submitdate);
                $body = '<p>New Purchasse Order No ' . $obj_po->pono . ' is Created. Kindly Login and Approve or Reject This PO.</p>
                     <p>Total Quantity :' . $roundTotalQty . ' &nbsp;&nbsp;&nbsp;&nbsp; Total Amount :' . $roundTotalVal . '</p>
                                <p>Thanks & Regards,</p>
                                <p>Sarotam</p>
                                <p><b>Note : This is computer generated email do not reply. Kindly reply to '.$user->email.' </b></p>';

                $emailHelper = new EmailHelper();
                $success = $emailHelper->send(array($to), $subject, $body);
                //$dbl->updateEmailStatus($objpo->id);
            }
        }
    }
    // }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'po/additem/poid=' . $pid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = "po/postatus=".$postatus;
    //$redirect = "po/awaiting/approvals";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
