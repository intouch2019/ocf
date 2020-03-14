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
$crid = isset($_GET['crid'])?$_GET['crid']:false;
$daterange=isset($_GET['uploaddate']) ? $_GET['uploaddate'] : false;

$dtrng= explode ("-", $daterange);
$frmdt=$dtrng[0];
$tdt=$dtrng[1];
$frmdt = str_replace("/", "-", $frmdt);
$tdt = str_replace("/", "-", $tdt);
$frmdt1 =explode ("-", $frmdt);
$tdt1 =explode ("-", $tdt);
$FromDate=trim($frmdt1[2])."-".trim($frmdt1[1])."-".trim($frmdt1[0]);
$ToDate= trim($tdt1[2])."-".trim($tdt1[1])."-".trim($tdt1[0]);
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
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. no');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Reference NO');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice NO');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Invoice Date');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Customer');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Cust Mo NO');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Product');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Desc1');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Desc2');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Thickness');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'HSN Code');
$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Batchcode');
$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Length');
$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Qty');
$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Rate');
$objPHPExcel->getActiveSheet()->setCellValue('P1', 'Cutting Charges');
$objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Rate (Rs./Kg)');
$objPHPExcel->getActiveSheet()->setCellValue('R1', 'Taxable');
$objPHPExcel->getActiveSheet()->setCellValue('S1', 'CGST');
$objPHPExcel->getActiveSheet()->setCellValue('T1', 'SGST');
$objPHPExcel->getActiveSheet()->setCellValue('U1', 'Total(Rs.)');
$objPHPExcel->getActiveSheet()->setCellValue('V1', 'Createtime');
$objPHPExcel->getActiveSheet()->setCellValue('W1', 'Customer gst no');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
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
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);





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
$objPHPExcel->getActiveSheet()->getStyle('Q')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('R')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('S')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('T')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('U')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('V')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('W')->applyFromArray($styleArray);

$gstno = 0;
$rowCount=2;
//select c.invoice_no,c.cname,c.cphone,p.name,cl.batchcode,cl.qty,cl.mrp,cl.cuttingcharges,cl.rate,cl.cgst_amt,cl.sgst_amt,cl.total,cl.createtime
$result = $dbl->getCRSalesSummeryWithDtrange($crid,$FromDate,$ToDate);
    $sr_no = 1;
    foreach ($result as $obj) {
        $invnumarr = explode("-", $obj->invoice_no);
        $refNum = $invnumarr[0];
        $invoicenum = $invnumarr[1];
         $gstno = $obj->gstno;
        if(is_null ($gstno))
        {$gstno="-";}
        else{$gstno;}
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $refNum);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $invoicenum);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->saledate);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->cname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->cphone);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->desc1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->desc2);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->thickness);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->hsncode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $obj->batchcode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $obj->length);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $obj->qty);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $obj->mrp);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $obj->cuttingcharges);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount, $obj->rate);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, $obj->taxable);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $obj->cgst_amt);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $rowCount, $obj->sgst_amt);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $rowCount, $obj->total);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $rowCount, $obj->createtime);
         $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $rowCount, $gstno);
        
     
        
        $sr_no++;    
        $rowCount++;
    }
 
 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="CR Sales Report.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>