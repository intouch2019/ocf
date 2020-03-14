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
    $usertype = $user->usertype;
    if(count($error) == 0){
        $availableqty = 0;
        $batchcode = "";
        $objBatchcodes = $dbl->getBatchCodes($prodid,$usertype,$userid);
        foreach($objBatchcodes as $obj){
            $batch_details[] = array(
          "id" => $obj->id,  
          "batchcode" => $obj->batchcode,
          "qty" => $obj->qty,
        );
        }
        echo json_encode($batch_details);
    }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
