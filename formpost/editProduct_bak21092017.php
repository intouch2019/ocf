<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once "lib/db/DBLogic.php";
require_once "api/updateProductAPI.php";
require_once "api/updateVariantsAPI.php";

$error = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;    
$pid = trim($_POST['prod_id']);
$prodrefid = trim($_POST['prod_ref_id']);
$variant_id = trim($_POST['variant_id']);
$pname= trim($_POST['prodname']);
$rate = trim($_POST['prodrate']);
$uom = trim($_POST['uomsel']);
$uom_arr = explode(":",$uom);
$uomid=  $uom_arr[0];   
$uomnm=  $uom_arr[1]; 
//$uomnm = trim($_POST['uom']);
$category = trim($_POST['catsel']);
$cat_arr = explode(":",$category);
$categoryid=  $cat_arr[0]; 
$categorynm=  $cat_arr[1]; 
//$categorynm = trim($_POST['category']);
$pcksize = trim($_POST['pckszsel']);
//print $pcksize;
$pc_arr= explode(":", $pcksize);
$pcksizeid =$pc_arr[0];
$pcksizenm =$pc_arr[1];
//$pcksizenm = trim($_POST['packsize']);
$isactive = trim($_POST['actvsel']);
//print "selected-".$uomnm .":".$categorynm.":".$pcksizenm;
if($isactive ==1){
    $published= "true";
}else{
    $published= "false";
}

$success = "";
$db = new DBConn();
$dbl = new DBLogic();
$user = getCurrStore();
//print_r($user);
$userid = $user->id;
$updateapicall = new updateProductAPI();
$updatevarapicall = new updateVariantsAPI();
$product_array =array();
$product = array();
$var_arr = array();
$variants = array();
try{
    $updatedrow =0;
    if(trim($pid)=="" && trim($rate)=="" && trim($pname)==""){
        $error['missing_name_rate'] = "Name, Rate can't be Empty";
    }else if(trim($pname)==""){
        $error['missing_name'] = "Enter Product Name";
    }else if(trim($rate)==""){
        $error['missing_rate'] = "Enter Product Rate";
    }else if(! is_numeric(trim($rate))){
        $error['missing_rate'] = "Rate must be in Numeric";
    }else{  
        $pobj = $dbl->getProductByNameId($pname,$pid);
        if($pobj){
             $error['already_exist'] = "$pname product already exist.";
        }else{           
//             $product["id"] = $prodrefid;
//             $product["title"] = $pname;             
//             $product_array["product"] = $product;
//           print_r($product_array);
            //call karrot api set path in it_config
            //if success then update else show error error:Kerrot API Error: error msg 
            //update product    
//            $resp = $updateapicall->updateProduct($prodrefid, $product_array);  
//            echo "<br> response:-".$resp;
             //            if(preg_match("/.*(200\s*OK).*/",$resp)){
//                $updatedrow= $dbl->updateProduct($categoryid,$uomid,$pcksizeid,$pid,$userid,$rate,$pname,$isactive);
////                echo $updatedrow;
//            }else{            
//                $error['server_error'] = "Product $pname Not updated at Shopify";
//            } 
             
           ////////////////////////////////New code//////////////////
             if($prodrefid != "" &&  $variant_id != ""){
//                             print"in update1";
                         //update API 
                        //update shopify data with excel data
                            $product["id"] = $prodrefid;
                            $product["title"] = $pname;
                            $product["body_html"] = "";  
                            $product["vendor"] = "MrKarrot"; 
                            $product["product_type"] = $categorynm; 
                            $product["published"] = $published; 
                            $product["published_at"] = null; 
                            $product["template_suffix"] = null; 

                            $variants["id"] = $variant_id;
                            $variants["title"] = $pcksizenm; 
                            $variants["price"] = $rate;
                            $variants["sku"] = $pcksizenm;  
                            $variants["taxable"] = TRUE; 
                            $variants["barcode"] = ""; 
                            $variants["image_id"] = null; 
                            $variants["inventory_quantity"] = null;
        //                  $variants["weight"] = "0.0"; 
//                          $variants["weight_unit"] = $uomnm; 
                            $variants["old_inventory_quantity"] = null;
                            $variants["requires_shipping"] = FALSE; 
                            
                            $var_arr["variant"] = $variants; 

                            $product_array["product"] = $product;
                            
                            $updtresp ="";                       
                            $updtresp = $updateapicall->updateProduct($prodrefid, $product_array); 
//                            print"<br>up prod resp---$updtresp<br>";
                            //blank chk
//                            $updtresp ="";  
                            if($updtresp == ""){
//                                 print"In error<br>";
                                 //$error[$pname]  = $pname."<br> Karrot API Error: Empty Responce from Update Product API";
                                 $error[$pname]  = "<br>Error at Product'".$pname."'<br> Karrot API Error: Empty Responce from Update Product API";
                            }else{
                                if(preg_match('/.*(200\s*OK).*/',$updtresp)){   
                                    //update variants
                                    $updtvarresp="";
                                    $updtvarresp = $updatevarapicall->updateVariants($variant_id, $var_arr); 
//                                    print"<br> up var resp- $updtvarresp<br>";
                                    if($updtvarresp == ""){
                                      //  $error[$pname] = $pname."<br>Karrot API Error: Empty Responce from Update Variants API";
                                        $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error: Empty Responce from Update Variants API";
                                    }else{
                                        if(preg_match('/.*(200\s*OK).*/',$updtvarresp)){    
//                                                print"<br>update in portal db";           
                                                $dbl->updateProduct($categoryid,$uomid,$pcksizeid,$pid,$rate,$pname,$isactive,$userid,$user->location_id);
                                        }else{  // else missing
                                            $resp_arr= explode("{",$updtvarresp,2);
                                            $json = "{".$resp_arr[1];
                                          //  $error[$pname] = $pname."<br>".$json;
                                           // $error[$pname] = $pname."<br>Karrot API Error:".$json;
                                            $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error:".$json;
                                        }
                                    }
                                }else{  
                                    $resp_arr= explode("{",$updtresp,2);
                                    $json = "{".$resp_arr[1];
                                   // $error[$pname] = $json;
                                    $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error:".$json;
                                }
                            }                          
                        }         
        }       
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print "<br>Errors---";
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'product/edit/pid='.$pid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    $_SESSION['form_success'] = $success;
    $redirect = 'products';
}
//session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;