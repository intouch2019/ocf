<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once "lib/db/DBLogic.php";
require_once "api/fetchProdbyHandleAPI.php";
require_once "api/updateProductAPI.php";
require_once "api/updateVariantsAPI.php";
require_once "api/createProductAPI.php";

$error = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;    
$pid = trim($_POST['prod_id']);
$prodrefid = trim($_POST['prod_ref_id']);
$variant_id = trim($_POST['variant_id']);
$pname= trim($_POST['prodname']);
$shopifyname= trim($_POST['shopifyname']);
$producthandle = trim($_POST['producthandle']); 
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
$pruomsel = trim($_POST['pruomsel']);
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
$_SESSION['form_post'] = $_POST;
$success = "";
$db = new DBConn();
$dbl = new DBLogic();
$user = getCurrStore();
//print_r($user);
$userid = $user->id;
$fetchapicall = new fetchProdbyHandleAPI();
$updateapicall = new updateProductAPI();
$updatevarapicall = new updateVariantsAPI();
$createapicall = new createProductAPI();
$product_array =array();
$product = array();
$var_arr = array();
$variants = array();
$options = array();
$opt_arr = array();
$no = 0;
$i = 0;//index for respose
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
    }else if(trim($pruomsel)==""){
        $error['missing_uom'] = "Select purchasing UOM";
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
             if($prodrefid != "" &&  $variant_id != ""  && $prodrefid != 0 &&  $variant_id != 0 ){
//                        print"in update1";
                         //update API 
                        //update shopify data with excel data
                            $product["id"] = $prodrefid;
                           // $product["title"] = $pname; 
                            $product["title"] = $shopifyname;
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
                         //   if($updtresp == ""){
//                        //         print"In error<br>";
                           //      $error[$pname]  = "<br>Error at Product'".$pname."'<br> Karrot API Error: Empty Responce from Update Product API";
                         //   }else{
//                                if(preg_match('/.*(200\s*OK).*/',$updtresp)){   
//                                    //update variants
//                                    $updtvarresp="";
//                                    $updtvarresp = $updatevarapicall->updateVariants($variant_id, $var_arr); 
////                                    print"<br> up var resp- $updtvarresp<br>";
//                                    if($updtvarresp == ""){
//                                        $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error: Empty Responce from Update Variants API";
//                                    }else{
//                                        if(preg_match('/.*(200\s*OK).*/',$updtvarresp)){    
////                                                print"<br>update in portal db";  
                                            //update prod_ref_id and variant_id in db
                                                $dbl->updateProduct($categoryid,$uomid,$pcksizeid,$pid,$rate,$pname,$isactive,$userid,$user->location_id,$pruomsel);
//                                        }else{  // else missing
//                                            $resp_arr= explode("{",$updtvarresp,2);
//                                            $json = "{".$resp_arr[1];
//                                            $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error:".$json;
//                                        }
//                                    }
//                                }// if not found then create product at shopify
//                                else{  
//                                    $resp_arr= explode("{",$updtresp,2);
//                                    $json = "{".$resp_arr[1];
//                                    $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error:".$json;
//                                }
                        //    }                          
                        }else{
                            // fetch product by handle
                            //if found fetch prod_ref_id and variant id from resp and update in portal db
                            // if not found create product bu create API call
                            // then update prod_ref_id and variant id from resp and update in portal db

                          //  $handle = str_replace(" ","-", $pname); 
                            $handle = $producthandle;
            //                print "<br>handle=$handle<br>";
                            $fetchresp ="";
                            $fetchresp = $fetchapicall->fetchProdbyHandle($handle); 
                            if($fetchresp == ""){
            //                    print"in error";
                                $error[$pname] ="Error at Product'".$pname."':<br>Karrot API Error: Empty Responce from Fetch API<br>";
            //                    array_push($issue_arr,$issue);
                            }else if(preg_match("/.*(200\s*OK).*/",$fetchresp)== TRUE){                                                    
                                // get ref_id of existing product for updation 
                                $prodrefid = "";
                                $variant_id = "";
                                $handle = "";
                                $resp_arr= explode("{",$fetchresp,2);
                                $json = "{".$resp_arr[1];
        //                        print "<br>".$json."<br>";
                                $jobj = json_decode($json);
        //                        print_r($jobj);
                                if(isset($jobj->products[$i]->id)){
                                    $prodrefid= $jobj->products[$i]->id;
                //                    print"<br> ref_id=$prodrefid"; 
                                    $variant_id= $jobj->products[$i]->variants[$i]->id;
            //                        print "<br> variants-$variant_id<br>";
                                    $handle= $jobj->products[$i]->handle;
                //                    print"<br> handle=$handle";
                                    $no = $dbl->updateProductInfo($pid,$prodrefid,$variant_id,$handle);
                                    if($no >0 ){
                                        //                        print"in update1";
                                         //update API 
                                        //update shopify data with excel data
                                            $product["id"] = $prodrefid;
                                            //$product["title"] = $pname;
                                            $product["title"] = $shopifyname;
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
                                                 $error[$pname]  = "<br>Error at Product'".$pname."'<br> Karrot API Error: Empty Responce from Update Product API";
                                            }else{
                                                if(preg_match('/.*(200\s*OK).*/',$updtresp)){   
                                                    //update variants
                                                    $updtvarresp="";
                                                    $updtvarresp = $updatevarapicall->updateVariants($variant_id, $var_arr); 
                //                                    print"<br> up var resp- $updtvarresp<br>";
                                                    if($updtvarresp == ""){
                                                        $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error: Empty Responce from Update Variants API";
                                                    }else{
                                                        if(preg_match('/.*(200\s*OK).*/',$updtvarresp)){    
                //                                                print"<br>update in portal db";  
                                                            //update prod_ref_id and variant_id in db
                                                                $dbl->updateProduct($categoryid,$uomid,$pcksizeid,$pid,$rate,$pname,$isactive,$userid,$user->location_id);
                                                        }else{  // else missing
                                                            $resp_arr= explode("{",$updtvarresp,2);
                                                            $json = "{".$resp_arr[1];
                                                            $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error:".$json;
                                                        }
                                                    }
                                                }// if not found then create product at shopify
                                                else{  
                                                    $resp_arr= explode("{",$updtresp,2);
                                                    $json = "{".$resp_arr[1];
                                                    $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error:".$json;
                                                }
                                            }
                                    }
                                } else{
                                            //insert API
                    //                    print"<br>new product";
                                      //  $product["title"] = $pname;
                                        $product["title"] = $shopifyname;
                                        $product["body_html"] = "";  
                                        $product["vendor"] = "MrKarrot"; 
                                        $product["product_type"] = $category; 
                                        $product["published"] = FALSE; 
                                        $product["published_at"] = null; 
                                        $product["template_suffix"] = null; 
                                        
                                        $options["name"] = "Weight";
                                        
                                        $variants["option1"] = $packsize; 
                                        //$variants["title"] = $packsize; 
                                        $variants["price"] = $rate;
                                        $variants["position"] = 1; 
                                        $variants["sku"] = $packsize;  
                                        $variants["taxable"] = TRUE; 
                                        $variants["barcode"] = ""; 
                                        $variants["image_id"] = null; 
                                        $variants["inventory_quantity"] = null;
                    //                    $variants["weight"] = "0.0"; 
                    //                    $variants["weight_unit"] =; 
                                        $variants["old_inventory_quantity"] = null;
                                        $variants["requires_shipping"] = FALSE; 
                    //                  
                                        $opt_arr[0] = $options;
                                        $var_arr[0]= $variants;
                                        $product["options"] = $options;
                                        $product["variants"] = $var_arr;  

                                        $product_array["product"] = $product;
                //                        print_r($product_array); 

                                        $insrtresp = $createapicall->createProduct($product_array); 
                //                        print "<br>resp---------$insrtresp<br>";
                                        //blank chk
                                        if(preg_match("/.*(201\s*Created).*/",$insrtresp)){
                //                            print"<br>insert in portal db";
                                            $resp_arr= explode("{",$insrtresp,2);
                                            $json = "{".$resp_arr[1];
                                            $obj = json_decode($json);
                                            $ref_id= $obj->product->id;
                                            $variant_id = $obj->product->variants[$i]->id;
                                            $handle= $obj->product->handle;
                //                            print"<br> handle=$handle";
                                           // $dbLogic->insertProduct($category_id,$uom_id,$pack_size_id,$pname,$json,$ref_id,$variant_id,$handle,$rate,$userid,$user->location_id);
                                            $dbl->updateProduct($categoryid,$uomid,$pcksizeid,$pid,$rate,$pname,$isactive,$userid,$user->location_id);
                                            $dbl->updateProductInfo($pid,$ref_id,$variant_id,$handle);
                                        }else{
                                            $resp_arr= explode("{",$insrtresp,2);
                                            $json = "{".$resp_arr[1];
        //                                    $issue[$pname] = $json;
                                            $error[$pname] = "<br>Error at Product'".$pname."'<br>Karrot API Error:".$json;
                                        }
                                }
                            }else if(preg_match("/.*(not found)i.*/",$fetchresp)== TRUE){
                                      //insert API
            //                    print"<br>new product";
                               // $product["title"] = $pname;
                                $product["title"] = $shopifyname;
                                $product["body_html"] = "";  
                                $product["vendor"] = "MrKarrot"; 
                                $product["product_type"] = $category; 
                                $product["published"] = FALSE; 
                                $product["published_at"] = null; 
                                $product["template_suffix"] = null; 
                                
                                $options["name"] = "Weight";
                                
                                $variants["option1"] = $packsize; 
                                //$variants["title"] = $packsize; 
                                $variants["price"] = $rate;
                                $variants["position"] = 1; 
                                $variants["sku"] = $packsize;  
                                $variants["taxable"] = TRUE; 
                                $variants["barcode"] = ""; 
                                $variants["image_id"] = null; 
                                $variants["inventory_quantity"] = null;
            //                    $variants["weight"] = "0.0"; 
            //                    $variants["weight_unit"] =; 
                                $variants["old_inventory_quantity"] = null;
                                $variants["requires_shipping"] = FALSE; 
            //                  
                                $opt_arr[0] = $options;
                                $var_arr[0]= $variants;
                                $product["options"] = $options;
                                $product["variants"] = $var_arr;  

                                $product_array["product"] = $product;
        //                        print_r($product_array); 

                                $insrtresp = $createapicall->createProduct($product_array); 
        //                        print "<br>resp---------$insrtresp<br>";
                                //blank chk
                                if(preg_match("/.*(201\s*Created).*/",$insrtresp)){
        //                            print"<br>insert in portal db";
                                    $resp_arr= explode("{",$insrtresp,2);
                                    $json = "{".$resp_arr[1];
                                    $obj = json_decode($json);
                                    $ref_id= $obj->product->id;
                                    $variant_id = $obj->product->variants[$i]->id;
                                    $handle= $obj->product->handle;
        //                            print"<br> handle=$handle";
                                   // $dbLogic->insertProduct($category_id,$uom_id,$pack_size_id,$pname,$json,$ref_id,$variant_id,$handle,$rate,$userid,$user->location_id);
                                    $dbl->updateProduct($categoryid,$uomid,$pcksizeid,$pid,$rate,$pname,$isactive,$userid,$user->location_id);
                                    $dbl->updateProductInfo($pid,$ref_id,$variant_id,$handle);
                                }else{
                                    $resp_arr= explode("{",$insrtresp,2);
                                    $json = "{".$resp_arr[1];
//                                    $issue[$pname] = $json;
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
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'products';
}
//session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
