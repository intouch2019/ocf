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
    
    if(!$collecRegID){ $error['collecRegID'] = "Not able to get Collection Register Id"; }
    
    if(count($error) == 0 ){
        
        $collectionobj = $dbl->getSaleCollectionByCollectionRegId($collecRegID);
            if(isset($collectionobj)){
                $resp = array(
                    "error" => "0",
                    "data" => $collectionobj
                );
            }else{
                $resp = array(
                    "error" => "1",
                    "data" => "Not able to get last collection Information"
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

