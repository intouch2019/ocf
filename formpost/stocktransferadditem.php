<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {

    $transferid = isset($transferid) && trim($transferid) != "" ? intval($transferid) : false;
    if (!$transferid) {
        $error['missing_grnid'] = "Not able to get Stock Transfer Reference";
    }
    
    if(isset($check1)){
        $transferIn = 0;
    }
    
    if(isset($check2)){
        $transferIn = 1;
    }
    
    $arr = explode("::", $transferitem);
    $grnlineid = $arr[0];
    $prodid = $arr[1];
  
 
    $qty = isset($qty) && trim($qty) != "" ? trim($qty) : false;
    if (!$qty) {
        $qty  = isset($qty2) && trim($qty2) != "" ? trim($qty2) : false;
        if(!$qty){
        $error['missing_qty'] = "Enter qty to Transfer";
        }
    }

    $obj_prod = $dbl->getProdSupplier($prodid);

    $obj_st = $dbl->getStockTransferDetails($transferid);
    $from_loc_id = $obj_prod->supplier_dc;
    $obj_po_alloc = $dbl->getPoAllocationDetails($transferid,$from_loc_id);

    // $qrySel3 = "select to_location_id, transferno from it_stock_transfer where id = $transferid";
    // $obj_st = $db->fetchObject($qrySel3);

    
    // $qrySel2 = "select id from it_po_allocation where transferid = $transferid and from_location_id = $from_loc_id";
    // $obj_po_alloc = $db->fetchObject($qrySel2);


    if(!isset($obj_po_alloc) || $obj_po_alloc == "" || $obj_po_alloc == null){ 
        $status = POAllocationStatus::BeingCreated;
        $qrySel4 = "select id, allocation_num from it_po_allocation where transferid = $transferid order by id desc";
        $obj_po_alloc_num = $db->fetchObject($qrySel4);
        print_r($qrySel4);
        if(!isset($obj_po_alloc_num) || $obj_po_alloc_num == "" || $obj_po_alloc_num == null){
            $alloc_num = $obj_st->transferno."-1";
        }else{
            $st_num_arr = explode("-", $obj_po_alloc_num->allocation_num);
            $num = $st_num_arr[3];
            $num = $num+1;
            $alloc_num = $obj_st->transferno."-".$num;
        }
        $to_location_id = $obj_st->to_location_id;
        $inserted_id = $dbl->createPoAllocation($transferid,$from_loc_id,$to_location_id,$qty,$status,$alloc_num);
        // $insertQry = "insert into it_po_allocation set transferid = '$transferid', from_location_id = '$from_loc_id', to_location_id = '$to_location_id', order_qty = '$qty', status = '$status', allocation_num = '$alloc_num', createtime = now(), updatetime = now()";
        // $inserted_id = $db->execInsert($insertQry);

        $fullfilled_qty = 0;
        $stritem_id = $dbl->insertPoAllocationItem($prodid,$qty,$fullfilled_qty,$inserted_id);
        // $queryItem4 = "insert into it_po_allocation_items set prodid = '$prodid', order_qty = '$qty', fullfilled_qty = '$fullfilled_qty', po_allocation_id = '$inserted_id'";
        // $stritem_id = $db->execInsert($queryItem4);

    }else{
        $fullfilled_qty = 0;
        $stritem_id2 = $dbl->insertPoAllocationItem($prodid,$qty,$fullfilled_qty,$obj_po_alloc->id);

        $update_id2 = $dbl->updatePoAllocationTotalQty($qty,$obj_po_alloc->id);
    }
    if (count($error) == 0) {
        $stocktransferitem_id = $dbl->insertStockTransferItem($transferid,$prodid,$qty);
    }

    $db->closeConnection();
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'stocktransfer/additem/transferid=' . $transferid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "stocktransfer/additem/transferid=" . $transferid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
