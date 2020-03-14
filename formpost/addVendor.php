<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$errors = array();
$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;
print_r($_POST);
$success = "";
$user = getCurrStore();

try{
    if(trim($name)==""){
        $errors['missing_name'] = "Enter vendor name";
    }else{  
        if(trim($phone)!=""){
            $len = strlen($phone);
            if($len > 10){            
                $errors['missing_phone'] = "Phone no cannot be more than 10 digits.";
            }
        }
        
        if(trim($pincode)!=""){
            $len = strlen($pincode);
            if($len > 10){            
                $errors['missing_pincode'] = "Pincode cannot be more than 10 digits.";
            }
            
            if( !is_numeric($pincode)){            
                $errors['missing_pincode'] = "Pincode should be numeric";
            }
        }
        
        if(trim($commper)!=""){           
            if(! is_float($commper) && !is_numeric($commper)){            
                $errors['missing_pincode'] = "Commision percentage should not contain characters.";
            }
        }
        
         $obj = $dbl->getVendorByName($name);
            if(isset($obj) && !empty($obj)){
                $errors['already_exist'] = "'$name' already exist. Try to create new vendor";
            }
            
         if(trim($phone)!=""){   
         $obj = $dbl->getVendorByPhone($phone);
            if(isset($obj) && !empty($obj)){
                $errors['already_exist'] = "Duplicate phone no not allowed";
            } 
         }   
         
//         print_r($errors);
        
        if(count($errors) == 0){           
                $last_inserted_id = $dbl->insertVendor($name,$address,$phone,$city,$pincode,$commper,$user->id,$user->location_id);
                if(trim($last_inserted_id)<=0){
                    $errors['insert_fail'] = "Error while creating new supplier. Contact Intouch.";
                }
            
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
    $redirect = 'vendor/create';
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