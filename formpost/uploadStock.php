<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
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
   $fresp = loadFileData($filename);
   if(trim($fresp)!=""){
       $errors[] = $fresp;
   }
//   print_r($errors);
}

if (count($errors) > 0 ) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "stock/upload";
    unset($_SESSION['stockupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);
    unset($_SESSION['creditnote_fpath']);
    $_SESSION['form_success'] = $success;
    $redirect = "stock";
    $_SESSION['stockupload'] = "done";
}
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;


function loadFileData($filepath){
//    print"in fun";
    $fh = fopen($filepath,"r");
    $clsloc = new clsLocation();
    $dbl = new DBLogic();
    $user = getCurrStore();
    $resp = "";
    $row = 1;
    $flag = 1;
    while(($data=fgetcsv($fh)) !== FALSE) {
        if($flag == 1){
           $flag = 2;
           continue;
        }   
        $locname = $data[0];
        $binname = $data[1];
        $prodname = $data[2];       
        $qty = $data[3];
//        $uom = $data[4];
        $row++;
        $locid = "";
        $uid = 0;
        $pid = "";
        $bid = "";
        //chk location
        if(trim($locname) != ""){
            $lobj = $clsloc->getLocationByname($locname);
            if(isset($lobj) && !empty($lobj)){
                $locid = $lobj->id;
            }else{
                $resp .= "<br/>Invalid Location for '$prodname'. Create Loction First.<br/>"; 
            }
        }else{
             $resp .= "<br/> Empty Location Name for '$prodname'.<br/>"; 
        }
        //chk bin
        if(trim($binname) != "" && trim($locid) !=""){
            $bobj = $dbl->getBinByName($binname,$locid);
            if(!isset($bobj) && empty($bobj)){
                $resp .= "<br/>Invalid Bin for '$prodname'. Create Bin First.<br/>"; 
            }else{
                $bid = $bobj->id;
            }
        }else{
             $resp .= "<br/> Empty Bin Name for '$prodname'.<br/>"; 
        }
        //chk product
        if(trim($prodname) != ""){
           $pobj = $dbl->getProductByName($prodname);
            if(!isset($pobj) && empty($pobj)){
                $resp .= "<br/>'$prodname' Not Exist. Create Product First.<br/>"; 
            }else{
                $pid = $pobj->id;
            }
        }else{
            $resp .= "<br/> Product Name Empty.<br/>"; 
        }
        //chk qty
        if(trim($qty)!=""){
            if(!is_numeric($qty)){
                $resp .= "<br/>Invalid Quantity for $prodname. Enter numeric only.<br/>"; 
            }
        }
        //chk uom
//        $uobj="";
//        if(trim($uom) != ""){
//            $uobj = $dbl->getUOMByName($uom);
//            if(isset($uobj) && !empty($uobj)){
//                $uid = $uobj->id;
//                $puobj ="";
//                $puobj = $dbl->getProductByNameUID($prodname, $uid);
////                print_r($puobj);
//                if(!isset($puobj) && empty($puobj)){
//                    $presentobj="";
//                    $presentobj1="";
                    //chk for  kg if $uom is gms
                    // if $uom is gms then kg conversion is req.  
//                    if($uid == 2){
////                        print"<br>in grm to kg";
//                        //chk for prod uom is kg;
//                        $is_uid = 1;
//                        $presentobj = $dbl->getProductByNameUID($prodname, $is_uid);
//                        if(isset($presentobj) && !empty($presentobj)){
//                            //existing product is in kg do the conversion
//                            //grm to kg
////                            $qty = round(trim($qty)/1000,2);
////                            $uid = 1;
//                             $qty = trim($qty);
//                        }else{
//                            $resp .= "<br/>'$uom' Not Exist for '$prodname'.<br/>";
//                        }
//                    }else if($uid == 1){
////                        print"<br>in kg to grm";
//                        $is_uid = 2;
//                        $presentobj1 = $dbl->getProductByNameUID($prodname, $is_uid);
//                        if(isset($presentobj1) && !empty($presentobj1)){
//                            //existing product is in gms do the conversion
//                            //kg to grm
////                            $qty = trim($qty)*1000;
////                            $uid = 2;
//                            $qty = trim($qty);
//                        }else{
//                            $resp .= "<br/>'$uom' Not Exist for '$prodname'.<br/>";
//                        }
//                    }
//                    else{
//                    $resp .= "<br/>'$uom' Not Exist for '$prodname'.<br/>";
//                    }
//                }  
//            }else{
//                $resp .= "<br/>Invalid UOM for '$prodname'. Create UOM First.<br/>"; 
//            }
//        }else{
//             $resp .= "<br/> Empty UOM for '$prodname'.<br/>";  
//        }
//    } 
//    print "<br>   rsp---$resp<br>";
    if($resp ==""){
//        $uid = 0;
        //select from it_stock_current
        //if exist then update into stock_current
        //else insert into stock_current
//        print"fetch existing stock";
        $sobj = $dbl->getStockByProd($pid,$bid);
//        print_r($sobj);
        if(isset($sobj)){
            //product exist -update
//            print  "update";
            $updtrow=0;
            $sid= $sobj->id;
            $updtrow = $dbl->updateStock($sid,$qty,$user->id,$user->location_id);
//            if($updtrow > 0){
               //insert into stock diary
               $insid = $dbl->insertStockdiary($bid,$pid,$uid,$qty,$user->id,$user->location_id);
//            }
//            if($updtrow == 0){ // 
//               $resp .= "<br/> Can't upload stock for '$prodname'.<br/>"; 
//            }
        }else{
            //insert product
            $insrid=0;
            $insrid = $dbl->insertStock($bid,$pid,$uid,$qty,$user->id,$user->location_id);
//            print $insrid;
            if($insrid > 0){
                //insert into stock diary
                $insid = $dbl->insertStockdiary($bid,$pid,$uid,$qty,$user->id,$user->location_id);
            }else{
                $resp .= "<br/> Can't upload stock for '$prodname'.<br/>"; 
            }
        }
    }
    }
    return $resp;   
} 
