<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
//require_once "lib/subdealer/clsSubDealer.php";
require_once "lib/core/Constants.php";
//require_once "lib/distributor/clsDistributor.php";

$errors = array();
$success = "Success";
$db = new DBConn();
$dbl = new DBLogic();
$user = getCurrStore();

extract($_GET);
$_SESSION['form_id'] = $form_id;

if (!isset($filename) && trim($filename) == "") {
    $errors['file'] = "File not found";
}
//$state_id = $_GET['rid'];
$csvAsArray = array_map('str_getcsv', file($filename));
$ext = end((explode(".", $filename)));
//echo "Extension : ".$ext;
if($ext != "csv" ) {
    $errors["name"] = "Please upload .csv file only";
}

if(count($errors)== 0){
   loadFileData($filename);
}

if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "product/upload";
        unset($_SESSION['productupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);
    unset($_SESSION['form_id']);
    unset($_SESSION['creditnote_fpath']);
    $_SESSION['form_success'] = $success;
   $redirect = "products";
   $_SESSION['productupload'] = "done";
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;


function loadFileData($filepath){
    $fh = fopen($filepath,"r");
    //$clsSubDealer = new clsSubDealer();
    $dbLogic = new DBLogic();
    $user = getCurrStore();
    $userid = $user->id;
    $resp = "";
    $row = 1;
    $flag = 1;
    //$skipped_cnt = 0;
   while(($data=fgetcsv($fh)) !== FALSE) {
       if($flag == 1){
          $flag = 2;
          continue;
       }
       $category = $data[0];                           
       $itemname = $data[1];       
       $uom = $data[2];
       $packsize = $data[3];
       $rate = $data[4];
       $row++;
      
       if(trim($category)!= "" && trim($itemname)!="" && trim($uom) != "" && trim($packsize)!=""){
          
           //category 
          $ctgobj = $dbLogic->getCtgByName($category);
          if(isset($ctgobj) && !empty($ctgobj) && $ctgobj != null){
             $category_id = $ctgobj->id; 
          }else{
              //insert ctg
              $category_id = $dbLogic->insertCategory($category,$user->id,$user->location_id);
          }
          
           //uom
          $uomobj = $dbLogic->getUOMByName($uom);
          if(isset($uomobj) && !empty($uomobj) && $uomobj != null){
             $uom_id = $uomobj->id; 
          }else{
              //insert ctg
             $uom_id = $dbLogic->insertUOM($uom,$user->id,$user->location_id);
          }
          
          //pack size
          $pszobj = $dbLogic->getPackSize($packsize);
          if(isset($pszobj) && !empty($pszobj) && $pszobj != null){
             $pack_size_id = $pszobj->id; 
          }else{
              //insert ctg
             $pack_size_id = $dbLogic->insertPackSize($packsize,$user->id,$user->location_id);
          }
          
          //call karrot api 
          // fectbyhandle
             // update/insert api of karrot
          
          //insert product
          if(trim($category_id)!="" && trim($category_id)!="-1" && trim($uom_id)!="" && trim($uom_id)!="-1" && trim($pack_size_id)!="" && trim($pack_size_id)!="-1" ){
             $pobj = $dbLogic->getProductByName($itemname); 
             if(isset($pobj) && !empty($pobj) && $pobj != null){
                //update info 
                 $dbLogic->updateProduct($category_id,$uom_id,$pack_size_id,$pobj->id,$rate,$userid,$user->location_id);
             }else{
                 //insert
                 $dbLogic->insertProduct($category_id,$uom_id,$pack_size_id,$itemname,$rate,$userid,$user->location_id);
             }
          }else{
              $skipped_cnt++;
          }
          
       }
       
      
      
       
       
     }  
  return $resp;  
}