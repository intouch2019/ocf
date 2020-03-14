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
    
    if(count($error) == 0){
        
        $CRobj = $dbl->getCRDetailsByUserId($userid);
        if(isset($CRobj)){
            $crid = $CRobj->id;
            $collectionObj = $dbl->getCollectionForOpenSale($crid);
            if(isset($collectionObj)){
                $resp = array(
                    "error" => "0",
                    "data" => $collectionObj
                );
            }else{
                $resp = array(
                    "error" => "1",
                    "data" => "Not able to get last collection Information"
                );
            }
            echo json_encode($resp);
        }
        

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
