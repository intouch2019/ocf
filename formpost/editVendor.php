<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$errors = array();
$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;
//print_r($_POST);
$success = "";
$user = getCurrStore();

try{
    if(trim($sname)==""){
        $errors['missing_name'] = "Enter vendor name";
    }else{  
        if(trim($sphone)!=""){
            $len = strlen($sphone);
            if($len > 10){            
                $errors['missing_phone'] = "Phone no cannot be more than 10 digits.";
            }
        }
        
        if(trim($spincode)!=""){
            $len = strlen($spincode);
            if($len > 10){            
                $errors['missing_pincode'] = "Pincode cannot be more than 10 digits.";
            }
            
            if( !is_numeric($spincode)){            
                $errors['missing_pincode'] = "Pincode should be numeric";
            }
        }
        
         $obj = $dbl->getVendorByNameId($sname,$vid);
            if(isset($obj) && !empty($obj)){
                $errors['already_exist'] = "'$sname' already exist. Try to create new vendor";
            }
            
         if(trim($sphone)!=""){   
         $obj = $dbl->getVendorByPhoneId($sphone,$vid);
            if(isset($obj) && !empty($obj)){
                $errors['already_exist'] = "Duplicate phone no not allowed";
            } 
         }   
        
         if(trim($scommper)!=""){           
            if(! is_float($scommper) && !is_numeric($scommper)){            
                $errors['missing_pincode'] = "Commision percentage should not contain characters.";
            }
        }
        if(count($errors) == 0){           
                $dbl->editVendor($vid,$sname,$saddress,$sphone,$scity,$spincode,$scommper,$user->id,$user->location_id);
                
            
        }
    }
} catch (Exception $ex) {
    $errors['exc'] = $ex->message;
}
//print_r($errors);
if (count($errors) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $errors;
    $redirect = 'vendor/edit/vid='.$vid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    $_SESSION['form_success'] = $success;
    $redirect = 'vendors';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;