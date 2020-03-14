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

$fileName = $_FILES['file']['name'];
$tmpName = $_FILES['file']['tmp_name'];
//$state_id = $_POST['rid'];
$flag=0;
$enr_id = "";
$dir = "../uploads/products/";

//print "<br> FILE NAME: ".$fileName;

$ext = end((explode(".", $fileName)));
$arr = array();
$err = "";
$estr = "Error. Please upload valid file. Below are the error(s) found.";
if($ext != "csv" ) {
    $errors["name"] = "Please upload .csv file only";
}else{
    $newfile = $dir."products_".$userid."_" . date("Ymd-His") . ".csv";
    if (!move_uploaded_file($tmpName, $newfile)) {
        $errors['fileerr'] = "File unable to load";
    }else{
        $err .= checkSequenceFile($newfile);
        if(trim($err)!=""){
            $errors['chkfile']= $estr.$err;
//                $errors[]= $err;
        }
        
        $err .= checkRate($newfile);
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

function checkSequenceFile($file){
    $fh = fopen($file,"r");
    $resp = "";    
   while(($data=fgetcsv($fh)) !== FALSE) {
       $col1 = $data[0];
       $no_space_value_c1 = str_replace(" ", "", $col1);
       
      // print "<br>".$no_space_value_c1;
       
       $col2 = $data[1];
       $no_space_value_c2 = str_replace(" ", "", $col2);
       $col3 = $data[2];
       $no_space_value_c3 = str_replace(" ", "", $col3);
       $col4 = $data[3];
       $no_space_value_c4 = str_replace(" ", "", $col4);
       $col5 = $data[4];
       $no_space_value_c5 = str_replace(" ", "", $col5);
       $col6 = $data[5];
       $no_space_value_c6 = str_replace(" ", "", $col6);
       $col7 = $data[6];
       $no_space_value_c7 = str_replace(" ", "", $col7);
       
       
       if (strcmp(strtolower(trim($no_space_value_c1)), "category") !== 0){
                 $resp .= "<br/>Column no 1 is not Category<br/>"; 
       }
       
        if (strcmp(strtolower(trim($no_space_value_c2)), "shopifyname") !== 0){
                 $resp .= "<br/>Column no 2 is not Shopify Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c3)), "itemname") !== 0){
                 $resp .= "<br/>Column no 3 is not Item Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c4)), "uom") !== 0){
                 $resp .= "<br/>Column no 4 is not UOM<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c5)), "packsize") !== 0){
                 $resp .= "<br/>Column no 5 is not Pack Size<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c6)), "rate") !== 0){
                 $resp .= "<br/>Column no 6 is not Rate<br/>"; 
       }
       if (strcmp(strtolower(trim($no_space_value_c7)), "purchasinguom") !== 0){
                 $resp .= "<br/>Column no 7 is not Purchasing UOM<br/>"; 
       }
       break;
     }  
  return $resp;  
}

function checkRate($newfile){
   $dbLogic = new DBLogic();  
   $fh = fopen($newfile,"r");
   $row = 1;
   $flag = 1;
   while(($data=fgetcsv($fh)) !== FALSE) {
       if($flag == 1){
          $flag = 2;
          continue;
       }                          
       $itemname = $data[2];      
       $rate = $data[5];
       $row++;
       if(trim($rate)!=""){
           if(!is_numeric($rate)){
               $resp .= "<br/>Invalid Rate for $itemname. Enter numeric only.<br/>";
           }
       }
       
       if(trim($itemname)!=""){
            $pobj = $dbLogic->getProductByName($itemname);
            if(isset($pobj) && !empty($pobj) && $pobj != null){
               $resp .= "<br/>Itemname '$itemname'  already exists. Please enter a new itemname <br/>"; 
            }
       }
   }
   return $resp;  
}

/*function checkFileData($file,$distid){
    $fh = fopen($file,"r");
    $clsSubDealer = new clsSubDealer();
    $resp = "";
    $row = 1;
    $flag = 1;
    $validcnt = 0;
   while(($data=fgetcsv($fh)) !== FALSE) {
       if($flag == 1){
          $flag = 2;
          continue;
       }
       $sub_dealer_code = $data[0];                           
       $amount = $data[1];       
       $reason = $data[2];
       $row++;
       if(trim($sub_dealer_code)!= "" && trim($amount)!="" && trim($reason) != ""){
          $sobj = $clsSubDealer->getSubDealerByCode($sub_dealer_code);  
          if(isset($sobj) && !empty($sobj) && $sobj != null){
             //check if that sub dealer is assigned to the dist 
             $obj = $clsSubDealer->getDistDealerEntry($distid,$sobj->id); 
             if(isset($obj) && !empty($obj) && $obj != null){
                if($obj->is_active == 0 && $obj->flag == SubDealerStatus::PendingForApproval){
                    $resp .= "<br>Sales head Response for Business approval is awaited for  sub dealer with code '$sub_dealer_code' at line no $row .";
                }else if($obj->is_active == 0 && $obj->flag == SubDealerStatus::Disapproved){
                    //Your business request against the dealer has  been disapproved by Saleshead
                    $resp .= "Sales head has disapproved Business request for  sub dealer with code '$sub_dealer_code' at line no $row .";
                }else if($obj->is_active == 1){
                    //valid do nothing
                    $validcnt++;
                } 
             }else{
                $resp .= "<br> Business Approval for the sub dealer with code '$sub_dealer_code' at line no $row  is awaiting . ";                 
             }
          }else{
              $resp .= "<br> Sub Dealer Code '$sub_dealer_code' at line no $row does not exist ";
              
          }
       }
      
       
       
     }  
     if($validcnt == 0){
        $resp .= "<br> File is Empty ";
     }
  return $resp;  
}*/

if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "product/upload";
    unset($_SESSION['productupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);    
    $_SESSION['form_success'] = $success;
    $redirect = "product/upload";
    $_SESSION['productupload_fpath']=$newfile;        
    unset($_SESSION['productupload']);
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;
