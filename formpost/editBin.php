<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$error = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$bid = trim($_POST['bin_id']);
$lid = trim($_POST['loc_id']);
$bname= trim($_POST['binname']);
$isactive = trim($_POST['actvsel']);
$_SESSION['form_post'] = $_POST;
$success = "";
$db = new DBConn();
$clsBin = new DBLogic();
$user = getCurrStore();
$userid = $user->id;
try{
    $updatedrow =0;
    if(trim($bname)==""){
        $error['missing_name'] = "Enter Bin Name";
    }else{  
        if(count($error) == 0){
            $obj = $clsBin->getBinByNameId($bname,$lid,$bid);
            if($obj){
                 $error['already_exist'] = "'$bname' Bin already exist.";
            }else{
                //update product               
               $updatedrow= $clsBin->updateBin($bid,$bname,$isactive,$userid,$user->location_id);
    //         echo $updatedrow;
            }  
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
    $redirect = 'bin/edit/bid='.$bid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'bins';
}
//session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;