<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$userid = getCurrStoreId();
$user = getCurrStore();
$crid = $user->crid;
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $prodid = isset($_GET['prodid']) ? ($_GET['prodid']) : false;
    if(!$prodid){ $error['prodid'] = "Not able to get Product Id"; }
    if(count($error) == 0){
        // $obj_price = $dbl->fetchProductPriceByProdId($prodid);
        $obj_price = $dbl->fetchProductPriceByProdIdCrid($prodid, $crid);
        if($obj_price == NULL){
            $resp = array(
                "error" => "1",
                "msg" => "No Price uploaded for this product"
            );
        }else{
            $resp = array(
                "error" => "0",
                "msg" => $obj_price->price,
                "kgperpc" => $obj_price->kg_per_pc
            );
        }
        echo json_encode($resp);
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get Prod Id"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
