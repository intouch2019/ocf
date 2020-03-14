<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $polineid = isset($_GET['polineid']) ? ($_GET['polineid']) : false;
    if(!$polineid){ $error['polineid'] = "Not able to get PO Line Id"; }

    $billid = isset($_GET['billid']) ? ($_GET['billid']) : false;
    if(!$billid){ $error['billid'] = "Not able to get Bill Id"; }
    
    $obj = $dbl->checkSuppBillItemInserted($polineid,$billid);
    
    if(count($error) == 0){
        if($obj == NULL){
            $obj_poline = $dbl->getPOLineItemById($polineid);
            if($obj_poline != NULL){
                $resp = array(
                    "error" => "0",
                    "product" => $obj_poline->name,
                    "qty" =>  $obj_poline->qty,
                    "rate" =>  $obj_poline->rate,
                    "expected_date" =>  ddmmyy($obj_poline->expected_date),
                    "prodid" => $obj_poline->product_id,
                );
                echo json_encode($resp);
            }else{
                $resp = array(
                    "error" => "1",
                    "msg" => "No details found "
                );
                echo json_encode($resp);
            }
        }else{
            $resp = array(
                "error" => "1",
                "msg" => "Product already added. Delete the added entry and then add again"
            );
            echo json_encode($resp);
        }
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get PO Line Id"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
