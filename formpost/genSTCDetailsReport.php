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
$objPHPExcel->getActiveSheet()->setTitle('STC Details Report');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Challan No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'ST No.');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'From location');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'To location');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Vehicle NO');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Eway Bill');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Product Name');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'HSN Code');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Batchcode');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Length');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'No. of pieces');
$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Quantity (MT)');
$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Rate/MT');
$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Total Value');
$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Submit Date');
$objPHPExcel->getActiveSheet()->setCellValue('P1', 'Pull Date');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);


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
$objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('P')->applyFromArray($styleArray);


$rowCount=2;
//select c.invoice_no,c.cname,c.cphone,p.name,cl.batchcode,cl.qty,cl.mrp,cl.cuttingcharges,cl.rate,cl.cgst_amt,cl.sgst_amt,cl.total,cl.createtime
$result = $dbl->getSTCDetailsReport();
    $sr_no = 1;
    foreach ($result as $obj) {
        $stoDetails = $dbl->getStockTransferInfo($obj->st_id);
        $desc1 = isset($obj->desc1) && trim($obj->desc1) != "" ? " , " . $obj->desc1 . " mm" : "";
        $desc2 = isset($obj->desc2) && trim($obj->desc2) != "" ? " x " . $obj->desc2 . " mm" : "";
        $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
        $spec = isset($obj->spec) && trim($obj->spec) != "" ? " ,spec-" . $obj->spec . "" : "";
        $itemname = $obj->name . $desc1 . $desc2 . $thickness . $spec;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->challan_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->transferno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $stoDetails->fromloc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $stoDetails->toloc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->vehicle_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->eway_bill);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $itemname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->hsncode);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->batchcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->length);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->numberpcs);
        $rounde_qty = round($obj->qty, 4, PHP_ROUND_HALF_UP);
        $rounde_qty = sprintf("%.4f", $rounde_qty);
        $rounde_rate = round($obj->rate, 2, PHP_ROUND_HALF_UP);
        $rounde_rate = sprintf("%.2f", $rounde_rate);
        $total_value = $rounde_qty * $rounde_rate;
        $rounde_value = round($total_value, 2, PHP_ROUND_HALF_UP);
        $rounde_value = sprintf("%.2f", $rounde_value);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $rounde_qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $rounde_rate);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $rounde_value);
        $submit_date = date('d-m-Y', strtotime($obj->submittedDate));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $submit_date);
        if($obj->status == StockTransferChallanStatus::Completed){
            $pull_date = date('d-m-Y', strtotime($obj->pulleddate));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $pull_date);
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, "-");
        }
        
        
     
        
        $sr_no++;    
        $rowCount++;
    }
 
 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="STC Details Report.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>