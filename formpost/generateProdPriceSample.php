<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="productpricing.csv"');
 

header('Pragma: no-cache');
header('Expires: 0');

$file = fopen('php://output', 'w');

$db = new DBConn();
$dbl = new DBLogic();

$obj_products = $dbl->getAllActiveProducts();
$srno = 1;

fputcsv($file,array("Sl No","Product Id","Category","Product Description","Short Form","Description 1 (If Dimension then in mm)","Description 2 (If Dimension then in mm)","Thickness (mm)",
    "Default Standard Length (Meter)","Default Specification","Default HSN Code","Standard (MT)","Price"));

foreach($obj_products as $prod){
    $specname = "";
    if($prod->spec_id > 0){
        $obj_spec = $dbl->getSpecificationById($prod->spec_id);
        if($obj_spec != NULL && isset($obj_spec)){
            $specname = $obj_spec->name;
        }
    }
    fputcsv($file, array($srno,$prod->id,$prod->ctg,$prod->prod,$prod->shortname,$prod->desc1,$prod->desc2,$prod->thickness,$prod->stdlength,$specname,$prod->hsncode,$prod->kg_per_pc,""));
    $srno++;
}
exit();
?>