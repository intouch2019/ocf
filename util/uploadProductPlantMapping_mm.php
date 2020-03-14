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
$prod_absent = 0;
$plant_absent = 0;
$uploaded_rows = 0;
$failed_rows = 0;

$fname = $argv[1];
$fileHandle = fopen($fname, "r");


while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no == 1) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";

    $productCode = isset($row[0]) && trim($row[0]) != "" ? $db->safe(trim($row[0])) : false;
    $plantName = isset($row[1]) && trim($row[1]) != "" ? $db->safe(trim($row[1])) : false;

    
    $queryProd = "select id from it_products where name = $productCode";
    $obj_prod = $db->fetchObject($queryProd);

    $queryPlant = "select id from it_dc_master where dc_name = $plantName";
    $obj_plant = $db->fetchObject($queryPlant);

    if($obj_plant == NULL){
        $plant_absent++;
        echo "Plant not found - ". $plantName ."\n\n";
    }

    if($obj_prod == NULL){
        $prod_absent++;
        echo "Product not fount - ". $productCode ."\n\n";
    }


    if($obj_plant != NULL && $obj_prod != NULL){

        $queryUpdate = "update it_products set supplier_dc = $obj_plant->id where id = $obj_prod->id";
        $result = $db->execUpdate($queryUpdate);
        if($result >= 0){
            $uploaded_rows++;
        }else{
            $failed_rows++;
            echo $queryUpdate."\n\n";
            break;
        }
    }
}
fclose($fileHandle);

echo "Product not found count- ". $prod_absent ."\n\n";
echo "Plant not found count- ". $plant_absent ."\n\n";
echo "Failed total count- ". $failed_rows ."\n\n";
echo "Mapped total count- ". $uploaded_rows ."\n\n";



function RemoveBS($Str) {  
    return $Str;
}
?>