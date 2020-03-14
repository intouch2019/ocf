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
$objPHPExcel->getActiveSheet()->setTitle('STO Header Report');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Transfer No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Transfer Date');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'From location');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'To location');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'TotalQty (MT)');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);   


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
$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode('0.0000');


$rowCount=2;
//select c.invoice_no,c.cname,c.cphone,p.name,cl.batchcode,cl.qty,cl.mrp,cl.cuttingcharges,cl.rate,cl.cgst_amt,cl.sgst_amt,cl.total,cl.createtime
$result = $dbl->getAllStockTransfer();


    foreach ($result as $obj) {
       
        $stoDetails = $dbl->getStockTransferInfo($obj->id);

        $transferdate = date('d-m-Y', strtotime(date($stoDetails->transferdate)));

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $stoDetails->transferno);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $transferdate);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $stoDetails->fromloc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $stoDetails->toloc);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->tot_qty);

        $rowCount++;

    }

 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="STO Header Report.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>