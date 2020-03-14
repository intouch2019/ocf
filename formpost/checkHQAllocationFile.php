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
//s
//print_r($_FILES);

$fileName = $_FILES['file']['name'];
$tmpName = $_FILES['file']['tmp_name'];
//$state_id = $_POST['rid'];
$flag=0;
$enr_id = "";
$dir = "../uploads/hqallocation/";

//print "<br> FILE NAME: ".$fileName;
$allotdate = $_POST['allotdt'];
$purchase_in_location_id = $_POST['locsel'] ;
//print "<br>".$allotdate."&".$purchase_in_location_id."<br>";
if(trim($allotdate)=="Select Date"||trim($allotdate)==""|| trim($allotdate)==null){
    $errors["date"] = "Please Select Date";
}
if(trim($purchase_in_location_id)==""|| trim($purchase_in_location_id)==null){
    $errors["location"] = "Please Select Location";
}
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
//    print_r($errors);  
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
  
       
       if (strcmp(strtolower(trim($no_space_value_c1)), "locationname") !== 0){
                 $resp .= "<br/>Column no 1 is not Location Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c2)), "productname") !== 0){
                 $resp .= "<br/>Column no 2 is not Product Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c3)), "reqquantityinpackets") !== 0){
                 $resp .= "<br/>Column no 3 is not Reqired Quantity Name<br/>"; 
       }
       
       if (strcmp(strtolower(trim($no_space_value_c4)), "purpose") !== 0){
                 $resp .= "<br/>Column no 4 is not Purpose<br/>"; 
       }
     
       break;
     }  
  return $resp;  
}

function checkData($newfile){
//    print"in fun";
    $fh = fopen($newfile,"r");
    $row = 1;
    $flag = 1;
    $resp = "";
    $clsloc = new clsLocation();
    $dbl = new DBLogic();
    $purpose_arr= PurposeType::getAll();  
    while(($data=fgetcsv($fh)) !== FALSE) {
//        print"in while";
        if($flag == 1){
           $flag = 2;
           continue;
        }   
        $locname = trim($data[0]);
        $prodname = trim($data[1]);
        $qty= trim($data[2]);       
        $purpose = trim($data[3]);
//        print"pur======$purpose<br>";
        $row++;
        $locid = "";
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
        //chk purpose        
//        print $purpose."<br>";
//        print_r($purpose_arr);
        $purposelist = "";
        foreach($purpose_arr as $ps){
            $purposelist .= ",".$ps;
        }
        $purposelist = ltrim($purposelist,",");
//        print $purposelist."<br>";
        
        if(trim($purpose) == ""){
            $resp .= "<br/>Purpose not given for $prodname.<br/>"; 
        }else{//if(!array_uintersect([trim($purpose)],$purpose_arr,'strcasecmp')){
//            print"<br><br> in else";
            foreach($purpose_arr as $ps){
                $pflag = 0;
                $ps = str_replace(" ","",$ps);   
                $purpose = str_replace(" ","",$purpose);
//                print"<br>pur=$purpose and ps =$ps";
                if(strcasecmp(trim($purpose),trim($ps))==0){
                    $pflag = 0;
//                    print "<br>pflag0=$pflag";
                    break;
                }else{
                    $pflag = 1;
//                    print "<br>pflag1=$pflag";
                }
            }
            if($pflag == 1){
//                print"<br> in pflag = 1";
                $resp .= "<br/>Invalid Purpose for $prodname.Give any of this $purposelist<br/>"; 
            }
        }       
    }
    return $resp;  
}
//print"<br>ERRORS____________________________<br>";
//print_r($errors);
if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "hq/allocation/upload";
    unset($_SESSION['hqallotupload']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);    
    $_SESSION['form_success'] = $success;
    $redirect = "hq/allocation/upload";
    $_SESSION['hqallotupload_fpath']=$newfile;  
    $_SESSION['allotdate']=$allotdate; 
    $_SESSION['purchaselocid']=$purchase_in_location_id;  
    unset($_SESSION['hqallotupload']);
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;
