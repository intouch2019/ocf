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
$crid = $currStore->crid;
try{
    $impreason = isset($impreason) && trim($impreason) != "" ? intval($impreason) : false;
    if (!$impreason) {
        $error['missing_reason'] = "Please select reason.";
    }
    if(trim($amount) == "" || trim($description) == ""){
        $error['missing_parameters'] = "Please Enter All Required Fields";
    }
    $prevImpObj = $dbl->getImprestDetailsByCrId($crid);
    if($prevImpObj != null){
        $prevBal = $prevImpObj->balance;
    }else{
        $insert = $dbl->insertDefaultImprestBalanceForNewCR($crid);
        $prevBal = 0;
    }
    
    
    if($amount > $prevBal && $impreason == ImprestReason::Out){
        $error['insufficient_balance'] = "Available amount is $prevBal Rs. only.";
    }
    
    
    
    if(count($error) == 0){
        if($impreason == ImprestReason::In){
            $newBal = $prevBal + $amount;
        }else if($impreason == ImprestReason::Out){
            $newBal = $prevBal - $amount;
        }
        $voucher_num = $dbl->getVoucherNum();
        $userid = $user->id;
        $crdetails = $dbl->getCRDetailsByUserId($userid);
        $stateobj = $dbl->getStateInfo($crdetails->state);
        $stateTin = $stateobj->TIN;
        $crid = $crdetails->id;
        $voucher_no = strtoupper("IM".$dbl->getCRCode($userid) . "/" . $dbl->getActiveFinancialYear() . "-" . $stateTin . "/" . $voucher_num);
        $obj = $dbl->insertIntoImprestDetails($amount,$description,$voucher_no, $userid, $crid, $prevBal, $newBal, $impreason);
        if($obj != 0){
            $result = $dbl->updateVoucherNum();
            $updateCurrBal = $dbl->updateCurrBalanceByCrId($crid,$newBal);
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
    $redirect = 'imprest/register';
} else {

    
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'imprest/register';
}
session_write_close();
 header("Location: " . DEF_SITEURL . $redirect);
exit;
