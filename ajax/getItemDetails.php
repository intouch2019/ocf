<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$userid = getCurrStoreId();
$user = getCurrStore();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $prodid = isset($_GET['prodid']) ? ($_GET['prodid']) : false;
    if(!$prodid){ $error['prodid'] = "Not able to get Product Id"; }
    
    $itemid = isset($_GET['itemid']) ? ($_GET['itemid']) : false;
    if(!$itemid){ $error['itemid'] = "Not able to get Item Id"; }
    
//    $saledate = isset($_GET['saledate']) ? ($_GET['saledate']) : false;
//    if(isset($saledate)){
//        $saledate = yymmdd($saledate);
//    }
//    if(!$saledate){ $error['saledate'] = "Not able to get sale date"; }
    $usertype = $user->usertype;
    if(count($error) == 0){
        $obj_price = $dbl->fetchProductPriceByProdId($prodid);
        $obj_item = $dbl->getInvItemDetails($itemid,$prodid,$usertype,$userid);
        if($obj_item == NULL){
            $resp = array(
                "error" => "1",
                "msg" => "No Item Found."
            );
        } else if ($obj_price == NULL){
            $resp = array(
                "error" => "1",
                "msg" => "No Price uploaded for this product"
            );
        }else{
            $resp = array(
                "error" => "0",
                "actualrate" => $obj_item->actualrate,
                "mrp" => $obj_item->mrp,
                "totalvalue" => $obj_item->total,
                "batchcode" => $obj_item->batchcode,
                "qty" => $obj_item->qty,
                "todaysprice" => $obj_price->price,
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
