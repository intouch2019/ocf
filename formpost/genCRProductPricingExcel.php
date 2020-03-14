<?php

ini_set('max_execution_time', 300);
ini_set('memory_limit', '1024M');
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once 'lib/db/DBLogic.php';
require_once "lib/core/strutil.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once  'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

extract($_GET);


$curr = getCurrStore();
$currusertype = $curr->usertype;
$CRid=isset($_GET['CRid'])?$_GET['CRid']:false;

$errors = array();
try{
    
$db = new DBConn();
$dbl = new DBLogic();
$crinfo=$dbl->getCrinfoByid($CRid);
$crname=$crinfo->dispname;
$sheetIndex=0;
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Create a first sheet, representing points details data
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('Product Pricing Details');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. no');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Category');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Name');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Desc 1');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Desc 2');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Thickness');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Length');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Price');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Date');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);


$sr_no = 1;  
$rowCount=2;
$result1=$dbl->getmaxAppDateBycr($CRid);
foreach ($result1 as $obj1) {
            $applicableDate = $obj1->applicable_date;
            $prodid = $obj1->product_id;
            $time = strtotime($applicableDate);
            $newapplicableDate = date('Y-m-d',$time);
            $today=date('Y-m-d',time());

            if($newapplicableDate == $today){
               
                 

     $result = $dbl->getProductPricingReportDetails($prodid,$applicableDate,$CRid);
     if(isset($result)){
         foreach ($result as $obj) {
             $ctid=$obj->ctg_id;
             $ctresult=$dbl->getCategoryByid($ctid);
             $ctname=$ctresult->name;

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $ctname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->desc1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->desc2);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->thickness);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->stdlength);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->price);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->applicable_date);

     
        
        $sr_no++;    
        $rowCount++;
         }
    }
            }
            
}
 $fname=$crname." Product Pricing Report";
 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename= "' . $fname . '.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>