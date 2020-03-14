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
while (($row = fgetcsv($fileHandle, 0, ";")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no == 1) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";
}  

fclose($fileHandle);
function RemoveBS($Str) {  
    return $Str;
}
?>