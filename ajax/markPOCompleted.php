<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$poid = isset($_GET['poid']) ? ($_GET['poid']) : false;
$lid = isset($_GET['lid']) ? ($_GET['lid']) : false;
$po_date = isset($_GET['po_date']) ? ($_GET['po_date']) : false;
$ex_date = isset($_GET['ex_date']) ? ($_GET['ex_date']) : false;

if (!$poid) { return error("missing parameters"); }
if (!$lid) { return error("missing parameters"); }
if (!$po_date) { return error("missing parameters"); }
if (!$ex_date) { return error("missing parameters"); }
$cnt=0;
try{
    $user = getCurrStore();
    $db = new DBConn();
    $lcode = "";
    $dt = date("Ymd-His");
    //fetch location code 
    $query = "select id,location_code,name from it_locations where id = $lid ";
    $lobj = $db->fetchObject($query);
    if(isset($lobj) && !empty($lobj) && $lobj!= null){
       if(trim($lobj->location_code)!=""){
         $lcode = $lobj->location_code;  
       }else{
           str_replace(" ", "", $lobj->name);
       } 
    }else{
        $lcode = $lid;
    }
    
    $po_no = $lcode."_".$dt;
    
    $query = "update it_purchase_orders set po_no = '$po_no' , status = ".POStatus::Completed." where id = $poid";
    //print "<br> $query ";
    $db->execUpdate($query);
    
    //do insert in po_account table
    $dayofweek = date('w', strtotime($ex_date));
    $ex_date = yymmdd($ex_date);
    $query = "insert into it_po_day_account set po_id = $poid , pur_location_id = $lid , po_no = '$po_no' , po_date = '$po_date', execution_date = '$ex_date' , execution_day = $dayofweek , status = ".POStatus::Completed." , created_by = $user->id , createtime = now() , createdat_location_id = $user->location_id ";
    $db->execInsert($query);
    
    unset($_SESSION['form_id']);
    
    $resp = array(
        "error" => "0",
        "msg" => "success",
        "pono" => $po_no
    );
    echo json_encode($resp);
    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
