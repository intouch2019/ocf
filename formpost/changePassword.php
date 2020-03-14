<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_GET);
//print_r($_POST);
$_SESSION['form_get'] = $_GET;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $err_msg = "";
    $username = isset($username) && trim($username) != "" ? $username : false;
    if(!$username){ $err_msg .= "Not able to get Username"; }
    
    $password = isset($password) && trim($password) != "" ? $password  :false;
    if(!$password){ $err_msg .= "Password cannot be blank"; }

    $password2 = isset($password2) && trim($password2) != "" ? $password2  :false;
    if(!$password2){ $err_msg .= "Confirm Password cannot be blank"; }
    
    if($password != "" && $password2 != ""){
        if($password != $password2){
            $err_msg .= "Both the passwords should be same";
        }
    }
    
    if($err_msg != ""){
        $resp = array(
            "error" => "1",
            "msg" => $err_msg
        );
        echo json_encode($resp);
    }else{
        
        $dbl->resetPassword($username,$password);
        $resp = array(
            "error" => "0",
            "msg" => "New password updated successfully"
        );
        echo json_encode($resp);
    }
    

    if(count($error) == 0){
       //$po_id = $dbl->insertPO($suppsel,$paymentterms,$deliveryterms,$transitinsurance,$dccode,$postatus,$ponum,$userid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
