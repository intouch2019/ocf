<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

$userid = getCurrStoreId();
$user = getCurrStore();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $data = [];

    $obj_products = $dbl->getAllActiveProducts();

    foreach ($obj_products as $prod) {
        $desc1 = isset($prod->desc1) && trim($prod->desc1) != "" ? " , " . $prod->desc1." mm" : "";
        $desc2 = isset($prod->desc2) && trim($prod->desc2) != "" ? " x " . $prod->desc2." mm": "";
        $thickness = isset($prod->thickness) && trim($prod->thickness) != "" ? " , " . $prod->thickness." mm" : "";
        $productName = $prod->prod . $desc1 . $desc2 . $thickness;


        $data[] = [ 'value' => $productName ,
                    'id' => $prod->id
                    ];
        // $data[]['id'] = $prod->id;
        // $data[]['name'] = $productName;
    }

    // print_r($data);
    echo json_encode($data);
    

}catch(Exception $xcp){
    print($xcp->getMessage());
}
