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
$errors = array();
try{
    
$db = new DBConn();
$dbl = new DBLogic();

$sheetIndex=0;
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Create a first sheet, representing points details data
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('STC Header Report');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Challan No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'From location');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'To location');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Vehicle No.');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Eway Bill');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Total Qty (MT)');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total Value');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Submit Date');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Pull Date');



$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);




$styleArray = array(
    'font'  => array(
        'bold'  => false,
//        'color' => array('rgb' => 'FF0000'),
        'size'  => 10,
    ));
$objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);  
$objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);


$rowCount=2;
//select c.invoice_no,c.cname,c.cphone,p.name,cl.batchcode,cl.qty,cl.mrp,cl.cuttingcharges,cl.rate,cl.cgst_amt,cl.sgst_amt,cl.total,cl.createtime
$result = $dbl->getSTCHeaderReport();

    $sr_no = 1;
    foreach ($result as $obj) {
       
        $itemDetails = $dbl->getChallanItemDetailsByChallanId($obj->id);
       
        $totat_qty = 0;
        $total_value = 0;
        foreach ($itemDetails as $itemInfo){
            
            $stoDetails = $dbl->getStockTransferInfo($obj->st_id);
            
            $rounde_qty = round($itemInfo->qty, 4, PHP_ROUND_HALF_UP);
            $rounde_qty = sprintf("%.4f", $rounde_qty);
            
            $rounde_rate = round($itemInfo->rate, 2, PHP_ROUND_HALF_UP);
            $rounde_rate = sprintf("%.2f", $rounde_rate);
            
            $totat_qty = $totat_qty + $rounde_qty;
            $total_value = $total_value + ($rounde_rate * $rounde_qty);
    
        }
      
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->challan_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $stoDetails->fromloc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $stoDetails->toloc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->vehicle_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->eway_bill);
        
        $rounde_total_qty = round($totat_qty, 4, PHP_ROUND_HALF_UP);
        $rounde_total_qty = sprintf("%.4f", $rounde_total_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $rounde_total_qty);
        
        $rounde_total_value = round($total_value, 2, PHP_ROUND_HALF_UP);
        $rounde_total_value = sprintf("%.2f", $rounde_total_value);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $rounde_total_value);
        $submit_date = date('d-m-Y', strtotime($obj->submittedDate));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $submit_date);
        if($obj->status == StockTransferChallanStatus::Completed){
            $pull_date = date('d-m-Y', strtotime($obj->pulleddate));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $pull_date);
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, "-");
        }
        
     
        
        $sr_no++;    
        $rowCount++;
    }
 
 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="STC Header Report.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>