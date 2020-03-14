<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "api/fetchProdbyHandleAPI.php";

$fetchapicall = new fetchProdbyHandleAPI();
$db = new DBConn();
$dbLogic = new DBLogic();
$cnt = 0;
$resp = "";
//$handle = "tomatoes-red";
$selQ = "select id, category_id, product_handle, name  from it_products";// tomata & white radish tomatoes-red 
//print $selQ;
$pobjs = $db->fetchObjectArray($selQ);
if(isset($pobjs) && !empty($pobjs)){
    foreach($pobjs as $pobj){
//        print $pobj->product_handle;
        $handle = $pobj->product_handle;
        $itemname = $pobj->name;
        $category_id = $pobj->category_id;
        $cnt++;
        //API call 
        $fetchresp ="";
        $fetchresp = $fetchapicall->fetchProdbyHandle($handle); 
        if($fetchresp == ""){
//          print"in error";
            $resp .=  "Error at Product'".$itemname."':<br>Karrot API Error: Empty Responce from Fetch API<br>";
        }else{
            if(preg_match("/.*(200\s*OK).*/",$fetchresp)== TRUE){  
                $variant_id = "";
                $resp_arr= explode("{",$fetchresp,2);
                $json = "{".$resp_arr[1];
//                        print "<br>".$json."<br>";
                $jobj = json_decode($json);
//                print_r($jobj);
                $variantsarr = $jobj->products[0]->variants;
                $prodrefid = trim($jobj->products[0]->id);
                $shopify_handle= trim($jobj->products[0]->handle);
                $prod_name = trim($jobj->products[0]->title);
//                print_r($variantsarr);
                foreach($variantsarr as $variant){
//                    print_r($variant);
                    $var_id = trim($variant->id);   
                    $rate = trim($variant->price);
                    $packsize = trim($variant->sku);
                    $arr = explode(" ",$packsize);
                    $uom = trim($arr[1]);
//                    $uom = trim($variant->weight_unit);
                    $newname= $prod_name." ".$packsize;
//                    $newname_db = $db->safe($newname);
                    
                    $uomobj = $dbLogic->getUOMByName($uom);
                    if(isset($uomobj) && !empty($uomobj) && $uomobj != null){
                       $uom_id = $uomobj->id; 
                    }else{;
                       $uom_id = $dbLogic->insertUOM($uom,-1,-1);
                    }
                    
                    $pszobj = $dbLogic->getPackSize($packsize);
                    if(isset($pszobj) && !empty($pszobj) && $pszobj != null){
                       $pack_size_id = $pszobj->id; 
                    }else{
                       $pack_size_id = $dbLogic->insertPackSize($packsize,-1,-1);
                    }
                    
                    $query = "select id from it_products where product_handle = '$shopify_handle' and variants_id =$var_id limit 1";
                    $vobj = $db->fetchObject($query);
                    
                    if(isset($vobj)){
                        print "\n in update";
                         //$dbLogic->updateProduct($category_id,$uom_id,$pack_size_id,$vobj->id,$rate,$newname,$isactive=false,-1,-1,$json);
                        // purchasing UOM -1 set
                        $dbLogic->updateProduct($category_id,$uom_id,$pack_size_id,$vobj->id,$rate,$newname,$isactive=false,-1,-1,-1,$json);
                    }else{
                        print "\n in insert";
                        $dbLogic->insertProduct($category_id,$uom_id,$pack_size_id,$newname,$json,$prodrefid,$var_id,$shopify_handle,$rate,-1,-1,$prod_name);

                    }
                    //insert or update products
                    //check with varient_id and shopify_handle if exist update 
                    //else insert same product info for multiple varients  
                    //categoty id shopify name and prod_handle remain same prod- name uom packsize and varient id will change
                    //in both cases update portal name like = currentname+packsize
                }
            }   
        }
    }
    print"\n $cnt";
    print"\n $resp"; 
}