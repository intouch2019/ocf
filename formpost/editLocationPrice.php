<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once "lib/db/DBLogic.php";
require_once "lib/core/strutil.php";
require_once "api/updateProductAPI.php";
require_once "api/updateVariantsAPI.php";


$errors = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;    


$success = "";
$db = new DBConn();
$dbl = new DBLogic();
$user = getCurrStore();
$resp = "";
//print_r($user);

try{
    $updatedrow =0;
    if(trim($price)!="" && trim($actvsel)!=""){
        if(trim($price) < 0){
            $errors[] = "Price cannot be negative";
        }
        
        if(!is_numeric($price)){
            $errors[] = "Price should be numeric";
        }
        
        if(count($errors)==0){
            if(trim($location_type_id)==3){ // online
                $appldt = yymmdd($appldt);
                $resp = onlineLocType($pname,$price,$actvsel,$appldt,$location_type_id,$variants_id,$product_ref_id,$lprid);
                if(trim($resp)!=""){
                    $errors[] = $resp;                   
                }else{
                   //  $dbl->updateLocationPrice($lprid,$appldt,$price,$actvsel,$user->id,$user->location_id);
                }
            }else{
            $appldt = yymmdd($appldt);
            $dbl->updateLocationPrice($lprid,$appldt,$price,$actvsel,$user->id,$user->location_id);
            }
        }
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
    $redirect = 'location/price/edit/lprid='.$lprid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    $_SESSION['form_success'] = $success;
    $redirect = 'location/price';
}
//session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;



function onlineLocType($itemname,$price,$is_active,$appldt,$loctype,$variants_id,$product_ref_id,$lprid){
//    print "<br>IN ONLINE FUNCTION <br>";    
    $dbLogic = new DBLogic();
    $user = getCurrStore();     
    $updateapicall = new updateProductAPI();
    $updatevarapicall = new updateVariantsAPI();    
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

    $iobj = $dbLogic->getProductByName($itemname);            
    if(isset($iobj) && !empty($iobj) && $iobj != null){
      
                $prodrefid = $product_ref_id;
                $variant_id = $variants_id;              
                if($prodrefid != "" &&  $variant_id != ""){
                    //update the status
                    $product["id"] = $prodrefid;
                  //  $product["title"] = $itemname;
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

                    $product_array["product"] = $product;
                    //update the status
                    $updtresp ="";                       
                    $updtresp = $updateapicall->updateProduct($prodrefid, $product_array); 
//                            print"<br>up prod resp---$updtresp<br>";
                    //blank chk
                    if($updtresp == ""){
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
                                $dbLogic->updateLocationPrice($lprid,$appldt,$price,$is_active,$user->id,$user->location_id);
                                
                            }
                       }else{
                           //error
                            $resp_arr= explode("{",$updtresp,2);
                            $json = "{".$resp_arr[1];                               
                            $resp .= "<br>Error at Product'".$itemname."'<br>Karrot API Error:".$json;
                       } 
                    }
                }        
    }
    
    return $resp;
}