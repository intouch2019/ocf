<?php
require_once ("lib/db/DBConn.php");

class fetchProdbyHandleAPI {
    
    public function _construct(){
        
    }
    public function fetchProdbyHandle($handle){
        //$itemname = "Intouch Item Name Test";
        //$handle = "intouch-prod-test";
        //$handle = "alfa-alfa-sprouts";
        //$url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/products.json?title=$itemname";
        // $url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/products.json?handle=$handle";
        $url = DEF_URL."/admin/products.json?handle=$handle";
       // echo $url;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($product_array));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


        $server_output = trim(curl_exec($ch));

        curl_close ($ch);
        //print_r($server_output);
//        echo $server_output;
        return($server_output);
    }
}