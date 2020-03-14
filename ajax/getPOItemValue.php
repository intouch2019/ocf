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
    
    $poitemid = isset($_GET['poitemid']) ? ($_GET['poitemid']) : false;
    if(!$poitemid){ $error['poitemid'] = "Not able to get PO Item Id"; }
    
    if(count($error) == 0){
        $length = "";
        $color = "";
        $brand = "";
        $manufacturer = "";
        $qty = 0;
        $calc_no_of_pieces = 0;
        $baserate = 0;
        $lcharge = 0;
        $cgstvalue = 0;
        $sgstvalue = 0;
        $totrate = 0;
        $totvalue = 0;
        $obj = $dbl->getPOItemInfo($poitemid);
        if($obj != NULL){
            $length = $obj->length;
            $color = $obj->color;
            $brand = $obj->brand;
            $manufacturer = $obj->manufacturer;
            $mtqty = $obj->qty;
            $qty = $obj->qtykg;
            $calc_no_of_pieces = $obj->no_of_pieces;
            $baserate = $obj->rate;
            $lcharge = $obj->lcrate;
            $cgstvalue = $obj->cgstval;
            $sgstvalue = $obj->sgstval;
            $totrate = $obj->totalrate;
            $totvalue = $obj->totalvalue;
            $resp = array(
                "error" => "0",
                "length" => $length,
                "color" => $color,
                "brand" => $brand,
                "manufacturer" => $manufacturer,
                "mtqty" => $mtqty,
                "qty" => $qty,
                "pono_of_pieces" => $calc_no_of_pieces,
                "baserate" => $baserate,
                "lcharge" => $lcharge,
                "cgstvalue" => $cgstvalue,
                "sgstvalue" => $sgstvalue,
                "totrate" => $totrate,
                "totvalue" => $totvalue
            );
            echo json_encode($resp);
        }else{
            $resp = array(
                "error" => "1",
                "msg" => "Not able to get POItem Info"
            );
            echo json_encode($resp);
        }
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get POItem Info"
        );
        echo json_encode($resp);
    }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
