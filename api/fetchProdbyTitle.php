<?php
$itemname = "Intouch Item Name Test";
$itemname = "Brinjal Kateri";
$handle = "intouch-prod-test";
$handle = "alfa-alfa-sprouts";
$handle = "brinjal-kateri";
$handle = "brinjaltst-kateri";
$handle = "tomatoes-red";
$handle = "intouch-potato";
//$id = "6757716419";
//$url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/products.json?title=$itemname";
$url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/products.json?handle=$handle";
//$url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/products.json?id=$id";
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
echo $server_output;

//$i = 0;//index for respose
//$prodrefid = "";
//$variant_id = "";
//$handle = "";
//$resp_arr= explode("{",$server_output,2);
//$json = "{".$resp_arr[1];
//$jobj = json_decode($json);

//print "<br><br> JSOn DECODE: ";
//print_r($jobj);
//if(isset($jobj->products[$i]->id)){
//    print "<br> IN IF";
//}else{
//    print "<br> IN ELSE";
//}

