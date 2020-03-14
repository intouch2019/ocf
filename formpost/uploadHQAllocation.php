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
$allotdate = $_SESSION['allotdate'];
$purchase_in_location_id = $_SESSION['purchaselocid'] ;
//print "<br>".$allotdate."&".$purchase_in_location_id."<br>";
if(trim($allotdate)=="Select Date"||trim($allotdate)==""|| trim($allotdate)==null){
    $errors["date"] = "Please Select Date";
}
if(trim($purchase_in_location_id)==""|| trim($purchase_in_location_id)==null){
    $errors["location"] = "Please Select Location";
}
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
   $fresp = loadFileData($filename,$allotdate,$purchase_in_location_id);
   if(trim($fresp)!=""){
       $errors[] = $fresp;
   }
//   print"<br>ERRORS______________<br>";
//   print_r($errors);
}

if (count($errors) > 0 ) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "hq/allocation/upload";
    unset($_SESSION['hqallotupload']);
    unset($_SESSION['allotdate']);
    unset($_SESSION['purchaselocid']);
} else {
    //echo $tmpName;
    unset($_SESSION['form_errors']);
    unset($_SESSION['creditnote_fpath']);
    unset($_SESSION['allotdate']);
    unset($_SESSION['purchaselocid']);
    $_SESSION['form_success'] = $success;
    $redirect = "hq/allocation";
    $_SESSION['hqallotupload'] = "done";
}
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;


function loadFileData($filepath,$allotdate,$purchase_in_location_id){
//    print"in fun";
    $fh = fopen($filepath,"r");
    $clsloc = new clsLocation();
    $dbl = new DBLogic();
//    $user = getCurrStore();
    $resp = "";
    $row = 1;
    $flag = 1;
    $hqid = 0;
    $insertitmid = 0;
    $tot_qty = 0;
    $rowupdted=0;
    $purpose_arr= PurposeType::getAll();  
    // insert into hq allocation and hq allocation items;
        $hqno = "";
        $hqobj = $dbl->gethqno();
        if(isset($hqobj)){
//            print"found";
            $hqno = trim(str_replace("HQ","",$hqobj->hqno));  
//             print "<br>hqno1====$hqno";
            $hq_no = sprintf('%07d', $hqno+1);
//            print "<br>hqno2====$hq_no";
            $hq_no = "HQ".$hq_no;
//            print "<br>hqno3====$hq_no";
        }else{
//            print"not found<br>";
            $hq_no =  "HQ0000000";    
//            print $hq_no;
        }
        $hqid = $dbl->insertHQAllocation($hq_no,$allotdate,$purchase_in_location_id);
        if($hqid == 0){
            $resp .= "<br/>Failed to insert in Database.<br/>";
        }else{  
            //update  HQNO in table 
            if(isset($hqobj)){
                $rowupdt= $dbl->updatehqno($hq_no);
//                print"<br>********$rowupdt";
            }else{
                $rowid= $dbl->inserthqno($hq_no);
//                print"<br>********$rowid";
            }
            // insert into hq_allocation_items
            while(($data=fgetcsv($fh)) !== FALSE) {
                if($flag == 1){
                   $flag = 2;
                   continue;
                }   
                $locname = trim($data[0]);
                $prodname = trim($data[1]);
                $qty= trim($data[2]);       
                $purpose = trim($data[3]);
                $row++;
                $locid = "";
                $pid = "";
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
                //chk purpose
      //        print $purpose."<br>";
         //     print_r($purpose_arr);
                $purposelist = "";
                foreach($purpose_arr as $ps){
                    $purposelist .= ",".$ps;
                }
                $purposelist = ltrim($purposelist,",");
         //       print $purposelist."<br>";

                if(trim($purpose) == ""){
                    $resp .= "<br/>Purpose not given for $prodname.<br/>"; 
                }else{//if(!array_uintersect([trim($purpose)],$purpose_arr,'strcasecmp')){
//                    print"<br><br> in else";
                    foreach($purpose_arr as $ps){
                        $pflag = 0;
                        $ps = str_replace(" ","",$ps);   
                        $purpose = str_replace(" ","",$purpose);
//                        print"<br>pur=$purpose and ps =$ps";
                        if(strcasecmp(trim($purpose),trim($ps))==0){
                            $pflag = 0;
//                            print "<br>pflag0=$pflag";
                            break;
                        }else{
                            $pflag = 1;
//                            print "<br>pflag1=$pflag";
                        }
                    }
                    if($pflag == 1){
//                        print"<br> in pflag = 1";
                        $resp .= "<br/>Invalid Purpose for $prodname.Give any of this $purposelist<br/>"; 
                    }
                }
                if($resp == ""){
                    foreach($purpose_arr as $purposekey=>$purposeval){
                    //    print "\ndata=".$purpose."=>".$value."\n";
                        $purposeval = str_replace(" ","",$purposeval);
                        if(strcasecmp($purpose, $purposeval)==0){
//                            print "\ndata=".$purposekey."=>".$purposeval."\n";
                            break;
                        }
                    }
                    $insertitmid = $dbl->insertHQAllocationitms($hqid,$pid,$locid,$qty,$purposekey);
                    if($insertitmid == 0){
                        $resp .= "<br/>Failed to insert in Database.<br/>";
                    }else{
                        $tot_qty = $tot_qty + trim($qty); 
//                        $rowupdted= $dbl->updateqty($tot_qty,$hqid);
//                        print "<br>rowupdted=$rowupdted<br>";
                       // add qty and update it into header table
                    }
                }else{
                    return $resp;
                }
            }
            $rowupdted = $dbl->updateqty($tot_qty,$hqid);
            if($rowupdted > 0){
                //insert into it_hq_summary group by product and sum of qty in packet as well as in kg
                //get hqalloctioitems
                $hqitemsobjs = $dbl->getHQAllocationitms($hqid);
                // create summary no product wise
                    $hqsummaryno = "";
                    $hqsumobj = $dbl->gethqsummaryno();
                    if(isset($hqsumobj)){
            //            print"found";
                        $hqsumno = trim(str_replace("HQS","",$hqsumobj->hqsummaryno));  
            //             print "<br>hqno1====$hqno";
                        $hq_sum_no = sprintf('%07d', $hqsumno+1);
            //            print "<br>hqno2====$hq_no";
                        $hq_sum_no = "HQS".$hq_sum_no;
            //            print "<br>hqno3====$hq_no";
                    }else{
            //            print"not found<br>";
                        $hq_sum_no =  "HQS0000000";    
            //            print $hq_no;
                    }
                foreach ($hqitemsobjs as $hqitemsobj ){
                    $insertsummaryid = 0;
                    $prodid = $hqitemsobj->product_id;
                    $t_qty = $hqitemsobj->t_qty;
//                    // create summary no product wise
//                    $hqsummaryno = "";
//                    $hqsumobj = $dbl->gethqsummaryno();
//                    if(isset($hqsumobj)){
//            //            print"found";
//                        $hqsumno = trim(str_replace("HQS","",$hqsumobj->hqsummaryno));  
//            //             print "<br>hqno1====$hqno";
//                        $hq_sum_no = sprintf('%07d', $hqsumno+1);
//            //            print "<br>hqno2====$hq_no";
//                        $hq_sum_no = "HQS".$hq_sum_no;
//            //            print "<br>hqno3====$hq_no";
//                    }else{
//            //            print"not found<br>";
//                        $hq_sum_no =  "HQS0000000";    
//            //            print $hq_no;
//                    }
                    //get pack size of each product
                    $pckobj= $dbl->getProdpacksize($prodid);
                    if(isset($pckobj)){
                        $pc_size = $pckobj->pack_size;
                        $pc_size_arr = explode(" ",$pc_size);
//                        print_r($pc_size_arr);
                        $qty = $pc_size_arr[0];
                        $unit = $pc_size_arr[1];
                        if(trim($unit)==""|| trim($unit)=='gm' || trim($unit)=='gms'){
//                            print"\n convert";
                            $Q_kg= trim($qty)/1000;
//                            print"\n kg=$Q_kg";
                            $tot_kg = $Q_kg * $t_qty;
//                            print"\n total kg=$tot_kg";
                        }else{
                            //packet = kg 
                            $tot_kg = $t_qty;
//                            print"\n not convert";
                        }
                        $insertsummaryid = $dbl->insertHQsummary($hq_sum_no,$hqid,$allotdate,$purchase_in_location_id,$prodid,$t_qty,$tot_kg);
                        if($insertsummaryid == 0){
                            $resp .= "<br/>Failed to insert in Database.<br/>";
                            //update last summary no ;
                        }else{
                           
                        }
                    }
                }     
                
                 if(isset($hqsumobj)){
                                $rowupdt= $dbl->updatehqsno($hq_sum_no);
                //                print"<br>********$rowupdt";
                            }else{
                                $rowid= $dbl->inserthqsno($hq_sum_no);
                //                print"<br>********$rowid";
                            }
            }
//            print "<br>rowupdted=$rowupdted<br>";
        }
     return $resp;  
} 
