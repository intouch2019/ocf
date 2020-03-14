<?php
//$id = "10888527747";
//$product_array = array(
//    "product"=>array(
//        "id" => "10888527747",
//        "title" => "Intouch tomato product",
//        "variants" => array(array(
//            'id' => "44867792835",
//            'product_id' => $id,                                                      
//            'price' => "30"
//        ))
//        )    
//);

//$id = "44867792835";
$id = "20793867331";
$product_array = array(
  "variant" => array(
    "id" => "20793867331",    
    "price" => "15.00"
    //"sku"=> "550 gms"  
  )
);

print "<br>";
print_r($product_array);
print "<br>";

$pjson = json_encode($product_array);
print "<br>";
print_r($pjson);
print "<br>";

$url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/variants/$id.json";
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
echo $server_output;




