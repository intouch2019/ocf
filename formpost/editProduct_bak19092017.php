<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once "lib/db/DBLogic.php";

$error = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$pid = trim($_POST['prod_id']);
$pname= trim($_POST['prodname']);
$rate = trim($_POST['prodrate']);
$uom = trim($_POST['uomsel']);
$category = trim($_POST['catsel']);
$pcksize = trim($_POST['pckszsel']);
$isactive = trim($_POST['actvsel']);

$success = "";
$db = new DBConn();
$dbl = new DBLogic();
$user = getCurrStore();
$userid = $user->id;
try{
    $updatedrow =0;
    if(trim($pid)=="" && trim($rate)=="" && trim($pname)==""){
        $error['missing_name_rate'] = "Name, Rate can't be Empty";
    }else if(trim($pname)==""){
        $error['missing_name'] = "Enter Product Name";
    }else if(trim($rate)==""){
        $error['missing_rate'] = "Enter Product Rate";
    }else{  
        $pobj = $dbl->getProductByNameId($pname,$pid);
        if($pobj){
             $error['already_exist'] = "$pname product already exist.";
        }else{
            //call karrot api
            //if success then update intouch db else show err msg, err msg :- Karrot API error: err msg
            
            //update product               
           $updatedrow= $dbl->updateProduct($category,$uom,$pcksize,$pid,$rate,$pname,$isactive,$userid,$user->location_id);
//            echo $updatedrow;
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
    $redirect = 'product/edit/pid='.$pid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    $_SESSION['form_success'] = $success;
    $redirect = 'products';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;