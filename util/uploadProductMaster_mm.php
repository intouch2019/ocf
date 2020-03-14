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
$prod_total_uploaded = 0;
$prod_total_duplicate = 0;
$prod_total_dailed = 0;
$fname = $argv[1];
$fileHandle = fopen($fname, "r");


while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
    //echo "here second loop<br>";
    if($row_no <= 2) { $row_no++; continue; }
    $error_row = $row_no;  
    $row_no++;
    $error_msg = "";

    $prodCode = isset($row[0]) && trim($row[0]) != "" ? $db->safe(trim($row[0])) : false;
    $cv = isset($row[1]) && trim($row[1]) != "" ? $db->safe(trim($row[1])) : false;
    $pac = isset($row[2]) && trim($row[2]) != "" ? $db->safe(trim($row[2])) : false;
    $ea = isset($row[3]) && trim($row[3]) != "" ? $db->safe(trim($row[3])) : false;
    $prodWeight = isset($row[4]) && trim($row[4]) != "" ? $db->safe(trim($row[4])) : false;
    $price = isset($row[5]) && trim($row[5]) != "" ? $db->safe(trim($row[5])) : false;
    $attribute = isset($row[6]) && trim($row[6]) != "" ? trim($row[6]) : false;

    // echo $pac."\n\n";
    // break;

    $category_id = 0;
    $category = "";
    $catArr = explode("/",$attribute);

    if(preg_match( '/^\/+.*/', $attribute)){
        $category = trim(str_replace("/","",$attribute));
        $query = "select id from it_categories where name = '$category'";
        // echo $category."\n\n";
    }else{
        $category = trim($attribute);
        $query = "select id from it_categories where name = '$category'";
    }

    $obj_category = $db->fetchObject($query);
    // return;
    if(isset($obj_category) && $obj_category != NULL){
        $category_id = $obj_category->id;
    }else{
        $queryInsert = "insert into it_categories set name = '$category', createtime = now()";
        $insertedId = $db->execInsert($queryInsert);
        $category_id = $insertedId;
    }
    // echo "cat id - ". $query ."\n\n";
    // print_r($category_id);

    $category_id_db = $db->safe($category_id);
    $query = "select * from it_products where name = $prodCode";
    $obj_product = $db->fetchObject($query);
    if($obj_product == NULL){

        $addQry = " hsncode = '7306', stdlength = '6.00', createtime = now(), updatetime = now(), spec_id = '1', ctg_id = $category_id_db";

        if($prodCode){
            $addQry .= ", code = $prodCode, name = $prodCode, shortname = $prodCode";
        }
        if($cv){
            $addQry .= ", desc1 = $cv";
        }
        if($pac){
            $addQry .= ", desc2 = $pac";
        }
        if($prodWeight){
            $addQry .= ", thickness = $prodWeight";
        }
        if($ea){
            $addQry .= ", kg_per_pc = $ea";
        }
        if($price){
            $addQry .= ", price = $price";
        }



        $query = "insert into it_products set  $addQry";
        $insertedResult = $db->execInsert($query);
        if($insertedResult > 0){
            $prod_total_uploaded++;
        }else{
            $prod_total_dailed++;
            echo $query."\n\n";
            break;
        }

        

    }else{
        echo "already uploaded - ". $prodCode ."\n\n";
        $prod_total_duplicate++;
    }
}
fclose($fileHandle);

echo "Uploaded Product Count- ". $prod_total_uploaded ."\n\n";
echo "Duplicate Product Count- ". $prod_total_duplicate ."\n\n";
echo "Failed Product Count- ". $prod_total_dailed ."\n\n";


function RemoveBS($Str) {  
    return $Str;
}
?>