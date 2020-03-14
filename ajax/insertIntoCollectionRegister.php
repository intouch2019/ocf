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
    
    $openingCash = isset($_GET['openingCash']) ? ($_GET['openingCash']) : false;
    $opeingStock = isset($_GET['opeingStock']) ? ($_GET['opeingStock']) : false;
    if(!isset($openingCash) || $openingCash == ""){ $error['openingCash'] = "Not able to get Opening Cash Amount"; }
    if(!isset($opeingStock) || $opeingStock == ""){ $error['opeingStock'] = "Not able to get Opening Stock Quantity"; }
    

 
    if(count($error) == 0){
        $CRobj = $dbl->getCRDetailsByUserId($userid);
//        echo json_encode($CRobj);
//            return;
        if(isset($CRobj)){
            $crid = $CRobj->id;
            $insertId = $dbl->insertIntoCollectionRegister($userid, $crid,$openingCash, $opeingStock);
//            print_r($insertId);
            if($insertId <= 0){
                $resp = array(
                    "error" => "1",
                    "data" => "Enable to insert into database."
                );
            }else{
                $resp = array(
                    "error" => "0",
                    "data" => $insertId
                );
            }
            echo json_encode($resp);
        }
    }else{
        $resp = array(
            "error" => "1",
            "data" => "Error in Open sale"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}

