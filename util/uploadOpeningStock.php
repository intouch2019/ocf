<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/subdealer/clsSubDealer.php";

$errors = array();
$success = "";
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
$subdealer = new clsSubDealer();

$row_no = 1;
$fname = $argv[1];
$distid = $argv[2];
$fileHandle = fopen($fname, "r");
while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no == 1) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";

    $itemcode = isset($row[0]) && trim($row[0]) != "" ? $db->safe($row[0]) : false;
    $batch = isset($row[1]) && trim($row[1]) != "" ? $db->safe($row[1]) : false;
    $qty = isset($row[2]) && trim($row[2]) != "" ? $row[2] : false;

    $query = "select * from it_ttk_items where itemcode = $itemcode";
    $obj_item = $db->fetchObject($query);
    if($qty > 0){
    if($obj_item != NULL && isset($obj_item)){
        $query = "select * from it_opening_mrp where itemcode = $itemcode";
        $obj_mrp = $db->fetchObject($query);
        $mrp = 0;
        if($obj_mrp != NULL && isset($obj_mrp)){
            $mrp = $obj_mrp->mrp;
            $nlc_rate = $obj_mrp->nlcrate;
            $query = "select * from it_nlc_master where nlc_rate = $nlc_rate and itemcode = $itemcode";
            $obj_nlc = $db->fetchObject($query);
            if($obj_nlc == NULL){
                $query = "insert into it_nlc_master set itemid = $obj_item->id, itemcode = $itemcode, batch = $batch, nlc_rate = $nlc_rate, is_active = 1";
                $db->execInsert($query);
            }
        }
        //insert into stockcurrent and stockdiary
        $query = "select * from it_dealer_items where distid = $distid, itemcode = $itemcode";
        echo $query."\n";
        $obj_dist_item = $db->fetchObject($query);
        $dist_item_id = 0;
        if($obj_dist_item == NULL){
            $itemname = $db->safe($obj_item->itemname);
            $query = "insert into it_dealer_items set distid = $distid, itemcode = $itemcode, itemname = $itemname, is_ttk = 1, ttk_item_id = $obj_item->id";
            echo $query."\n";
            $dist_item_id = $db->execInsert($query);
        }else{
            $dist_item_id = $obj_dist_item->id;
        }
        $reason = StockDiaryReason::openingStock;
        $query = "insert into it_dist_stock_diary set dist_id = $distid, item_id = $obj_item->id, itemcode = $itemcode, dist_item_id = $dist_item_id, quantity = $qty, batch = $batch, mrp = $mrp, reason = $reason";
        echo $query."\n";
        $db->execInsert($query);
        
        $query = "insert into it_dist_curr_stock set dist_id = $distid, item_id = $obj_item->id, itemcode = $itemcode, quantity = $qty, batch = $batch, mrp = $mrp";
        echo $query."\n";
        $db->execInsert($query);
    }  }  
}
fclose($fileHandle);
function RemoveBS($Str) {  
    return $Str;
}
?>