<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
//require_once "lib/subdealer/clsSubDealer.php";
require_once "lib/core/Constants.php";
//require_once "lib/distributor/clsDistributor.php";
require_once "api/fetchProdbyHandleAPI.php";
require_once "api/updateProductAPI.php";
require_once "api/createProductAPI.php";
require_once "api/updateVariantsAPI.php";

$errors = array();
$success = "Success";
//$db = new DBConn();
//$dbl = new DBLogic();


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
   $errors = loadFileData($filename);
}

if (count($errors) > 0 ) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "product/upload";
    unset($_SESSION['productupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);
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
    $dbLogic = new DBLogic();
    $fetchapicall = new fetchProdbyHandleAPI();
    $updateapicall = new updateProductAPI();
    $updatevarapicall = new updateVariantsAPI();
    $createapicall = new createProductAPI();
    $product_array = array();
    $product = array();
    $var_arr = array();
    $variants = array();
    $user = getCurrStore();
    $userid = $user->id;
//    print $userid;
    $issue= array();
    $resp = "";
    $row = 1;
    $flag = 1;
    $i = 0;//index for respose
//    $errors = array();
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
            //call fetchbyhandle api
            //if success call updateapi to update existing product with our info then update in portal db
            //else call insertapi to insert at their location then insert into portal db  
            //insert product

            if(trim($category_id)!="" && trim($category_id)!="-1" && trim($uom_id)!="" && trim($uom_id)!="-1" && trim($pack_size_id)!="" && trim($pack_size_id)!="-1" ){
                // create handle 
                $handle = str_replace(" ","-", $itemname);
//                print "<br>handle=$handle<br>";
                $fetchresp ="";
                $fetchresp = $fetchapicall->fetchProdbyHandle($handle); 
//                echo "<br>fetch resp----$fetchresp<br>";
                //blank check // throw api err msg
                if($fetchresp == ""){
                    $issue[$itemname] ="Karrot API Error: Empty Responce from Fetch API";
//                    array_push($issue_arr,$issue);
                }else{
                    if(preg_match("/.*(200\s*OK).*/",$fetchresp)== TRUE){                                                    
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
                        }
                        if($prodrefid != "" &&  $variant_id != "" && $handle != ""){
//                             print"in update1";
                         //update API 
                        //update shopify data with excel data
                            $product["id"] = $prodrefid;
                            $product["title"] = $itemname;
                            $product["body_html"] = "";  
                            $product["vendor"] = "MrKarrot"; 
                            $product["product_type"] = $category; 
                            $product["published"] = FALSE; 
                            $product["published_at"] = null; 
                            $product["template_suffix"] = null; 

                            $variants["id"] = $variant_id;
                            $variants["title"] = $packsize; 
                            $variants["price"] = $rate;
                            $variants["sku"] = $packsize;  
                            $variants["taxable"] = TRUE; 
                            $variants["barcode"] = ""; 
                            $variants["image_id"] = null; 
                            $variants["inventory_quantity"] = null;
        //                  $variants["weight"] = "0.0"; 
//                          $variants["weight_unit"] = $uom; 
                            $variants["old_inventory_quantity"] = null;
                            $variants["requires_shipping"] = FALSE; 

//                            $var_arr[0]= $variants;
                            $var_arr["variant"] = $variants; 

                            $product_array["product"] = $product;
                            
                            $updtresp ="";                       
                            $updtresp = $updateapicall->updateProduct($prodrefid, $product_array); 
//                            print"<br>up prod resp---$updtresp<br>";
                            //blank chk
                            if($updtresp == ""){
//                                 print"In error<br>";
                                 $issue[$itemname]  ="Karrot API Error: Empty Responce from Update Product API";
                            }else{
                                if(preg_match('/.*(200\s*OK).*/',$updtresp)){   
                                    //update variants
                                    $updtvarresp="";
                                    $updtvarresp = $updatevarapicall->updateVariants($variant_id, $var_arr); 
//                                    print"<br> up var resp- $updtvarresp<br>";
                                    if($updtvarresp == ""){
                                        $issue[$itemname] = "Karrot API Error: Empty Responce from Update Variants API";

                                    }else{
                                        if(preg_match('/.*(200\s*OK).*/',$updtvarresp)){    
                                            //check product present in portal db 
                                            //if present then update else insert in db
                                            $pobj = $dbLogic->getProductByName($itemname);
                                            if(isset($pobj) && !empty($pobj) && $pobj != null){
//                                                print"<br>update in portal db";           
                                                $dbLogic->updateProduct($category_id,$uom_id,$pack_size_id,$pobj->id,$rate,$userid,$user->location_id);
                                            }else{
//                                                print"<br>in insert product in portal db";
                                                $dbLogic->insertProduct($category_id,$uom_id,$pack_size_id,$itemname,$userid,$json,$prodrefid,$variant_id,$handle,$rate,$userid,$user->location_id);
                                            }
                                        }else{  // else missing
                                            $resp_arr= explode("{",$updtvarresp,2);
                                            $json = "{".$resp_arr[1];
                                            $issue[$itemname] = $json;
                                        }
                                    }
                                }else{  
                                    $resp_arr= explode("{",$updtresp,2);
                                    $json = "{".$resp_arr[1];
                                    $issue[$itemname] = $json;
                                }
                            }                          
                        }else{
                              //insert API
    //                    print"<br>new product";
                        $product["title"] = $itemname;
                        $product["body_html"] = "";  
                        $product["vendor"] = "MrKarrot"; 
                        $product["product_type"] = $category; 
                        $product["published"] = FALSE; 
                        $product["published_at"] = null; 
                        $product["template_suffix"] = null; 

                        $variants["title"] = $packsize; 
                        $variants["price"] = $rate;
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
                        $var_arr[0]= $variants;
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
                            $dbLogic->insertProduct($category_id,$uom_id,$pack_size_id,$itemname,$json,$ref_id,$variant_id,$handle,$rate,$userid,$user->location_id);
                        }else{
                            $resp_arr= explode("{",$insrtresp,2);
                            $json = "{".$resp_arr[1];
                            $issue[$itemname] = $json;
                        }
                          
                        }
                    }else{
                         $issue[$itemname] ="Karrot API Error: Incorrect Response from Fetch API";
                    }
                }
            }else{
                $skipped_cnt++;
            }
          
        }
    }  
  if(count($issue)!=0){
//      print"<br>Error---<br>";
//      print_r($issue);
        return $issue ; 
  }else{ 
        return $resp;  
  }
} 
//key parir array msg
//err msg prefix "Karrot API err"
