<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once "lib/email/EmailHelper.php";

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$dbl = new DBLogic();
try{
    
    $fileName = $_FILES['csv']['name'];
    $tmpName = $_FILES['csv']['tmp_name'];
    
    $cr = isset($selcr) && trim($selcr) != "" ? $selcr : null;
    if($cr == null){
        $error["missing_cr"] = "Select CR / Select All CR";
    }
    $currDate=date("Y-m-d", time());
    $upTime="02:00:00";
    $uploaddate = isset($uploaddate) && trim($uploaddate) != "" ? yymmdd($uploaddate) : false;
   
    if(!$uploaddate){ $error["missing_uploadddate"] = "Please enter Upload date"; }
     if(strtotime($uploaddate) > strtotime($currDate)){
         $uploaddate = $uploaddate." ".$upTime;
     }else{
   $uploaddate = $uploaddate." ".date("H:i", time());
     }
    if($tmpName == NULL || $tmpName == ""){
        $error["missing_file"] = "Please upload the product pricing file";
    }
    
    if(count($error) == 0){
    $row_no = 1;
    $error_msg = "";
    $fileHandle = fopen($tmpName, "r");
    while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
        //echo "here second loop<br>";
        if($row_no == 1) { $row_no++; continue; }
        
        $srno = isset($row[0]) && trim($row[0]) != "" ? $row[0] : false;
        $prodid = isset($row[1]) && trim($row[1]) != "" ? $row[1] : false;
        $price =  isset($row[12]) && trim($row[12]) != "" ? $row[12] : false;
        //if(!$price){
        if($price == ""){
            $error_msg .= "Missing price";
        }
        
        if($error_msg != ""){
            $error[$row_no] = "Error at Row : ".$row_no."=>".$error_msg;
        }
        $row_no++;
    }
    
    /*$obj_cnt = $dbl->getActiveProductsCount();
    if($obj_cnt != null){
        $active_products = $obj_cnt->cnt;
        $excel_products = $row_no - 1;
        if($excel_products < $active_products){
            $error_msg .= "Upload prices for all the products";
        }
    }*/
    
    if($error_msg != ""){
        $row_no++;
        $error[$row_no] = $error_msg;
    }
    }

    if(count($error) == 0){    
        $row_no = 1;
        $fileHandle1 = fopen($tmpName, "r");    
        while (($row = fgetcsv($fileHandle1, 0, ",")) !== FALSE) {
            //echo "here second loop<br>";
            if($row_no == 1) { $row_no++; continue; }
            $row_no++;
            $srno = isset($row[0]) && trim($row[0]) != "" ? $row[0] : false;
            $prodid = isset($row[1]) && trim($row[1]) != "" ? $row[1] : false;
            $price =  isset($row[12]) && trim($row[12]) != "" ? $row[12] : false;
            
            //fetch last uploaded price for the same product
            $last_price = 0;
            $obj_prod_last_price = $dbl->fetchLastProductPrice($prodid,$price,$cr);
            if($obj_prod_last_price != NULL){
                $last_price = $obj_prod_last_price->price;
            }
            $id = $dbl->uploadProductPrice($prodid,$price,$userid,$cr,$last_price,$uploaddate);
            /*$obj_prod_price = $dbl->fetchProductPrice($prodid,$price);
            if($obj_prod_price != NULL && isset($obj_prod_price)){
                $dbl->updateProductPrice($prodid,$price,$userid,$cr);
            }else{*/

            //}
        }
        $row_no = $row_no - 2;
        //$currDate = date("Y-m-d");
        $currDate = $uploaddate;
        $usertype = UserType::Director;
        $obj_user = $dbl->getUserInfoByType($usertype);
        $crname = "";
        if($cr > 0){
            $objcr = $dbl->getCRInfoById($cr);
            if($objcr != NULL){
                $crname = $objcr->dispname;
            }
        }else{
            $crname = "All CR";
        }
        
        $emailid = "";
        if($obj_user != NULL && isset($obj_user)){
            $emailid = $obj_user->email;
        }
        //email sending
        //$subject = "Approval awaiting : Product price uploaded (".ddmmyy($currDate).")";
        $subject = "Approval awaiting : Product price uploaded (".ddmmyy($currDate).")";
        $body = '<p>New product price uploaded for '.strtoupper($crname).'<br>'
              . 'Price uploaded for '.$row_no.' products<br>'
              . 'Please approve the prices</p>';
        $emailHelper = new EmailHelper();
        $emailHelper->send(array($emailid), $subject, $body);
        
        
    }
    
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'product/pricing/upload';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "product/pricing";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
