<?php

require_once("../../it_config.php");
//require_once("/var/www/html/sarotam/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";


$error = array();
$db = new DBConn();
$dbl = new DBLogic();

try {

    $currStockQuery = "select * from it_stockcurr";
    $currStockObjs = $db->fetchObjectArray($currStockQuery);
    if (isset($currStockObjs)) {

        $count = 0;
        foreach ($currStockObjs as $stockobj) {
            $dcid = $stockobj->dcid;
            $crid = $stockobj->crid;
            $prodid = $stockobj->prodid;
            $batchcode = $stockobj->batchcode;
            $qty = $stockobj->qty;
            $today = date("Y-m-d");
            
            $id = $dbl->pushDateIntoClosingStockTable($dcid,$crid,$prodid,$batchcode,$qty,$today);
//            return;
            $count++;
        }
    }
} catch (Exception $ex) {
    
}


