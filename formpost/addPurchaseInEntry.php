<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$errors = array();
$dbLogic = new DBLogic();
extract($_GET);
$_SESSION['form_id'] = $form_id;
//print_r($_GET);
$success = "";
$user = getCurrStore();

try{
    if(!isset($selsupp) || trim($selsupp)==""){
          $response = array(
               "error"   => 1,                 
               "message" => "Please select supplier"
             );
            print json_encode($response);
            return;
            exit;
       } 
       
    if(!isset($selprod) || trim($selprod)==""){
          $response = array(
               "error"   => 1,                 
               "message" => "Please select product"
             );
            print json_encode($response);
            return;
            exit;
       }    
    
     if(!isset($seluom) || trim($seluom)==""){
          $response = array(
               "error"   => 1,                 
               "message" => "Please select uom"
             );
            print json_encode($response);
            return;
            exit;
       }   
       
       if(!isset($qty) || trim($qty)==""){
          $response = array(
               "error"   => 1,                 
               "message" => "Please enter quantity"
             );
            print json_encode($response);
            return;
            exit;
       }else if(trim($qty) < 0){
          $response = array(
               "error"   => 1,                 
               "message" => "Quantity cannot be negative"
             );
            print json_encode($response);
            return;
            exit; 
       }else if(! is_numeric($qty)){
          $response = array(
               "error"   => 1,                 
               "message" => "Quantity should be numeric"
             );
            print json_encode($response);
            return;
            exit; 
       }  
       
       
        if(!isset($rate) || trim($rate)==""){
          $response = array(
               "error"   => 1,                 
               "message" => "Please enter price"
             );
            print json_encode($response);
            return;
            exit;
       }else if(trim($rate) < 0){
          $response = array(
               "error"   => 1,                 
               "message" => "Price cannot be negative"
             );
            print json_encode($response);
            return;
            exit; 
       }else if(! is_numeric($rate)){
          $response = array(
               "error"   => 1,                 
               "message" => "Price should be numeric"
             );
            print json_encode($response);
            return;
            exit; 
       } 
       
       
       $inserted_id = $dbLogic->addPurchaseIn($purin_id,$selprod,$selsupp,$qty,$rate,$seluom,$user->id,$user->location_id);
       if(trim($inserted_id) > 0){
          $response = array(
               "error"   => 0,                 
               "message" => "success"
             );
            print json_encode($response);
            return;
            exit;  
       }else{
           $response = array(
               "error"   => 1,                 
               "message" => "Error"
             );
            print json_encode($response);
            return;
            exit; 
       }
    
} catch (Exception $ex) {
    $errors['exc'] = $ex->message;
}
//print_r($errors);
if (count($errors) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $errors;
    $redirect = 'purchase/in/prin_id='.$purin_id;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    $_SESSION['form_success'] = $success;
    $redirect = 'purchase/in/prin_id='.$purin_id;
}
//session_write_close();
//header("Location: " . DEF_SITEURL . $redirect);
//exit;