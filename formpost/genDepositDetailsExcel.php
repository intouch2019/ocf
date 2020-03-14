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
$daterange = isset($_GET['dtrng'])?$_GET['dtrng']:false;
$crid = isset($_GET['crid'])?$_GET['crid']:false;
$dtrng= explode ("-", $daterange);
$frmdt=$dtrng[0];
$tdt=$dtrng[1];
$frmdt = str_replace("/", "-", $frmdt);
$tdt = str_replace("/", "-", $tdt);
$frmdt1 =explode ("-", $frmdt);
$tdt1 =explode ("-", $tdt);
$FromDate=trim($frmdt1[2])."-".trim($frmdt1[1])."-".trim($frmdt1[0])." 00:00:00";
$ToDate= trim($tdt1[2])."-".trim($tdt1[1])."-".trim($tdt1[0])." 23:59:59";


$errors = array();
try{
    
$db = new DBConn();
$dbl = new DBLogic();

$sheetIndex=0;
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Create a first sheet, representing points details data
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('Stock Details');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'CR Code');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Amount');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Receipt No.');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Description');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Transaction Type');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'By User');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Createtime');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);


$styleArray = array(
    'font'  => array(
        'bold'  => false,

        'size'  => 10,
    ));
$objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('D')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('E')->applyFromArray($styleArray);  
$objPHPExcel->getActiveSheet()->getStyle('F')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('G')->applyFromArray($styleArray);


$rowCount=2;

$result = $dbl->getDepositDetailsExcelReport($crid,$FromDate,$ToDate);


    $sr_no = 1;
    $total_qty = 0;
    $total_taxable_amt=0;
    $total_gst_rate=0;
    $tot_cgst_val = 0;
    $tot_sgst_val = 0;
    $round_invoice_val = 0;
    $roundoff=0;
    $total_net_invoice_value= 0;
    $round_invoice_val= 0;
    $gstno= 0;
    if(isset($result) && $result != NULL){

    foreach ($result as $obj) {
        $transactionType = strtolower($obj->chargetypedesc);
        $transactionMode = str_replace("charges", "", $transactionType);
        $row[] = $transactionMode;
        
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $obj->dispname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->amount);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->receipt_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->description); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $transactionMode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->name);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->ctime);
       
        $sr_no++;    
        $rowCount++;
    }
            
    }else{
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, "Data not available for this daterange.");
    }
    $headerstyleArray = array(
       'font' => array(
            'bold' => true,
           
            'size' => 10,
   ));

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Deposit Details Excel Report.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>