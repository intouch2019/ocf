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
$stockist_total_uploaded = 0;
$stockist_total_duplicate = 0;
$stockist_total_failed = 0;
$fname = $argv[1];
$fileHandle = fopen($fname, "r");


while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no == 1) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";

    $locationId = isset($row[0]) && trim($row[0]) != "" ? $db->safe(trim($row[0])) : false;
    $city = isset($row[1]) && trim($row[1]) != "" ? $db->safe(trim($row[1])) : false;
    $state = isset($row[2]) && trim($row[2]) != "" ? $db->safe(trim($row[2])) : false;
    $pinCode = isset($row[3]) && trim($row[3]) != "" ? $db->safe(trim($row[3])) : false;
    $latitude = isset($row[4]) && trim($row[4]) != "" ? $db->safe(trim($row[4])) : false;
    $longitude = isset($row[5]) && trim($row[5]) != "" ? $db->safe(trim($row[5])) : false;

    
    $query = "select * from it_rfc_master where dispname = $locationId";
    $obj_location = $db->fetchObject($query);
    if($obj_location == NULL){

        $addQry = " crcode = 'cr270001', is_auto_price_carryover_set = '0', contact_person = 'abc', address = 'address', gstno = 'gstno', panno = 'panno', state = '22', is_approved = '1', is_imprest_available = '0', createtime=now(), inactive = '0'";

        if($locationId){
            $addQry .= ", dispname = $locationId, rfc_name = $locationId";
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



        $queryInsert = "insert into it_rfc_master set  $addQry";
        $insertedResult = $db->execInsert($queryInsert);
        if($insertedResult > 0){
            $stockist_total_uploaded++;
        }else{
            $stockist_total_failed++;
            echo $queryInsert."\n\n";
            break;
        }

    }else{
        echo "already uploaded - ". $locationId ."\n\n";
        $stockist_total_duplicate++;
    }
}
fclose($fileHandle);

echo "Uploaded Stockist Count- ". $stockist_total_uploaded ."\n\n";
echo "Duplicate Stockist Count- ". $stockist_total_duplicate ."\n\n";
echo "Failed Stockist Count- ". $stockist_total_failed ."\n\n";


function RemoveBS($Str) {  
    return $Str;
}
?>