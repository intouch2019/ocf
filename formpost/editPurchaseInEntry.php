<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once "lib/db/DBLogic.php";
require_once "lib/core/strutil.php";


$errors = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;    


$success = "";
$db = new DBConn();
$dbl = new DBLogic();
$user = getCurrStore();
//print_r($user);

try{
    $updatedrow =0;
    if(!isset($selsupp) || trim($selsupp)==""){
           $errors[] = "Please select supplier";
     } 
     
     if(!isset($seluom) || trim($seluom)==""){
           $errors[] = "Please select uom";
     }  
     
     if(trim($qty)!=""){
         if(trim($qty) < 0){
          $errors[] = "Quantity cannot be negative ";            
       }else if(! is_numeric($qty)){
            $errors[] = "Quantity should be numeric";                                               
       }  
     }else{
          $errors[] = "Please enter qunatity ";
     }
     
     
    if(!isset($rate) || trim($rate)==""){                        
        $errors[] =  "Please enter price";             
    }else if(trim($rate) < 0){                         
     $errors[] = "Price cannot be negative";            
    }else if(! is_numeric($rate)){                     
      $errors[] = "Price should be numeric";          
    }  
     
                
    if(count($errors)==0){       
        $dbl->updatePurchaseInEntry($prenid,$selsupp,$qty,$seluom,$rate,$user->id,$user->location_id);
    }    
    
} catch (Exception $ex) {
    $errors['exc'] = $ex->message;
}
//print "<br>Errors---";
//print_r($errors);
if (count($errors) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $errors;
    $redirect = 'purchase/in/entry/edit/pid='.$prenid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    $_SESSION['form_success'] = $success;
    $redirect = 'purchase/in';
}
//session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
