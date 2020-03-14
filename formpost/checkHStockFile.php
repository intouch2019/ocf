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

       if (strcmp(strtolower(trim($no_space_value_c1)), "productname") !== 0){
                 $resp .= "<br/>Column no 1 is not Product Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c2)), "currentstock") !== 0){
                 $resp .= "<br/>Column no 2 is not Current Stock<br/>"; 
       }       
       
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

        $prodname = trim($data[0]);       
        $qty = trim($data[1]);
//        $uom = trim($data[4]);
        $row++;
        $locid = "";
        $uid = "";

        if(trim($prodname)!="" && trim($qty)!=""){
           $pobj = $dbl->getProductByName($prodname);

           if(!isset($pobj) && empty($pobj)){
                $resp .= "<br/>'$prodname' Not Exist. Create Product First.<br/>"; 
            }  
            
            
            if(!is_numeric($qty)){
                $resp .= "<br/>Invalid Quantity for $prodname. Enter numeric only.<br/>"; 
            }
        } 
    }
    return $resp;  
}

if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "hb/stock/upload";
    unset($_SESSION['stockupload']);
} else {
    //echo $tmpName;
    $_SESSION["h_sel_hub"] = $selhub;
    $_SESSION["h_sel_dt"] = $appldt;
    unset($_SESSION['form_errors']);    
    $_SESSION['form_success'] = $success;
    $redirect = "hb/stock/upload";
    $_SESSION['stockupload_fpath']=$newfile;     
    unset($_SESSION['stockupload']);
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;
