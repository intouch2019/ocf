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
    
    $prodid = isset($_GET['prodid']) ? ($_GET['prodid']) : false;
    if(!$prodid){ $error['prodid'] = "Not able to get Product Id"; }
    
    $grnid = isset($_GET['grnid']) ? ($_GET['grnid']) : false;
    if(!$grnid){ $error['grnid'] = "Not able to get grn Id"; }
    
    if(count($error) == 0){
        
        $qty = 0;
        
        $obj = $dbl->getGRNItemInfobyGRNid($prodid,$grnid);
        if($obj != NULL){
            $qty = $obj->qty;
            $resp = array(
                "error" => "0",
                "qty" => $qty
            );
            echo json_encode($resp);
        }else{
            $resp = array(
                "error" => "1",
                "msg" => "Not able to get sum qty Info"
            );
            echo json_encode($resp);
        }
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get sum qty Info"
        );
        echo json_encode($resp);
    }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
