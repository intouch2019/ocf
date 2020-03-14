<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
require_once "lib/locations/clsLocation.php";

$errors = array();
$success = "Success";
$fresp = "";

extract($_GET);
//print_r($_GET);
$_SESSION['form_id'] = $form_id;    
if (!isset($filename) && trim($filename) == "") {
    $errors['file'] = "File not found";
}
//$state_id = $_GET['rid'];
$csvAsArray = array_map('str_getcsv', file($filename));
$ext = end((explode(".", $filename)));
//echo "Extension : ".$ext;
if($ext != "csv" ) {
    $errors["name"] = "Please upload .csv file only";
}

if(count($errors)== 0){
   $fresp = loadFileData($filename,$selhub,$appldt);
   if(trim($fresp)!=""){
       $errors[] = $fresp;
   }
//   print_r($errors);
}

if (count($errors) > 0 ) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "hb/stock/upload";
    unset($_SESSION['stockupload']);
} else {
    //echo $tmpName;
    unset($_SESSION["h_sel_hub"]);
    unset($_SESSION["h_sel_dt"]); 
    unset($_SESSION['form_errors']);
    unset($_SESSION['creditnote_fpath']);
    $_SESSION['form_success'] = $success;
    $redirect = "hb/stock/upload";
    $_SESSION['stockupload'] = "done";
}
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;


function loadFileData($filepath,$selhub,$appldt){
//    print"in fun";
    //insert the header
    $user = getCurrStore();
    $db = new DBConn();
    $dt = yymmdd($appldt);  
    $query ="select id from it_hub_stock where location_id = $selhub and stock_date = '$dt'";
    $sobj = $db->fetchObject($query);
    if(isset($sobj) && !empty($sobj) && $sobj != null){
       $stock_id = $sobj->id;    
    }else{
        $query = "insert into it_hub_stock set location_id = $selhub , stock_date = '$dt' , created_by = $user->id , createtime = now() ";
    //    print "<br> query <br> $query";
        $stock_id = $db->execInsert($query); 
    }
    
   
    
    $fh = fopen($filepath,"r");
    $clsloc = new clsLocation();
    $dbl = new DBLogic();
    //$user = getCurrStore();
    $resp = "";
    $row = 1;
    $flag = 1;
    while(($data=fgetcsv($fh)) !== FALSE) {
        if($flag == 1){
           $flag = 2;
           continue;
        }   
//        $locname = $data[0];
//        $binname = $data[1];
        $prodname = $data[0];       
        $qty = $data[1];
//        $uom = $data[4];
        $row++;
        $locid = "";
        $uid = 0;
        $pid = "";
        $bid = "";
 
        if(trim($prodname)!="" && trim($qty)!=""){
            $pobj = $dbl->getProductByName($prodname);
            if(isset($pobj) && ! empty($pobj) && $pobj!= null){
                //insert into stock items
                $qry = "select id from it_hub_stock_items where hub_stock_id = $stock_id and product_id = $pobj->id ";
                $siobj = $db->fetchObject($qry);
                if(isset($siobj) && !empty($siobj) && $siobj != null){
                    //update
                    $qry = "update it_hub_stock_items set qty = $qty where id = $siobj->id ";
                    $db->execUpdate($qry);
                }else{
                    $qry = "insert into it_hub_stock_items set hub_stock_id = $stock_id , location_id = $selhub , product_id = $pobj->id , qty = $qty , created_by = $user->id , createtime = now() , createdat_location_id = $user->location_id ";
//                print "<br>$qry<br>";
                $db->execInsert($qry);
                }
                
            }else{
               $resp .= "<br/>'$prodname' Not Exist. Create Product First.<br/>";  
            }
        }
    
    }
    return $resp;   
} 
