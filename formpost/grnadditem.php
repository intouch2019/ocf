<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
//return;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {

    $poid = isset($poid) && trim($poid) != "" ? intval($poid) : false;
    if (!$poid) {
        $error['missing_poid'] = "Not able to get PO referance";
    }

    $grnid = isset($grnid) && trim($grnid) != "" ? intval($grnid) : false;
    if (!$grnid) {
        $error['missing_grnid'] = "Not able to get GRN referance";
    }

    $polineid = isset($poitem) && trim($poitem) != "" ? intval($poitem) : false;
    if (!$polineid) {
        $error['missing_polineid'] = "Not able to get PO Item";
    }
    //echo $polineid;
    if(isset($check1)){
        $receiveIn = $check1;
    }
    
    if(isset($check2)){
        $receiveIn = $check2;
    }
    
    $arr = explode("::", $poitem);
    $polineid = $arr[0];
    $prodid = $arr[2];

//    $prodid = isset($poitem) && trim($prodid) != "" ? intval($prodid) : false;
//    if (!$prodid) {
//        $error['missing_product'] = "Not able to get Product";
//    }
    

    $alias = isset($alias) && trim($alias) != "" ? $alias : false;
    $length = isset($length) && trim($length) != "" ? $length : false;
    $colorsel = isset($colorsel) && trim($colorsel) != "" ? $colorsel : false;
    $brandsel = isset($brandsel) && trim($brandsel) != "" ? $brandsel : false;
    $manfsel = isset($manfsel) && trim($manfsel) != "" ? $manfsel : false;

    $qty = isset($qty) && trim($qty) != "" ? trim($qty) : false;
    if (!$qty) {
        $qty  = isset($qty2) && trim($qty2) != "" ? trim($qty2) : false;
        if(!$qty){
        $error['missing_qty'] = "Enter qty to order";
        }
    }

    $mtqty = isset($mtqty) && trim($mtqty) != "" ? trim($mtqty) : false;
    $uom = isset($uom) && trim($uom) != "" ? trim($uom) : false;
    
    $pieces = isset($pieces) && trim($pieces) != "" ? trim($pieces) : false;
    if(!$pieces){
        $pieces = isset($pieces2) && trim($pieces2) != "" ? trim($pieces2) : false;
    }
    
    $rate = isset($rate) && trim($rate) != "" ? trim($rate) : false;
    if (!$rate) {
        $rate = isset($rate2) && trim($rate2) != "" ? trim($rate2) : false;
        if(!$rate){
        $error['missing_rate'] = "Enter rate";
        }
    }

    $lcrate = isset($lcrate) && trim($lcrate) != "" ? trim($lcrate) : false;
    if(!$lcrate){
        $lcrate = isset($lcrate2) && trim($lcrate2) != "" ? trim($lcrate2) : false;
    }
    $cgstpct = 0.09;
    $cgstval = isset($cgst) && trim($cgst) != "" ? trim($cgst) : false;
    $sgstpct = 0.09;
    $sgstval = isset($sgst) && trim($sgst) != "" ? trim($sgst) : false;

    $totalrate = isset($totalrate) && trim($totalrate) != "" ? trim($totalrate) : false;
    $totalvalue = isset($value) && trim($value) != "" ? trim($value) : false;

    $status = GRNItemStatus::Open;
    echo "statuss ".$status;

    $objgrn = $dbl->getGRNDetails($grnid);
    $stateid = 0;
    $dccode = $objgrn->dcid;
    if (!$dccode) {
        $error['missing_dccode'] = "Select DC ";
    } else {
        $objdc = $dbl->getDCInfo($dccode);
        if ($objdc == null) {
            $error['missing_dccode'] = "DC does not exist";
        } else {
            $stateid = $objdc->state;
        }
    }

    $statenumber = 0;
    if ($stateid == 0) {
        $error['missing_dccode'] = "Not able to find DC state";
    } else {
        $objstate = $dbl->getStateInfo($stateid);
        if ($objstate != null) {
            $statenumber = $objstate->TIN;
        }
    }
    $batchcode = $statenumber . $dbl->getActiveFinancialYear() . $dbl->fetchNextBatchNumber($stateid);

    if (count($error) == 0) {
        $grnitem_id = $dbl->insertGRNItem($poid, $prodid, $grnid, $polineid, $mtqty,$qty, $rate, $length, $colorsel, $brandsel, $manfsel, 
               $pieces, $lcrate, $cgstpct, $cgstval, $sgstpct, $sgstval, $totalrate, $totalvalue, $status, $batchcode, $alias,$receiveIn);

        //$barcode=getEANCode($poitem_id);
        //$dbl->AddSku($poitem_id,$barcode);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'grn/additem/grnid=' . $grnid.'/uom='.$uom;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "grn/additem/grnid=" . $grnid."/uom=".$uom;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
