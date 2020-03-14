<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';

$error = array();
extract($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$dbl = new DBLogic();
$count = 0;
try{
    
    $fileName = $_FILES['csv']['name'];
    $tmpName = $_FILES['csv']['tmp_name'];

    $row_no = 1;
    $error_msg = "";
    $fileHandle = fopen($tmpName, "r");
    if($tmpName == NULL){
        $error["missing_file"] = "Please upload the product file";
    }
    if(count($error) == 0){
    while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
        //echo "here second loop<br>";
        $error_msg = "";
        if($row_no == 1) { $row_no++; continue; }
        
        $srno = isset($row[0]) && trim($row[0]) != "" ? $row[0] : false;
        $category = isset($row[1]) && trim($row[1]) != "" ? $row[1] : false;
        if(!$category){
            $error_msg .= "Missing category";
        }
        $proddesc = isset($row[2]) && trim($row[2]) != "" ? $row[2] : false;
        if(!$proddesc){
            $error_msg .= "Missing product description";
        }
        $shortname = isset($row[3]) && trim($row[3]) != "" ? $row[3] : false;
        if(!$shortname){
            $error_msg .= "Missing Short Form";
        }
        $desc1 = isset($row[4]) && trim($row[4]) != "" ? $row[4] : false;
        /*if(!$desc1){
            $error_msg .= "Missing Description 1";
        }*/
        $desc2 = isset($row[5]) && trim($row[5]) != "" ? $row[5] : false;
        /*if(!$desc2){
            $error_msg .= "Missing Description 2";
        }*/
        $thickness = isset($row[6]) && trim($row[6]) != "" ? $row[6] : false;
        /*if(!$thickness){
            $error_msg .= "Missing Thickness";
        }*/
        $stdlength = isset($row[7]) && trim($row[7]) != "" ? $row[7] : false;
        if(!$stdlength){
            $error_msg .= "Missing Standard Length";
        }
        $specification = isset($row[8]) && trim($row[8]) != "" ? $row[8] : false;
        if(!$specification){
            $error_msg .= "Missing Specification";
        }
        $hsncode = isset($row[9]) && trim($row[9]) != "" ? $row[9] : false;
        if(!$hsncode){
            $error_msg .= "Missing Hsncode";
        }
        $stdkg = isset($row[10]) && trim($row[10]) != "" ? $row[10] : false;
        if(!$stdkg){
            $error_msg .= "Missing Standard Kg";
        }
        
        if($error_msg != ""){
            $error[$row_no] = "Error at Row : ".$row_no."=>".$error_msg."\n";
        }
        $row_no++;
    }
    }

    if(count($error) == 0){    
        $row_no = 1;
        $fileHandle1 = fopen($tmpName, "r");    
        while (($row = fgetcsv($fileHandle1, 0, ",")) !== FALSE) {
            //echo "here second loop<br>";
            if($row_no == 1) { $row_no++; continue; }

            $srno = isset($row[0]) && trim($row[0]) != "" ? $row[0] : false;
            $category = isset($row[1]) && trim($row[1]) != "" ? trim($row[1]) : false;
            $proddesc = isset($row[2]) && trim($row[2]) != "" ? trim($row[2]) : false;
            $shortname = isset($row[3]) && trim($row[3]) != "" ? trim($row[3]) : false;
            $desc1 = isset($row[4]) && trim($row[4]) != "" ? trim($row[4]) : false;
            $desc2 = isset($row[5]) && trim($row[5]) != "" ? trim($row[5]) : false;
            $thickness = isset($row[6]) && trim($row[6]) != "" ? trim($row[6]) : false;
            $stdlength = isset($row[7]) && trim($row[7]) != "" ? trim($row[7]) : false;
            $specification = isset($row[8]) && trim($row[8]) != "" ? trim($row[8]) : false;
            $hsncode = isset($row[9]) && trim($row[9]) != "" ? trim($row[9]) : false;
            $std_kg_per_pc = isset($row[10]) && trim($row[10]) != "" ? trim($row[10]) : false;
            
            $ctg_id = 0; $prod_id = 0; $spec_id = 0;
            if(isset($category)){
                $obj_category = $dbl->getCategoryByName($category);
                if(isset($obj_category) && $obj_category != NULL){
                    $ctg_id = $obj_category->id;
                }else{
                    $ctg_id = $dbl->insertCategory($category,$userid);
                }
            }

            if(isset($specification) && trim($specification) != ""){
                $obj_specification = $dbl->getSpecificationByName($specification);
                if(isset($obj_specification) && $obj_specification != NULL){
                    $spec_id = $obj_specification->id;
                }else{
                    $spec_id = $dbl->insertSpecification($specification, $userid);
                }
            }
            
            if(isset($proddesc)){
                $obj_product = $dbl->getProduct($proddesc,$shortname,$desc1,$desc2,$thickness);
                if(isset($obj_product) && $obj_product != NULL){
                    $count++;
                    //echo $count;
                    $prod_id = $obj_product->id;
                    //$dbl->updateProduct($ctg_id, $proddesc, $spec_id, $hsncode, $std_kg_per_pc, $userid, $prod_id);
                    $dbl->updateProduct($ctg_id, $proddesc, $shortname, $desc1, $desc2, $thickness, $stdlength, $spec_id, $hsncode, $std_kg_per_pc, $userid, $prod_id);
                }else{
                    //$prod_id = $dbl->insertProduct($ctg_id, $proddesc, $spec_id, $hsncode, $std_kg_per_pc, $userid);
                   $prod_id = $dbl->insertProduct($ctg_id, $proddesc, $shortname, $desc1, $desc2, $thickness, $stdlength, $spec_id, $hsncode, $std_kg_per_pc, $userid);
                }
            }
        }
    }
    
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'products/upload';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "products";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;