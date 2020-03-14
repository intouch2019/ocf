<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

extract($_GET);
$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    

    $crid = isset($crid) && trim($crid) != "" ? $crid : NULL;
    $id = isset($id) && trim($id) != "" ? $id : NULL;
    $prodid=isset($prodid) && trim($prodid) != "" ? $prodid : NULL;
    if($crid == NULL){ 
        $resp = array(
            "error" => "1",
            "msg" => "Please Select CR / Select All"
        );
        echo json_encode($resp);
        
    }else{
        $dbl->disapproveStockAdjustment($id,$crid,$userid,$prodid);
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        
       echo json_encode($resp);
       
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
