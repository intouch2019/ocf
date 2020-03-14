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
    $uploaddate = isset($uploaddate) && trim($uploaddate) != "" ? $uploaddate : NULL;

    $crid = isset($crid) && trim($crid) != "" ? $crid : NULL;
    $prodid = isset($prodid) && trim($prodid) != "" ? $prodid : NULL;
    $status = ProductPriceStatus::Approved;
    //echo "CR Id : ".$crid;
    if($crid == NULL || $prodid == NULL){
        $resp = array(
            "error" => "1",
            "msg" => "Please Select CR / Select All / Product not selected"
        );
        echo json_encode($resp);
    }else{
        //$dbl->approveProductPrice($status,$todaysdate,$prodid,$crid,$userid);
        $dbl->approveProductPrice($status,$uploaddate,$prodid,$crid,$userid);
        //approveProductPrice($status,$todaysdate,$crid,$userid);
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
