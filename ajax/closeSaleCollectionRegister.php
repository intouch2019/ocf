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
    
    $collecRegID = isset($_GET['collecRegID']) ? ($_GET['collecRegID']) : false;
    $closecashAmt = isset($_GET['cashAmt']) ? ($_GET['cashAmt']) : false;
    $debitcardAmt = isset($_GET['debitcardAmt']) ? ($_GET['debitcardAmt']) : false;
    $creditcardAmt = isset($_GET['creditcardAmt']) ? ($_GET['creditcardAmt']) : false;
    $saleCash = isset($_GET['saleCash']) ? ($_GET['saleCash']) : false;
    $closingStock = isset($_GET['closingStock']) ? ($_GET['closingStock']) : false;
    
    if(!$collecRegID){ $error['collecRegID'] = "Not able to get Collection Register Id"; }
    if(!$closingStock){ $error['closingStock'] = "Not able to get Closing Stock"; }
    
    if(count($error) == 0 ){
        
                $insertedID = $dbl->closeSaleCollectionRegInfo($collecRegID,$closecashAmt,$debitcardAmt,$creditcardAmt, $saleCash,$userid,$closingStock);
                if (isset($insertedID)){
                    $resp = array(
                    "error" => "0",
                    "data" => "Success."
                    );
                } else {
                    $resp = array(
                    "error" => "0",
                    "data" => "Error in update."
                    );
                }
              
            echo json_encode($resp);
    }else{
        $resp = array(
            "error" => "1",
            "data" => "Not able to get last collection Information"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}


