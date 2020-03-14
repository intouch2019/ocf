<?php
require_once ("lib/db/DBConn.php");

class updateProductAPI {
    
    public function _construct(){
        
    }
    public function updateProduct($id, $product_array){
        $url = DEF_URL."/admin/products/$id.json";
        
//        echo "<br>".$url."<br>";
//        print_r($product_array);
//        return $url;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($product_array));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


        $server_output = trim(curl_exec($ch));

        curl_close ($ch);
//        print_r($server_output);
       // echo "<br>".$server_output."<br>";
        return($server_output);
        }
    }

