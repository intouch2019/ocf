<?php
require_once ("lib/db/DBConn.php");

class createProductAPI {
    
    public function _construct(){
        
    }
    public function createProduct($product_array){
        //for real products keep "published_scope":"global", in below array & remove published ->false
        //$product_array = array(
        //    "product"=>array(
        //        "title" => "Intouch Item Push Test",
        //        "body_html" => "",
        //        "vendor" => "MrKarrot",
        //        "product_type" => "",
        //        "published"=> false ,
        //        "published_at" => null,
        //        "template_suffix" => null,
        //          "variants"=>array(
        //                        array(
        //                        "title" => "50 gms",                        
        //                        "price"=> "20.00",
        //                        "sku"=> "50 gms",                                                                                                                
        //                        "taxable" => true,
        //                        "barcode" => "",
        //                        "image_id"=>null,
        //                        "inventory_quantity" => null,
        //                        "weight" => 0.0,
        //                        "weight_unit" => "kg",
        //                        "old_inventory_quantity" => null,
        //                        "requires_shipping" => false
        //                        )
        //        )
        //    )
        //);

    //$url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/products.json";
    $url = DEF_URL."/admin/products.json";
//    return $url;
    $ch = curl_init();
//
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($product_array));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = trim(curl_exec($ch));

    curl_close ($ch);
//    print_r($server_output);
//    echo $server_output;
    return($server_output);
    }
  }