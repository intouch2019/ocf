<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/core/Constants.php";
require_once 'lib/user/clsUser.php';
require_once "lib/db/DBLogic.php";

$errors = array();
$user = getCurrStore();
$by_user = getCurrStoreId();
$userpage = new clsUser();
extract($_POST);
$_SESSION['form_id'] = $form_id;

$po_alloc_id = isset($_POST['po_alloc_id']) && trim($_POST['po_alloc_id']) != "" ? $_POST['po_alloc_id'] : false;
$supplierId = isset($_POST['supplier']) && trim($_POST['supplier']) != "" ? $_POST['supplier'] : false;


$dbl = new DBLogic();
$errors = array();
$cnt = 0;
$success = "";
try{   
    
    

    $details = $dbl->getPoAllocationDetailsById($po_alloc_id);

    $result = $dbl->changeSupplier($details->transferid,$po_alloc_id,$supplierId);
    // print_r($result);
    if($result){
      $cnt = 1;
    }
    
}catch(Exception $xcp){
   $errors['xcp'] = $xcp->getMessage();
}

if($cnt > 0){
  $success = "New Supplier assigned successfully ";
}else{
  $errors['pg'] =  "Failed Assigned New Supplier";
}  
if (count($errors)>0) {
        unset($_SESSION['form_success']);       
        $_SESSION['form_errors'] = $errors;
  } else {
        unset($_SESSION['form_errors']);
        $_SESSION['form_success'] = $success;        
  }
  
  header("Location: ".DEF_SITEURL."cr/stock/pull/po_alloc_id=".$po_alloc_id);
  exit;



