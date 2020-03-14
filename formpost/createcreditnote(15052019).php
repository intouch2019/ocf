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

    $invoiceno = isset($invoiceno) && trim($invoiceno) != "" ? trim($invoiceno) : false;
    if(!$invoiceno){ $error["missing_invno"] = "Please enter Invoice number"; }
    
    $discount = isset($discount) && trim($discount) != "" ? trim($discount) : false;
    if(!$discount){ $error["missing_invno"] = "Please enter Discount"; }
    
    $invid = null;
    $suppid = null;
    $objinv = $dbl->getInvoiceDetailsByInvoiceNo($invoiceno);
    //print_r($objinv);
    if($objinv == null){ $error["missing_invno"] = "Invoice number entered does not exist. Please check Invoice Number."; }else{
        if($objinv->status != InvoiceStatus::Created){
            $error["missing_pono"] = "Invoice number entered is not created.";
        }else{
            $invid = $objinv->id;
            $invdate = $objinv->saledate;
            $customer_id = $objinv->customer_id;
            $cname  = $objinv->cname;
            $cphone = $objinv->cphone;
            //$suppid = $objinv->supplier_id;
        }
    }
 
    /*partial implimentation*/
    $cndate = isset($cndate) && trim($cndate) != "" ? yymmdd($cndate) : false;
    if(!$cndate){ $error["missing_grndate"] = "Please enter date"; }
    
    
    
    $stateid = 0;

    $statenumber = 0;
    $crobj = $dbl->getCRDetailsById($user->crid);
        $objstate = $dbl->getStateInfo($crobj->state);
        if($objstate != null){
            $statenumber = $objstate->TIN;
            $stateid = $crobj->state;
        }
  // echo $statenumber;
    $cnstatus = CreditNoteStatus::Open;
    $cnnum = "CN-".$statenumber."/".$dbl->getActiveFinancialYear()."-".$dbl->fetchNextCNNumber($stateid);
    //echo $cnnum;
    
    if(count($error) == 0){
       $cnid = $dbl->insertCreditNote($invid, $invdate, $invoiceno, $customer_id, $cname, $cphone, $cndate, $cnnum, $cnstatus, $userid,$discount);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'creditnote/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "creditnote/additem/cnid=".$cnid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect); 
exit;