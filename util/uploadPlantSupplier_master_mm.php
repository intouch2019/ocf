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
$supplier_total_uploaded = 0;
$supplier_total_duplicate = 0;
$supplier_total_failed = 0;
$fname = $argv[1];
$fileHandle = fopen($fname, "r");


while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no == 1) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";

    $locationId = isset($row[0]) && trim($row[0]) != "" ? $db->safe(trim($row[0])) : false;
    $type = isset($row[1]) && trim($row[1]) != "" ? $db->safe(trim($row[1])) : false;
    $city = isset($row[2]) && trim($row[2]) != "" ? $db->safe(trim($row[2])) : false;
    $state = isset($row[3]) && trim($row[3]) != "" ? $db->safe(trim($row[3])) : false;
    $pinCode = isset($row[4]) && trim($row[4]) != "" ? $db->safe(trim($row[4])) : false;
    $latitude = isset($row[5]) && trim($row[5]) != "" ? $db->safe(trim($row[5])) : false;
    $longitude = isset($row[6]) && trim($row[6]) != "" ? $db->safe(trim($row[6])) : false;

    


    $query = "select * from it_dc_master where dc_name = $locationId";
    $obj_location = $db->fetchObject($query);
    if($obj_location == NULL){

        $addQry = " contact_person = 'abc', address = 'address', gstno = 'gstno', panno = 'panno', state = '22', createtime=now(), inactive = '0'";

        if($locationId){
            $addQry .= ", dc_name = $locationId";
        }
        if($type){
            $addQry .= ", location_type = $type";
        }
        if($city){
            $addQry .= ", city = $city";
        }
        if($pinCode){
            $addQry .= ", pincode = $pinCode";
        }
        if($state){
            $addQry .= ", state_name = $state";
        }
        if($latitude){
            $addQry .= ", latitude = $latitude";
        }
        if($longitude){
            $addQry .= ", longitude = $longitude";
        }


        $queryInsert = "insert into it_dc_master set  $addQry";
        $insertedResult = $db->execInsert($queryInsert);
        if($insertedResult > 0){
            $supplier_total_uploaded++;
        }else{
            $supplier_total_failed++;
            echo $queryInsert."\n\n";
            break;
        }

    }else{
        echo "already uploaded - ". $locationId ."\n\n";
        $supplier_total_duplicate++;
    }
}
fclose($fileHandle);

echo "Uploaded Supplier Count- ". $supplier_total_uploaded ."\n\n";
echo "Duplicate Supplier Count- ". $supplier_total_duplicate ."\n\n";
echo "Failed Supplier Count- ". $supplier_total_failed ."\n\n";


function RemoveBS($Str) {  
    return $Str;
}
?>