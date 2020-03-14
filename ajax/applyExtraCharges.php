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

    $chargetype = isset($_GET['chargetype']) ? ($_GET['chargetype']) : false;
    //if($chargetype){ $error['chargetype'] = "Please select Debit / Credit Bill"; }
    
    if(count($error) == 0){
        $obj_sale_items = $dbl->getInvoiceItems($salesid,$userid);
        $chargetypeupdate = $dbl->updateInvoicepaymentmode($salesid,$userid,$chargetype);
        $obj_charge = $dbl->getChargeTypeInfo($chargetype);
        $charge_pct = 0;
        if($obj_charge != null){
            $charge_pct = $obj_charge->charge;
        }
        foreach($obj_sale_items as $item){
            $mrp = 0;
            $actual_rate = $item->actualrate;
            $cuttingcharges = $item->cuttingcharges;
            if($cuttingcharges != null && $cuttingcharges > 0){
                $mrp = $actual_rate + $cuttingcharges;
            }else{
                $mrp = $actual_rate;
            }
            $pctvalue = $mrp * $charge_pct;
            $mrp = $mrp + $pctvalue;
            $rows_affected = $dbl->updatetInvoiceItem($userid,$item->product_id,$item->qty,$mrp,$charge_pct,$item->id);
        }
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
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
