<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$errors = array();
$success = "File is Valid. Do you want to continue";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
//$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;

//print_r($_POST);
//
//print_r($_FILES);
//if(!isset($sellocation) || trim($sellocation)==""){
//    $errors['selloc'] = "Please select location";
//}

if(!isset($selltype) || trim($selltype)==""){
    $errors['selloctype'] = "Please select location type";
}

if(!isset($appldt) || trim($appldt)=="" || trim($appldt)=="Select Date"){
    $errors['appldt'] = "Please select date";
}


if(count($errors)==0){

$fileName = $_FILES['file']['name'];
$tmpName = $_FILES['file']['tmp_name'];
//$state_id = $_POST['rid'];
$flag=0;
$enr_id = "";
$dir = "../uploads/location_price/";

//print "<br> FILE NAME: ".$fileName;

$ext = end((explode(".", $fileName)));
$arr = array();
$err = "";
$estr = "Error. Please upload valid file. Below are the error(s) found.";
if($ext != "csv" ) {
    $errors["name"] = "Please upload .csv file only";
}else{
    $newfile = $dir."location_price_".$userid."_" . date("Ymd-His") . ".csv";
    if (!move_uploaded_file($tmpName, $newfile)) {
        $errors['fileerr'] = "File unable to load";
    }else{
        $err .= checkSequenceFile($newfile);
        if(trim($err)!=""){
            $errors['chkfile']= $estr.$err;
//                $errors[]= $err;
        }
        
        $err .= checkData($newfile);
        if(trim($err)!=""){
            $errors['chkfile']= $estr.$err;
 //                $errors[]= $err;
        }
        
//        if(empty($errors)){
//           //check file for data
//            $err.= checkFileData($newfile,$user->dist_ids);
//            if(trim($err)!=""){
//                $errors['chkfile']= $estr.$err;
//    //                $errors[]= $err;
//            }
//        }
        
        
        
    }
    
   // print_r($errors);
    
}

}

function checkSequenceFile($file){
    $fh = fopen($file,"r");
    $resp = "";    
   while(($data=fgetcsv($fh)) !== FALSE) {
       $col1 = $data[0];
       $no_space_value_c1 = str_replace(" ", "", $col1);      
       $col2 = $data[1];
       $no_space_value_c2 = str_replace(" ", "", $col2);
       $col3 = $data[2];
       $no_space_value_c3 = str_replace(" ", "", $col3);
       $col4 = $data[3];
       $no_space_value_c4 = str_replace(" ", "", $col4);
      
       
       if (strcmp(strtolower(trim($no_space_value_c1)), "shopifyproductname") !== 0){
                 $resp .= "<br/>Column no 1 is not Shopify Product Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c2)), "productname") !== 0){
                 $resp .= "<br/>Column no 2 is not Product Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c3)), "price") !== 0){
                 $resp .= "<br/>Column no 3 is not Price<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c4)), "status(active-aanddeactive-d)") !== 0){
                 $resp .= "<br/>Column no 4 is not Status(Active-A and Deactive -D)<br/>"; 
       }
                                   
       break;
     }  
  return $resp;  
}

function checkData($newfile){
   $dbl = new DBLogic(); 
   $fh = fopen($newfile,"r");
   $row = 1;
   $flag = 1;
   while(($data=fgetcsv($fh)) !== FALSE) {
       if($flag == 1){
          $flag = 2;
          continue;
       }
       $shopifyname = $data[0];
       $itemname = $data[1];      
       $price = $data[2];
       $status = $data[3];
       $row++;
       if(trim($itemname)!=""){
           $iobj = $dbl->getProductByName($itemname);
           if(isset($iobj) && !empty($iobj) && $iobj != null){
               //do nothing
           }else{
               //throw error
               $resp .= "<br/>Itemname '$itemname' does not exists .Please enter valid itemname.<br/>";
           }
       }
       if(trim($price)!=""){
           if(!is_numeric($price)){
               $resp .= "<br/>Invalid Price for $itemname. Enter numeric only.<br/>";
           }
           
           if($price < 0){
               $resp .= "<br/>Invalid Price for $itemname. Negative value not allowed.<br/>";
           }
       }
       
       if(trim($status)!=""){
          if (strcmp(strtolower(trim($status)), "a") !== 0){
                if (strcmp(strtolower(trim($status)), "d") !== 0){
                    $resp .= "<br/>Invalid Status for $itemname. Status should be either 'A for Active' or 'D for Deactivate'<br/>"; 
                }                
          } 
       }
   }
   return $resp;  
}



if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "location/price/upload";
    unset($_SESSION['locprupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);    
    $_SESSION['form_success'] = $success;
   // $redirect = "location/price/upload";
    $redirect = "location/price/upload/lid=$selltype/appldt=$appldt";
    $_SESSION['locprupload_fpath']=$newfile;        
    unset($_SESSION['locprupload']);
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;
