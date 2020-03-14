<?php


/*$product_json = '{
  "product": {
    "title": "Intouch Item Push Test",
    "body_html": "<strong>Good !<\/strong>",
    "vendor": "MrKarrot",
    "product_type": "",
    "variants": [
      {
        "title": "50 gms",
        "price": "10.00",
        "sku": "50 gms",
        "position":1,
        "grams":0,
        "inventory_policy":"deny",
        "compare_at_price":null,
        "fulfillment_service":"manual",
        "inventory_management":null,
        "option1":"50 gms",
        "option2":null,
        "option3":null,
        "created_at":"2016-09-01T13:12:26+05:30",
        "updated_at":"2017-05-16T09:39:28+05:30",
        "taxable":true,
        "barcode":"",
        "image_id":null,
        "inventory_quantity":0,
        "weight":0.0,
        "weight_unit":"kg",
        "old_inventory_quantity":0,
        "requires_shipping":false
      }      
    ],
    "options":[

        {           
            "name":"Size",
            "position":1,
            "values":[
                "50 gms"
            ]
        }

    ]
  }
}';
//////////////
 * $product_array = array(
    "product"=>array(
        "title" => "Intouch Item Push Test",
        "body_html" => "",
        "vendor" => "MrKarrot",
        "product_type" => "",
        "published"=> false ,
        "published_at" => null,
        "template_suffix" => null,
          "variants"=>array(
                        array(
                        "title" => "50 gms",                        
                        "price"=> "20.00",
                        "sku"=> "50 gms",    
                        "grams"=>"0",
                        "inventory_policy" => "deny",
                        "compare_at_price" => null,
                        "fulfillment_service" => "manual",
                        "inventory_management" => null,
                        "option1" => "50 gms",
                        "option2" => null,
                        "option3" => null,                        
                        "taxable" => true,
                        "barcode" => "",
                        "image_id"=>null,
                        "inventory_quantity" => null,
                        "weight" => 0.0,
                        "weight_unit" => "kg",
                        "old_inventory_quantity" => null,
                        "requires_shipping" => false
                        )
        )
    )
);

 * 
 *  */

//for real products keep "published_scope":"global", in below array & remove published ->false
$product_array = array(
    "product"=>array(
        "title" => "Intouch redtomato11  prod",
        "body_html" => "",
        "vendor" => "MrKarrot",
        "product_type" => "",
        "published"=> false ,
        "published_at" => null,
        "template_suffix" => null,
        "options" => array(
                    array(
                        "name" => "Weight"
                    )
        ),
          "variants"=>array(
                        array(                                                                                               
                        "option1" => "250 gms",                         
                        "price"=> "20.00",
                        "position"=>"1",    
                        "sku"=> "250 gms",                                                                                                                
                        "taxable" => true,
                        "barcode" => "",
                        "image_id"=>null,
                        "inventory_quantity" => null,
                        "weight" => "250",
                        "weight_unit" => "g",                        
                        "old_inventory_quantity" => null,
                        "requires_shipping" => false
                        )
        )
    )
);

//$products_array = array(
//    "product"=>array(
//        'title'=>'',
//        "title"=> "Intouch Prod Test",
//        "body_html"=> "<strong>Good snowboard!</strong>",
//        "vendor"=> "Intouch",
//        "product_type"=> "Snowboard",
//        "published"=> false ,
//        "variants"=>array(
//                        array(
//                        "sku"=>"t_009",
//                        "price"=>20.00,
//                        "grams"=>200,
//                        "taxable"=>false,
//                        )
//        )
//    )
//);
echo json_encode($product_array);
echo "<br />";


$url = "https://788f6c5b0149dc25bd711a6db3d6fcd2:0cd0ed0f6e696fc8726948c6fa60e352@mrkarrot.myshopify.com/admin/products.json";

$ch = curl_init();
 
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
//print_r($server_output);
echo $server_output;


