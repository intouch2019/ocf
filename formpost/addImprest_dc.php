<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$error = array();
$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;

$success = "";
$user = getCurrStore();
$db = new DBConn();
$dbl = new DBLogic();
$currStore = getCurrStore();
$dcid = $currStore->dcid;

try{
    $impreason = isset($impreason) && trim($impreason) != "" ? intval($impreason) : false;
    if (!$impreason) {
        $error['missing_reason'] = "Please select reason.";
    }
    if(trim($amount) == "" || trim($description) == ""){
        $error['missing_parameters'] = "Please Enter All Required Fields";
    }
    
    $impledger = trim($impledger) != "" ? intval($impledger) : false;
    if ( !$impledger )
     {
        $error['missing_ledger'] = "Please select ledger.";
    } 
    
    $prevImpObj = $dbl->getImprestDetailsByDcId($dcid);
//     print_r($prevImpObj);
//    return;
    if($prevImpObj != null){
        $prevBal = $prevImpObj->balance;
    }else{
        $insert = $dbl->insertDefaultImprestBalanceForNewDC($dcid);
        $prevBal = 0;
    }
//    print_r($prevBal);
//    return;
    
    if($amount > $prevBal && $impreason == ImprestReason::Out){
        $error['insufficient_balance'] = "Available amount is $prevBal Rs. only.";
    }
    
    
    
    if(count($error) == 0){
        if($impreason == ImprestReason::In){
            $newBal = $prevBal + $amount;
        }else if($impreason == ImprestReason::Out){
            $newBal = $prevBal - $amount;
        }
//        print_r($newBal);
//        return;
        $voucher_num = $dbl->getVoucherNum();
        $userid = $user->id;
        $dcdetails = $dbl->getDCDetailsByUserId($userid);
      
        $stateobj = $dbl->getStateInfo($dcdetails->state);
        $stateTin = $stateobj->TIN;
        $dcid = $dcdetails->id;
        $voucher_no = strtoupper("IM".$dbl->getDCCode($dcid) . "/" . $dbl->getActiveFinancialYear() . "-" . $stateTin . "/" . $voucher_num);
        $obj = $dbl->insertIntoImprestDetails_Dc($amount,$description,$voucher_no, $userid, $dcid, $prevBal, $newBal, $impreason,$impledger);
      
        if($obj != 0){
            $result = $dbl->updateVoucherNum();
//            $objImpId = $dbl->getImprestHeadersIdByDCId($dcid);
            $updateCurrBal = $dbl->updateCurrBalanceByDcId($dcid,$newBal);
//              return;
            $success = "Data Entered Successfully.";
        }else {
           $error['insert_error'] = "Unable to insert data into database.";
       }

    }


} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'imprest/register/dc';
} else {

    
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'imprest/register/dc';
}
session_write_close();
 header("Location: " . DEF_SITEURL . $redirect);
exit;
