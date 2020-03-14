<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$error = array();
$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;
//print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();

try{
    if(trim($sname)==""){
        $error['missing_name'] = "Enter supplier name";
    }else{  
        if(trim($sphone)!=""){
            $len = strlen($sphone);
            if($len > 10){            
                $error['missing_phone'] = "Phone no cannot be more than 10 digits.";
            }
        }
        
        if(trim($spincode)!=""){
            $len = strlen($spincode);
            if($len > 10){            
                $error['missing_pincode'] = "Pincode cannot be more than 10 digits.";
            }
        }
        
         $obj = $dbl->getSuppByNameId($sname,$supp_id);
            if(isset($obj) && !empty($obj)){
                $error['already_exist'] = "'$sname' already exist. Try to create new supplier";
            }
            
         if(trim($sphone)!=""){   
         $obj = $dbl->getSuppByPhoneId($sphone,$supp_id);
            if(isset($obj) && !empty($obj)){
                $error['already_exist'] = "Duplicate phone no not allowed";
            } 
         }   
        
        if(count($error) == 0){           
                $dbl->editSupplier($supp_id,$sname,$saddress,$sphone,$scity,$spincode,$user->id,$user->location_id);
                
            
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
    $redirect = 'supplier/edit/suppid='.$supp_id;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'suppliers';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;