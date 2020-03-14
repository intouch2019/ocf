<?php
require_once("../../it_config.php");
require_once("../session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "Classes/PHPExcel/IOFactory.php";
require_once "lib/logger/clsLogger.php";

$errormsg = array();
$success = "";
$commit=false;

extract($_POST);
print_r($_POST);
$file = $_FILES['file']['tmp_name'];
print "file-$file<br>";
//$name=$_FILES['file']['name'];
//$npart=explode('.',$name);
//// print_r($npart);
//$ext=$npart[1];
//if(($file)==""){
//    $msg="File Not selected<br>";
//            array_push($errormsg,$msg);
//    }else if (!isset($file) && trim($file) == "") {
//       // print"in if";
//       // $errors['file'] = "File not found";
//          $msg="File Not Present<br>";
//            array_push($errormsg,$msg);
//           // print"$msg";
//    }else if((strcasecmp(trim($ext),'xlsx')!=0) && (strcasecmp(trim($ext),'xls')!=0)){
//        $msg="Incorrect File Type <br>";
//         array_push($errormsg,$msg); 
//    }else {
//    $commit = true;
//}
//   
////if (!isset($file) && trim($file) == "") {
////    $errors['file'] = "File not found";
////} else {
////    $commit = true;
////}
//if (count($errormsg) == 0) {
//    $db = new DBConn();
//    $success .= uploadstock($file,$commit);
//}
//
//if (count($errormsg) > 0) {
//    unset($_SESSION['form_success']);
//    unset($_SESSION['fpath']);
//    $_SESSION['form_errors'] = $errormsg;
//} else {
//    unset($_SESSION['form_errors']);
//    unset($_SESSION['fpath']);
//    $_SESSION['form_success'] = $success;
//    $_SESSION['stockuploaded'] = "done";
//}
//
//session_write_close();
//header("Location: " . DEF_SITEURL . "upload/excel");
//exit;
//
//function uploadstock($stkfile,$commit){
//    $fresp = "<br/>Excel Upload done<br/>";
//    $fh = fopen($stkfile, "r");
//    if (!$fh) { $fresp .= "File not found\n"; return; }
//    
//    $clsLogger = new clsLogger();
//    $db=new DBConn();
//    $objPHPExcel = PHPExcel_IOFactory::load($stkfile);
//    //$objWorksheet = $objPHPExcel->getActiveSheet();
//    $excelsheetarr= $objPHPExcel->getAllSheets();
//    foreach ($excelsheetarr as $objWorksheet){
//        $highestRow = $objWorksheet->getHighestRow(); 
//        $highestColumn = $objWorksheet->getHighestColumn(); 
//        $cnt=0;
//        
//       $sheetname=$objWorksheet->getTitle();
//       // list($dd, $mon)=explode("-",$sheetname);
//       // if($dd<10){
//       //     $dd=day($dd);
//       // }
//       // $mnt=month($mon);
//       // if(trim($mnt)!="" && trim($dd)!=""){
//       // $sheetdate=date('Y-'.$mnt.'-'.$dd)."<br>";
//       // $sheetdate_db=$db->safe(trim($sheetdate));
//       // //print"<br>$sheetdate<br>";
//       // }
//        $prepono="";
//        $prehodate="";
//        for ($row = 1; $row <= $highestRow; ++$row) {
//            $line="";
//            $customer="";
//            $pono ="";
//            $haggarstyle="";
//            $haggarmodel="";
//            $ref="";
//            $colorname="";
//            $poqty="";
//            $cqty="";
//            $ctot="";
//            $siqty="";
//            $sitot="";
//            $soqty="";
//            $sotot="";
//            $fqty="";
//            $ftot="";
//            $hodate="";
//            //print"*************initial-$hodate<br>";
//            $notes="";
//
//            $line= trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue());
//            $customer= trim($objWorksheet->getCellByColumnAndRow(1, $row)->getValue()); 
//            $pono= trim($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
//            $haggarstyle= trim($objWorksheet->getCellByColumnAndRow(3, $row)->getValue());
//            $haggarmodel= trim($objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
//            //$ref= trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue()); 
//            $colorname= trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
//            $poqty= trim($objWorksheet->getCellByColumnAndRow(6, $row)->getValue());
//            $cqty= trim($objWorksheet->getCellByColumnAndRow(7, $row)->getValue());
//            $ctot= trim(str_replace("="," ",($objWorksheet->getCellByColumnAndRow(8, $row)->getValue()))); 
//            $siqty= trim($objWorksheet->getCellByColumnAndRow(9, $row)->getValue());
//            $sitot= trim(str_replace("="," ",($objWorksheet->getCellByColumnAndRow(10, $row)->getValue())));
//            $soqty= trim($objWorksheet->getCellByColumnAndRow(11, $row)->getValue());
//            $sotot= trim(str_replace("="," ",($objWorksheet->getCellByColumnAndRow(12, $row)->getValue())));
//            $fqty= trim($objWorksheet->getCellByColumnAndRow(13, $row)->getValue());
//            $ftot= trim(str_replace("="," ",($objWorksheet->getCellByColumnAndRow(14, $row)->getValue()))); 
//            $hdt= trim($objWorksheet->getCellByColumnAndRow(15, $row)->getValue());
//            //print"hdt=$hdt<br>";
//            if(trim($hdt)!=""){
//                $hodate= date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(15, $row)->getValue())); //doubleval(trim($objWorksheet->getCellByColumnAndRow(15, $row)->getValue()));   
//            }else{
//                $hodate=$prehodate;
//            }
//            $notes= $db->safe(trim($objWorksheet->getCellByColumnAndRow(16, $row)->getValue()));
//            if((strcasecmp(trim($line),"TOTAL"))==0){
////                print"in break<br>";
//                break;
//            }
//            // print"data= $haggarstyle<br>";
//            if(preg_match("/as\s*on(.*)/",$haggarstyle,$matches)){
//                // print_r($matches);
//                $dt=  strtotime($matches[1]);
//                //$sheetdate = DateTime::createFromFormat('d-m-Y',$dt);
//                $sheetdate= date('Y-m-d',$dt);
//                // print"<br>sheetdate=$sheetdate<br>";
//                $sheetdate_db=$db->safe(trim($sheetdate));
////                print"in break<br>";
//              //  break;
//            }
//            if((is_numeric(trim($poqty)))){  
//                if(trim($haggarstyle)!="" && trim($haggarmodel) !=""){
//                    if(trim($pono)==""){
//                       // print"in empty pono<br>";
//                        $pono = $prepono;
//                    }else{
//                         $pono_db=$db->safe($pono);
//                    }              
//                    $hg_style_db=$db->safe($haggarstyle);
//                    $hg_model_db=$db->safe($haggarmodel);
//                    $colorname_db=$db->safe($colorname);
//                    $hodate_db=$db->safe($hodate);
//                    $statuscls="";
//                    if($hodate > $sheetdate){
//                       // print"current<br>";
//                        $statuscls=',status='.POStatus::Current;
//                    }else{
//                      //  print"completed<br>";
//                        $statuscls=',status='.POStatus::Completed;
//                    }
//                    $jobid=0;
//                    $query="select * from job_orders where pono= $pono_db and hg_style= $hg_style_db and hg_model=$hg_model_db";
//                    $jobobj= $db->fetchObject($query);
//                    if(isset($jobobj)){
//                        //if aleready exist job
//                        $jobid= trim($jobobj->id);
//                        //update status always
//                        $updtqry="update job_orders set handover_date=$hodate_db $statuscls where id=$jobid";
//                       // print"updtqry=$updtqry<br>";
//                        $db->execUpdate($updtqry);
//                    }else{
//                        //insert job order
//                        $insqry="insert into job_orders set pono= $pono_db, hg_style= $hg_style_db, hg_model=$hg_model_db,color_no_name=$colorname_db,po_quantity=$poqty,handover_date=$hodate_db,createtime = now() $statuscls";
//                      //  print"insqry=$insqry<br>";
//                        $jobid= $db->execInsert($insqry);
//                    }
//                    if($jobid>0){
//                    //$jobid=1;
//                        //insert job details
//                        $line_db= $db->safe(trim($line));
//                        $notes_db=$db->safe(trim($notes));
//                        if(trim($cqty)!=""){
//                        $insdetlqry1=" insert into job_order_details set job_order_id= $jobid ,line=$line_db,stage=". POStages::CUT.",stage_name='".POStages::getName(POStages::CUT)."',quantity=$cqty,total=$ctot, date=$sheetdate_db,notes=$notes, createtime=now()";
//                        //print"insdetlqry1=$insdetlqry1<br>"; 
//                        $db->execInsert($insdetlqry1);
//                        }
//                        if(trim($siqty)!=""){
//                        $insdetlqry2=" insert into job_order_details set job_order_id= $jobid ,line=$line_db,stage=". POStages::SEWINGINPUT.",stage_name='".POStages::getName(POStages::SEWINGINPUT)."',quantity=$siqty,total=$sitot,date=$sheetdate_db,notes=$notes, createtime=now()";
//                        //print"insdetlqry2=$insdetlqry2<br>";          
//                        $db->execInsert($insdetlqry2);
//                        }
//                        if(trim($soqty)!=""){
//                        $insdetlqry3=" insert into job_order_details set job_order_id= $jobid ,line=$line_db,stage=". POStages::SEWINGOUTPUT.",stage_name='".POStages::getName(POStages::SEWINGOUTPUT)."',quantity=$soqty,total=$sotot,date=$sheetdate_db,notes=$notes, createtime=now()";
//                        //print"insdetlqry2=$insdetlqry3<br>";   
//                        $db->execInsert($insdetlqry3);
//                        }
//                        if(trim($fqty)!=""){
//                        $insdetlqry4=" insert into job_order_details set job_order_id= $jobid ,line=$line_db,stage=". POStages::FINISH.",stage_name='".POStages::getName(POStages::FINISH)."',quantity=$fqty,total=$ftot,date=$sheetdate_db,notes=$notes, createtime=now()";
//                        //print"insdetlqry2=$insdetlqry4<br>";   
//                        $db->execInsert($insdetlqry4);
//                        }
//                    }
////                    print"$line---$pono --- $poqty---$sheetname--$fqty--$ftot****$hodate_db<br>"; 
//                }
//            }else{
//                continue;                 
//            }
//            $prepono = $pono;
//            $prehodate= $hodate;
//            //print"prepono=$prepono<br>";
//        }
//    }
//    fclose($fh);
//    $db->closeConnection();
//   return $fresp;
//}
//    function month($mon) {
//        $mon = trim($mon);
//        $a = array(
//            "Jan" => "01",
//            "Feb" => "02",
//            "MARCH" => "03",
//            "APRIL" => "04",
//            "May" => "05",
//            "Jun" => "06",
//            "Jul" => "07",
//            "Aug" => "08",
//            "Sep" => "09",
//            "Oct" => "10",
//            "Nov" => "11",
//            "Dec" => "12"
//        );
//        return $a[$mon];
//    }
//function day($dd) {
//        $dd = trim($dd);
//        $a = array(
//            "1" => "01",
//            "2" => "02",
//            "3" => "03",
//            "4" => "04",
//            "5" => "05",
//            "6" => "06",
//            "7" => "07",
//            "8" => "08",
//            "9" => "09"
//        );
//        return $a[$dd];
//    }