<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
//require_once "lib/subdealer/clsSubDealer.php";
require_once "lib/core/Constants.php";
//require_once "lib/distributor/clsDistributor.php";
require_once "lib/core/strutil.php";
require_once "api/fetchProdbyHandleAPI.php";
require_once "api/updateProductAPI.php";
require_once "api/createProductAPI.php";
require_once "api/updateVariantsAPI.php";

$errors = array();
$success = "Success";
$db = new DBConn();
$dbl = new DBLogic();
$user = getCurrStore();
$response = " Below are the items which didn't got updated: ";
$err = "";
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
  $err = loadFileData($filename,$loctype,$seldt);
  if(trim($err)!=""){
      $errors[] = $err;
  }
}

if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "location/price/upload";
        unset($_SESSION['locprupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);
    unset($_SESSION['form_id']);
    unset($_SESSION['locprupload_fpath']);
    $_SESSION['form_success'] = $success;
   $redirect = "location/price";
   $_SESSION['locprupload'] = "done";
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;


function loadFileData($filepath,$loctype,$seldt){
    $fh = fopen($filepath,"r");
    //$clsSubDealer = new clsSubDealer();
    $dbLogic = new DBLogic();
    $user = getCurrStore();
    $userid = $user->id;
    $resp = "";
    $row = 1;
    $flag = 1;
    $appldt = yymmdd($seldt);
    $resp = "";
    //$skipped_cnt = 0;
   while(($data=fgetcsv($fh)) !== FALSE) {
       if($flag == 1){
          $flag = 2;
          continue;
       }
       $itemname = $data[1];      
       $price = $data[2];
       $status = strtolower($data[3]);
       $row++;
       
       if(trim($itemname)!="" && trim($price)!="" && trim($status)!=""){    
           if($loctype == 3){ // online
            $resp .=  onlineLocType($itemname,$price,$status,$appldt,$loctype);  
           }else{
            $resp .=  otherLocTypes($itemname,$price,$status,$appldt,$loctype); 
           }
           
           
//            if(strcmp($status, "a") === 0){
//                $is_active = 1;
//            }else{
//                $is_active = 0;
//            }
//            $iobj = $dbLogic->getProductByName($itemname);            
//            if(isset($iobj) && !empty($iobj) && $iobj != null){
//                $lpobj = $dbLogic->fetchLocationTypePrice($loctype,$iobj->id,$appldt);
//                if(isset($lpobj) && !empty($lpobj) && $lpobj != null){
//                    //update
//                    $dbLogic->updateLocationTypePrice($lpobj->id,$appldt,$price,$is_active,$user->id,$user->location_id);
//                }else{
//                    //insert
//                    $dbLogic->insertLocationTypePrice($loctype,$iobj->id,$appldt,$price,$is_active,$user->id,$user->location_id);
//                }
//            }                       
       }
     }  
  return $resp;  
}

function otherLocTypes($itemname,$price,$status,$appldt,$loctype){
//    print "<br>IN OTHR TYPE FUNCTION <br>";
    $dbLogic = new DBLogic();
    $user = getCurrStore(); 
    $resp = "";
    if(strcmp($status, "a") === 0){
        $is_active = 1;
    }else{
        $is_active = 0;
    }
    $iobj = $dbLogic->getProductByName($itemname);            
    if(isset($iobj) && !empty($iobj) && $iobj != null){
        $lpobj = $dbLogic->fetchLocationTypePrice($loctype,$iobj->id,$appldt);
        if(isset($lpobj) && !empty($lpobj) && $lpobj != null){
            //update
            $dbLogic->updateLocationTypePrice($lpobj->id,$appldt,$price,$is_active,$user->id,$user->location_id);
        }else{
            //insert
            $dbLogic->insertLocationTypePrice($loctype,$iobj->id,$appldt,$price,$is_active,$user->id,$user->location_id);
        }
    } 
    
    return $resp;
}


function onlineLocType($itemname,$price,$status,$appldt,$loctype){
//    print "<br>IN ONLINE FUNCTION <br>";
    
    $dbLogic = new DBLogic();
    $user = getCurrStore(); 
    $fetchapicall = new fetchProdbyHandleAPI();
    $updateapicall = new updateProductAPI();
    $updatevarapicall = new updateVariantsAPI();
    $createapicall = new createProductAPI();
    $product_array = array();
    $product = array();
    $var_arr = array();
    $variants = array();
    $options = array();
    $opt_arr = array();
    $userid = $user->id;
    $resp = "";
//    print $userid;
    $issue= array();
    $resp = "";
    $row = 1;
    $flag = 1;
    $i = 0;//index for respose
    if(strcmp($status, "a") === 0){
        $is_active = 1;
    }else{
        $is_active = 0;
    }
    
    $iobj = $dbLogic->getProductByName($itemname);            
    if(isset($iobj) && !empty($iobj) && $iobj != null){
        //step 1: fetch by handle
        // create handle 
        //$handle = str_replace(" ","-", $itemname); 
        $handle = str_replace(" ","-", $iobj->shopify_name);
        $handle = trim($handle);
//      print "<br>handle=$handle<br>";
        $fetchresp ="";
        $fetchresp = $fetchapicall->fetchProdbyHandle($handle); 
       // print "<br> API FETCH RESP: <br> $fetchresp <br>";        
         if($fetchresp == ""){   //blank resp from shopify                
            $resp .=  "Error at Product'".$itemname."':<br>Karrot API Error: Empty Responce from Fetch API<br>";
        }else{
            if(preg_match("/.*(200\s*OK).*/",$fetchresp)== TRUE){   
                $prodrefid = "";
                $variant_id = "";
                $handle = "";
                $resp_arr= explode("{",$fetchresp,2);
                $json = "{".$resp_arr[1];
                $jobj = json_decode($json);
                if(isset($jobj->products[$i]->id)){
                    $prodrefid= $jobj->products[$i]->id;
                    $variant_id= $jobj->products[$i]->variants[$i]->id;
                    $handle= $jobj->products[$i]->handle;
                }    
                if($prodrefid != "" &&  $variant_id != "" && $handle != ""){
                    //update the status
                    $product["id"] = $prodrefid;
                   // $product["title"] = $itemname;
                     $product["title"] = $iobj->shopify_name;
                    $product["body_html"] = "";  
                    $product["vendor"] = "MrKarrot"; 
                    $product["product_type"] = $iobj->category; 
                    if(trim($is_active)==1){
                     $product["published"] = TRUE;    
                    }else{
                     $product["published"] = FALSE; 
                    }
                    $product["published_at"] = null; 
                    $product["template_suffix"] = null; 


                    //update the price
                    $variants["id"] = $variant_id;                        
                    $variants["price"] = $price;
                    
                    $var_arr["variant"] = $variants; 
                    
//                    print "<br> VARIANT ARR: <br>";
//                    print_r($var_arr);
//                    print "<br><br>";
                    
                    
//                    print "<br> PRODUCT ARR: <br>";
//                    print_r($product);
//                    print "<br><br>";
                    
                    $product_array["product"] = $product;
                    //update the status
                    $updtresp ="";                       
                    $updtresp = $updateapicall->updateProduct($prodrefid, $product_array); 
//                            print"<br>up prod resp---$updtresp<br>";
                    //blank chk
                    if($updtresp == ""){
//                                 print"In error<br>";
                         //$issue[$itemname]  ="<br>Error at Product'".$itemname."'<br>Karrot API Error: Empty Responce from Update Product API<br>";
                        $resp .= "<br>Error at Product'".$itemname."'<br>Karrot API Error: Empty Responce from Update Product API<br>";
                    }else{
                       if(preg_match('/.*(200\s*OK).*/',$updtresp)){   
                            //call the variant update API
                            $updtvarresp="";
                            $updtvarresp = $updatevarapicall->updateVariants($variant_id, $var_arr); 
    //                                    print"<br> up var resp- $updtvarresp<br>";
                            if($updtvarresp == ""){
                                //$issue[$itemname] ="<br>Error at Product'".$itemname."'<br>Karrot API Error: Empty Responce from Update Variants API<br>";
                                $resp .= "<br>Error at Product'".$itemname."'<br>Karrot API Error: Empty Responce from Update Variants API<br>";

                            }else{
                                // update the price and status at  intouch
                                $lpobj = $dbLogic->fetchLocationTypePrice($loctype,$iobj->id,$appldt);
                                if(isset($lpobj) && !empty($lpobj) && $lpobj != null){
                                    //update
                                    $dbLogic->updateLocationTypePrice($lpobj->id,$appldt,$price,$is_active,$user->id,$user->location_id);
                                }else{
                                    //insert
                                    $dbLogic->insertLocationTypePrice($loctype,$iobj->id,$appldt,$price,$is_active,$user->id,$user->location_id);
                                }
                            }
                       }else{
                           //error
                            $resp_arr= explode("{",$updtresp,2);
                            $json = "{".$resp_arr[1];                               
                            $resp .= "<br>Error at Product'".$itemname."'<br>Karrot API Error:".$json;
                       } 
                    }




                }else{
                   //insert at Shopify //not now
                }
                
            }else{               
               $resp .= "Karrot API Error: Incorrect Response from Fetch API";
            }
        }
        
    }
    
    return $resp;
}

