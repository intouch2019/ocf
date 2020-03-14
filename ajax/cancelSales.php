<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $salesid = isset($_GET['salesid']) ? ($_GET['salesid']) : false;
    if(!$salesid){ $error['salesid'] = "Not able to get Sales Id"; }
    
    if(count($error) == 0){
        $invoicestatus = InvoiceStatus::Created;
        $stockdiarystatus = StockDiaryReason::Sale;
        $rows_affected = $dbl->cancelSales($salesid,$userid);
        if($rows_affected <= 0){
            $resp = array(
                "error" => "1",
                "msg" => "Problem while canceling sales transaction"
            );
        }else{
            $resp = array(
                "error" => "0",
                "msg" => "success"
            );
        }
        echo json_encode($resp);
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get Invoice Id"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
