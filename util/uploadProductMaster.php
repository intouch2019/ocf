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
$fname = $argv[1];
$fileHandle = fopen($fname, "r");
$id = 1;
$code = 1;
$reference = 1;
$ctg_id = 1;
while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no == 1) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";

    $id_db = $db->safe($id);
    $code_db = $db->safe($code);
    $reference_db = $db->safe($reference);
    $category = isset($row[0]) && trim($row[0]) != "" ? $db->safe(trim($row[0])) : false;
    $product = isset($row[1]) && trim($row[1]) != "" ? $db->safe(trim($row[1])) : false;
    $specification = isset($row[2]) && trim($row[2]) != "" ? $db->safe(trim($row[2])) : false;

    $category_id = 0;
    $query = "select * from categories where name = $category";
    $obj_category = $db->fetchObject($query);
    if(isset($obj_category) && $obj_category != NULL){
        $category_id = $obj_category->ID;
    }else{
        $ctg_id_db = $db->safe($ctg_id);
        $query = "insert into categories set ID = $ctg_id_db , NAME = $category";
        $db->execInsert($query);
        $category_id = $ctg_id;
        $ctg_id = $ctg_id + 1;
    }
    $category_id_db = $db->safe($category_id);
    $query = "select * from products where NAME = $product";
    $obj_product = $db->fetchObject($query);
    if($obj_product == NULL){
        $query = "insert into products set ID = $id_db, REFERENCE = $reference_db, CODE = $code_db, NAME = $product, PRICEBUY = 0, PRICESELL = 0, CATEGORY = $category_id_db, "
                . "TAXCAT = '1', CATEGORY_NAME = $category, SPECIFICATIONS = $specification, UOM = 'KG'";
        echo $query."\n\n";
        $db->execInsert($query);
        $query = "insert into products_cat set PRODUCT = $id_db";
        $db->execInsert($query);
    }
    $id++;
    $code++;
    $reference++;
}
fclose($fileHandle);
function RemoveBS($Str) {  
    return $Str;
}
?>