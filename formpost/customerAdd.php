<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $name = isset($name) && trim($name) != "" ? trim($name) : false;
    if(!$name){ $error['missing_name'] = "Enter customer name"; }

    $address = isset($address) && trim($address) != "" ? trim($address) : false;
    
    $statesel = isset($statesel) && trim($statesel) != "" ? trim($statesel) : false;
    if(!$statesel){ $error['missing_state'] = "Select State"; }
    
    $city = isset($city) && trim($city) != "" ? trim($city) : false;
    
    $phone = isset($phone) && trim($phone) != "" ? trim($phone) : false;
    if(!$phone){ $error['missing_phone'] = "Enter customer phone"; }
    
    $custid = isset($custid) && trim($custid) != "" ? trim($custid) : false;
    $email = isset($email) && trim($email) != "" ? trim($email) : false;
    $gstno = isset($gstno) && trim($gstno) != "" ? trim($gstno) : false;
    $panno = isset($panno) && trim($panno) != "" ? trim($panno) : false;
    
    $reg = isset($reg) && trim($reg) != "" ? trim($reg) : false;

//    $customerUniqueNumber = strtoupper($user->crcode)."/".$dbl->fetchNextCustNumber();
    $crinfo = $dbl->getCRInfoById($user->crid);
    $customerUniqueNumber = strtoupper($crinfo->dispname)."/".$dbl->fetchNextCustNumber();
    $crid = $user->crid;
    
    $customer_id = 0;
    if(count($error) == 0){
        if($custid !=null){
          $update_id = $dbl->updateCustomers($custid,$name,$address,$statesel,$city,$phone,$email,$gstno,$panno,$userid,$reg,$customerUniqueNumber,$crid); 
          $customer_id = $custid;
        }else{
          $customer_id = $dbl->insertCustomer($name,$address,$statesel,$city,$phone,$email,$gstno,$panno,$userid,$reg,$customerUniqueNumber,$crid);
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
    $redirect = 'customer/add';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = 'sales/create/custid='.$customer_id;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
