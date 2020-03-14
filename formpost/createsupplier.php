<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $dateofentry = isset($dateofentry) && trim($dateofentry) != "" ? yymmdd($dateofentry) : false;
    if(!$dateofentry){ $error['missing_dateofentry'] = "Enter Date of entry"; }

    $kycnumber = isset($kycnumber) && trim($kycnumber) != "" ? $kycnumber : false;
    if(!$kycnumber){ $error['missing_kycnumber'] = "Enter Supplier KYC Number"; }

    $companyname = isset($companyname) && trim($companyname) != "" ? $companyname : false;
    if(!$companyname){ $error['missing_companyname'] = "Enter Company Name"; }

    $suppliercode = "0000000";
    $firstCharacter = substr($companyname, 0, 1);
    $query = "select snumber from it_supplier_codes where prefix = '$firstCharacter'";
    $obj_scodes = $db->fetchObject($query);
    if($obj_scodes  != null && isset($obj_scodes)){
        $suppliercode = $obj_scodes->snumber;
    }
    $suppliercode=sprintf('%07d', $suppliercode+1);
    
    $bankname = isset($bankname) && trim($bankname) != "" ? $bankname : false;
    if(!$bankname){ $error['missing_bankname'] = "Enter Bank Name"; }
    
    $bankaccno = isset($bankaccno) && trim($bankaccno) != "" ? $bankaccno : false;
    if(!$bankaccno){ $error['missing_bankaccno'] = "Enter Bank A/C No"; }
    
    $bankbranchname = isset($bankbranchname) && trim($bankbranchname) != "" ? $bankbranchname : false;
    if(!$bankbranchname){ $error['missing_bankbranchname'] = "Enter Bank Branch Name"; }    
    
    $firmtype = isset($firmtype) && trim($firmtype) != "" ? $firmtype : false;
    if(!$firmtype){ $error['missing_firmtype'] = "Missing Firm Type"; }    

    $currency = isset($currency) && trim($currency) != "" ? $currency : false;
    if(!$currency){ $error['missing_currency'] = "Missing Currency"; }    

    $state = isset($state) && trim($state) != "" ? $state : false;
    if(!$state){ $error['missing_state'] = "Select State"; }

    $country = isset($country) && trim($country) != "" ? $country : false;
    if(!$country){ $error['missing_country'] = "Select Country"; }
    
    $district = isset($district) && trim($district) != "" ? $district : false;
    if(!$district){ $error['missing_district'] = "Select District"; }

    $address = isset($address) && trim($address) != "" ? $address : false;
    if(!$address){ $error['missing_address'] = "Enter Address"; }

    $graddress = isset($graddress) && trim($graddress) != "" ? $graddress : false;

    $pincode = isset($pincode) && trim($pincode) != "" ? $pincode : false;
    if(!$pincode){ $error['missing_pincode'] = "Enter Pincode"; }
    
    $panno = isset($panno) && trim($panno) != "" ? $panno : false;
    
    $cinno = isset($cinno) && trim($cinno) != "" ? $cinno : false;    

    $gstapp = isset($gstapp) && trim($gstapp) != "" ? $gstapp : false;        
    
    $gstno = isset($gstno) && trim($gstno) != "" ? $gstno : false;        
    
    $contactperson1 = isset($contactperson1) && trim($contactperson1) != "" ? $contactperson1 : false;        
    $contactperson2 = isset($contactperson2) && trim($contactperson2) != "" ? $contactperson2 : false;        
    $contactperson3 = isset($contactperson3) && trim($contactperson3) != "" ? $contactperson3 : false;        
    $contactperson4 = isset($contactperson4) && trim($contactperson4) != "" ? $contactperson4 : false;        
    
    $phone1 = isset($phone1) && trim($phone1) != "" ? $phone1 : false;        
    $phone2 = isset($phone2) && trim($phone2) != "" ? $phone2 : false;        
    $phone3 = isset($phone3) && trim($phone3) != "" ? $phone3 : false;        
    $phone4 = isset($phone4) && trim($phone4) != "" ? $phone4 : false;        
    
    $email1 = isset($email1) && trim($email1) != "" ? $email1 : false;        
    $email2 = isset($email2) && trim($email2) != "" ? $email2 : false;        
    $email3 = isset($email3) && trim($email3) != "" ? $email3 : false;        
    $email4 = isset($email4) && trim($email4) != "" ? $email4 : false;        
    
    $msmedno = isset($msmedno) && trim($msmedno) != "" ? $msmedno : false;            

    if(count($error) == 0){
       $supp_id = $dbl->insertSupplier($dateofentry,$kycnumber,$companyname,$suppliercode,$bankname,$bankaccno,$bankbranchname,$firmtype,$currency,
               $state,$country,$district,$address,$graddress,$pincode,$panno,$cinno,$gstapp,$gstno,$contactperson1,$contactperson2,$contactperson3,$contactperson4,
               $phone1,$phone2,$phone3,$phone4,$email1,$email2,$email3,$email4,$msmedno,$userid);
       
       if($supp_id > 0){
           $dbl->updateSupplierCode($firstCharacter,$suppliercode);
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
    //$redirect = 'users';
    $redirect = "suppliers";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;