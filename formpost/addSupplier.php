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
//print_r($_POST);
$success = "";
$user = getCurrStore();

try{
    if(trim($name)==""){
        $error['missing_name'] = "Enter supplier name";
    }else{  
        if(trim($phone)!=""){
            $len = strlen($phone);
            if($len > 10){            
                $error['missing_phone'] = "Phone no cannot be more than 10 digits.";
            }
        }
        
        if(trim($pincode)!=""){
            $len = strlen($pincode);
            if($len > 10){            
                $error['missing_pincode'] = "Pincode cannot be more than 10 digits.";
            }
        }
        
         $obj = $dbl->getSuppByName($name);
            if(isset($obj) && !empty($obj)){
                $error['already_exist'] = "'$name' already exist. Try to create new supplier";
            }
            
         if(trim($phone)!=""){   
         $obj = $dbl->getSuppByPhone($phone);
            if(isset($obj) && !empty($obj)){
                $error['already_exist'] = "Duplicate phone no not allowed";
            } 
         }   
        
        if(count($error) == 0){           
                $last_inserted_id = $dbl->insertSupplier($name,$address,$phone,$city,$pincode,$user->id,$user->location_id);
                if(trim($last_inserted_id)<=0){
                    $error['insert_fail'] = "Error while creating new supplier. Contact Intouch.";
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
    $redirect = 'supplier/create';
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