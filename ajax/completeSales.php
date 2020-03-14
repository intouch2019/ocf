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
   $collecRegId = isset($_GET['collecRegId']) ? ($_GET['collecRegId']) : false;
   $paymodeId = isset($_GET['paymodeId']) ? ($_GET['paymodeId']) : false;
   $paymentRefNum = $_GET['paymentRefNum'];
   if(!$salesid){ $error['salesid'] = "Not able to get Sales Id"; }
   if(!$collecRegId){ $error['collecRegId'] = "Not able to get Collection Register Id"; }
   if(!$paymodeId){ $error['$paymodeId'] = "Not able to get Payment Mode Details."; }
   if(!isset($paymentRefNum) || $paymentRefNum == ""){ $error['$paymodeId'] = "Not able to get Payment Reference Number."; }
    
    if(count($error) == 0){
        $invoicestatus = InvoiceStatus::Created;
        $stockdiarystatus = StockDiaryReason::Sale;
        $rows_affected = $dbl->completeSales($salesid,$userid,$invoicestatus,$stockdiarystatus,$paymodeId,$collecRegId,$paymentRefNum);
        if($rows_affected <= 0){
            $resp = array(
                "error" => "1",
                "msg" => "Problem while completing sales transaction"
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
