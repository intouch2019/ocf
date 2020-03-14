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

    $gateentryid = isset($gateentryid) && intval($gateentryid) > 0 ? intval($gateentryid) : false;
    if(!$gateentryid){ $error['missing_gateentryid'] = "Not able to get Gate Entry Id."; }
    
    $suppsel = isset($suppsel) && intval($suppsel) > 0 ? intval($suppsel) : false;
    if(!$suppsel){ $error['missing_supplier'] = "Select Supplier"; }

    $transsel = isset($transsel) && intval($transsel) > 0 ? intval($transsel) : false;
    if(!$transsel){ $error['missing_transporter'] = "Select Transporter"; }
    
    $lrno = isset($lrno) && trim($lrno) != "" ? trim($lrno) : false;
    if(!$lrno){ $error['missing_lrno'] = "Enter LR No"; }

    $details = isset($details) && trim($details) != "" ? trim($details) : false;
    //if(!$details){ $error['missing_details'] = "Enter Details"; }
    
    $qty = isset($qty) && trim($qty) != "" ? trim($qty) : false;
    if(!$qty){ $error['missing_qty'] = "Enter Quantity"; }

    $receiver_id = 0;
    $usersel = isset($usersel) && intval($usersel) > 0 ? intval($usersel) : $usersel;
    if(!$usersel){ $error['missing_receiver'] = "Select Receiver"; }else{
        if($usersel == -1){
            //echo "here<br>";
            $newreceiver = isset($newreceiver) && trim($newreceiver) != "" ? trim($newreceiver) : false;
            if(!$newreceiver){ $error['missing_receiver'] = "Enter Receiver"; }else{
                $receiver_id = $dbl->insertNoLoginUser($newreceiver);
            }
        }else{
            //echo "here<br>";
            $receiver_id = $usersel;
        }
    }
    //echo "Usersel : ".$usersel;

    //print_r($error);
    
    if(count($error) == 0){
       $dbl->editGateEntry($suppsel,$transsel,$lrno,$details,$qty,$receiver_id,$userid,$gateentryid);
       //echo $id;
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'gate/entry/edit/gateentryid='+$gateentryid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "gate/entry";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;