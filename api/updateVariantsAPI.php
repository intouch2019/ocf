<?php
require_once ("lib/db/DBConn.php");

class updateVariantsAPI {
    
    public function _construct(){
        
    }
    public function updateVariants($id, $product_array){ 
//        $url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/variants/$id.json";
        $url = DEF_URL."/admin/variants/$id.json";
        //echo "<br>".$url."<br>";
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
        //print_r($server_output);
//        echo $server_output;
        return($server_output);

    }
}




