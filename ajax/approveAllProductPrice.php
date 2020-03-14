<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
extract($_GET);
$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    //$todaysdate = date("Y-m-d");

    $crid = isset($crid) && trim($crid) != "" ? $crid : NULL;
    $uploaddate = isset($uploaddate) && trim($uploaddate) != "" ? $uploaddate : NULL;
    $status = ProductPriceStatus::Approved;
    //echo "CR Id : ".$crid;
    if($crid == NULL){ 
        $resp = array(
            "error" => "1",
            "msg" => "Please Select CR / Select All"
        );
        echo json_encode($resp);
        
    }else{
       // $dbl->approveAllProductPrice($status,$todaysdate,$crid,$userid);
        $dbl->approveAllProductPrice($status,$uploaddate,$crid,$userid);
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
