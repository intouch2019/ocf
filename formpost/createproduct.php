<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';

$error = array();
extract($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$dbl = new DBLogic();
try{
    $ctg_id = null;
    if($ctgsel == 0){
        $error['missing_category'] = "Select Category";
    }else if($ctgsel == -1){
        if($ctgnew == ""){
            $error['missing_category'] = "Insert category name";
        }else{
            $ctg_id = $dbl->insertCategory(trim($ctgnew),$userid);
        }
    }else if($ctgsel > 0){
        $ctg_id = $ctgsel;
    }

    if(trim($name) == ""){
        $error['missing_name'] = "Enter product description";
    }else{
        $name = trim($name);
    }
    
    $spec_id = null;
    if($specsel == 0){
        $error['missing_specification'] = "Select Specification";
    }else if($specsel == -1){
        if($specnew == ""){
            $error['missing_specification'] = "Insert spcification";
        }else{
            $spec_id = $dbl->insertSpecification(trim($specnew),$userid);
        }
    }else if($specsel > 0){
        $spec_id = $specsel;
    }
    
    if(count($error) == 0){
        $prod_id = $dbl->insertProduct($ctg_id, $name, $spec_id, $userid);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'product/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "products";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;