<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$errors = array();
$success = "";
$db = new DBConn();

$row_no = 1;
$warehouse_total_uploaded = 0;
$warehouse_total_duplicate = 0;
$warehouse_total_failed = 0;
$fname = $argv[1];
$fileHandle = fopen($fname, "r");


while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no == 1) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";

    $warehouseId = isset($row[0]) && trim($row[0]) != "" ? trim($row[0]) : false;
    $type = isset($row[1]) && trim($row[1]) != "" ? $db->safe(trim($row[1])) : false;
    $city = isset($row[2]) && trim($row[2]) != "" ? $db->safe(trim($row[2])) : false;
    $state = isset($row[3]) && trim($row[3]) != "" ? $db->safe(trim($row[3])) : false;

    

    $query = "select * from it_dc_master where dc_name = 'WH".$warehouseId."'";
    $obj_location = $db->fetchObject($query);
    if($obj_location == NULL){

        $addQry = " contact_person = 'abc', address = 'address', gstno = 'gstno', panno = 'panno', state = '22', createtime=now(), inactive = '0'";

        if($warehouseId){
            $addQry .= ", dc_name = 'WH".$warehouseId."'";
        }
        if($type){
            $addQry .= ", location_type = $type";
        }
        if($city){
            $addQry .= ", city = $city";
        }
        if($state){
            $addQry .= ", state_name = $state";
        }


        $queryInsert = "insert into it_dc_master set  $addQry";
        $insertedResult = $db->execInsert($queryInsert);
        if($insertedResult > 0){
            $warehouse_total_uploaded++;
        }else{
            $warehouse_total_failed++;
            echo $queryInsert."\n\n";
            break;
        }

    }else{
        echo "already uploaded - ". $warehouseId ."\n\n";
        $warehouse_total_duplicate++;
    }
}
fclose($fileHandle);

echo "Uploaded Warehouse Count- ". $warehouse_total_uploaded ."\n\n";
echo "Duplicate Warehouse Count- ". $warehouse_total_duplicate ."\n\n";
echo "Failed Warehouse Count- ". $warehouse_total_failed ."\n\n";


function RemoveBS($Str) {  
    return $Str;
}
?>