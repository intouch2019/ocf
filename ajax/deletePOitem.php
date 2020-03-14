<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $itemid = isset($_GET['itemid']) ? ($_GET['itemid']) : false;
    if(!$itemid){ $error['itemid'] = "Not able to get PO Item Id"; }
    
    $poid = isset($_GET['poid']) ? ($_GET['poid']) : false;
    if(!$poid){ $error['poid'] = "Not able to get PO Id"; }
    
    if(count($error) == 0){
      $dbl->deletePOItem($itemid);
      $dbl->updatePOTotals($poid);
      $resp = array(
          "error" => "0",
          "msg" => "success"
      );
      echo json_encode($resp);
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get PO Item Id"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
