<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/db/DBLogic.php';
require_once "lib/locations/clsLocation.php";

$errors = array();
$success = "File is Valid. Do you want to continue";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
//$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;

//print_r($_POST);
//
//print_r($_FILES);

$fileName = $_FILES['file']['name'];
$tmpName = $_FILES['file']['tmp_name'];
//$state_id = $_POST['rid'];
$flag=0;
$enr_id = "";
$dir = "../uploads/stock/";

//print "<br> FILE NAME: ".$fileName;

$ext = end((explode(".", $fileName)));
$arr = array();
$err = "";
$estr = "Error. Please upload valid file. Below are the error(s) found.";
if($ext != "csv" ) {
    $errors["name"] = "Please upload .csv file only";
}else{
    $newfile = $dir."stock_".$userid."_" . date("Ymd-His") . ".csv";
    if (!move_uploaded_file($tmpName, $newfile)) {
        $errors['fileerr'] = "File unable to load";
    }else{
        $err .= checkSequenceFile($newfile);
        if(trim($err)!=""){
            $errors['chkfile']= $estr.$err;
//                $errors[]= $err;
        }
        $err .= checkData($newfile);
        if(trim($err)!=""){
            $errors['chkfile']= $estr.$err;
//                $errors[]= $err;
        }     
    }  
   // print_r($errors);  
}

function checkSequenceFile($file){
    $fh = fopen($file,"r");
    $resp = "";    
   while(($data=fgetcsv($fh)) !== FALSE) {
       $col1 = $data[0];
       $no_space_value_c1 = str_replace(" ", "", $col1);
       $col2 = $data[1];
       $no_space_value_c2 = str_replace(" ", "", $col2);
       $col3 = $data[2];
       $no_space_value_c3 = str_replace(" ", "", $col3);
       $col4 = $data[3];
       $no_space_value_c4 = str_replace(" ", "", $col4);
//       $col5 = $data[4];
//       $no_space_value_c5 = str_replace(" ", "", $col5);
       
       
       if (strcmp(strtolower(trim($no_space_value_c1)), "locationname") !== 0){
                 $resp .= "<br/>Column no 1 is not Location Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c2)), "binname") !== 0){
                 $resp .= "<br/>Column no 2 is not Bin Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c3)), "productname") !== 0){
                 $resp .= "<br/>Column no 3 is not Product Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c4)), "currentqty(inpackets)") !== 0){
                 $resp .= "<br/>Column no 4 is not Current Qty ( In Packets )<br/>"; 
       }
       
//       if (strcmp(strtolower(trim($no_space_value_c5)), "uom") !== 0){
//                 $resp .= "<br/>Column no 5 is not UOM<br/>"; 
//       }
       
       break;
     }  
  return $resp;  
}

function checkData($newfile){
    $fh = fopen($newfile,"r");
    $row = 1;
    $flag = 1;
    $resp = "";
    $clsloc = new clsLocation();
    $dbl = new DBLogic();
    while(($data=fgetcsv($fh)) !== FALSE) {
        if($flag == 1){
           $flag = 2;
           continue;
        }   
        $locname = trim($data[0]);
        $binname = trim($data[1]);
        $prodname = trim($data[2]);       
        $qty = trim($data[3]);
//        $uom = trim($data[4]);
        $row++;
        $locid = "";
        $uid = "";
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
            }
        }else{
             $resp .= "<br/> Empty Bin Name for '$prodname'.<br/>"; 
        }
        //chk product
        if(trim($prodname) != ""){
           $pobj = $dbl->getProductByName($prodname);
//           print "productid<br>";
//           print_r($pobj);
           if(!isset($pobj) && empty($pobj)){
                $resp .= "<br/>'$prodname' Not Exist. Create Product First.<br/>"; 
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
//        if(trim($uom) != ""){
//            $uobj = $dbl->getUOMByName($uom);
//            if(isset($uobj) && !empty($uobj)){
//                $uid = $uobj->id;
//                $puobj = $dbl->getProductByNameUID($prodname, $uid);
//                if(!isset($puobj) && empty($puobj)){
//                    if($uid == 2){
//                        //chk for prod uom is kg;
//                        $is_uid = 1;
//                        $presentobj = $dbl->getProductByNameUID($prodname, $is_uid);
//                        if(isset($presentobj) && !empty($presentobj)){
//                            //do nothing
//                        }else{
//                            $resp .= "<br/>'$uom' Not Exist for '$prodname'.<br/>";
//                        }
//                    }else if($uid == 1){
//                        $is_uid = 2;
//                        $presentobj = $dbl->getProductByNameUID($prodname, $is_uid);
//                        if(isset($presentobj) && !empty($presentobj)){
//                            //do nothing
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
    }
    return $resp;  
}

if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "stock/upload";
    unset($_SESSION['stockupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);    
    $_SESSION['form_success'] = $success;
    $redirect = "stock/upload";
    $_SESSION['stockupload_fpath']=$newfile;     
    unset($_SESSION['stockupload']);
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;
