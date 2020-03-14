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
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice No');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Invoice Date');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Customer');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Cust Mo NO');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Total Qty');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'GST %');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Total of Taxable Value');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Total Of CGST');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Total Of SGST');
$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Net Value');
$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Round off');
$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Invoice Value');
$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Customer gst no');


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
$objPHPExcel->getActiveSheet()->getStyle('H')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('I')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('J')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('K')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('L')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('M')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('N')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('O')->applyFromArray($styleArray);


$rowCount=2;

$result = $dbl->getAggCRSalesSummeryWithDtrange($crid,$FromDate,$ToDate);


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
    foreach ($result as $obj) {
        $invnumarr = explode("-", $obj->invoice_no);
        $refNum = $invnumarr[0];
        $invoicenum = $invnumarr[1];
        $total_qty = $total_qty + $obj->qty;
        $total_taxable_amt = $total_taxable_amt + $obj->taxable;

        $tot_cgst_val = $tot_cgst_val + $obj->cgst_amt;
        $tot_sgst_val = $tot_sgst_val + $obj->sgst_amt;
        $total_net_value = $obj->total;
        $round_invoice_val = round($total_net_value);
        $roundoff = round($round_invoice_val - $total_net_value ,2,PHP_ROUND_HALF_DOWN);
        $total_net_invoice_value = $total_net_invoice_value + $round_invoice_val;

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
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, round($obj->qty,4));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, "18");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, round($obj->taxable,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, round($obj->cgst_amt,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, round($obj->sgst_amt,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, round($total_net_value,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $roundoff);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $round_invoice_val); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $gstno); 
       
        
          

        $sr_no++;    
        $rowCount++;
    }
    $headerstyleArray = array(
       'font' => array(
            'bold' => true,
           
            'size' => 10,
   ));
   $objPHPExcel->getActiveSheet()->getStyle('A:Y')->applyFromArray($headerstyleArray);
   
       $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, "TOTALS");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, "");  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, round($total_qty,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, round($total_taxable_amt,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, round($tot_cgst_val,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, round($tot_sgst_val,2));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, "");
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, round($total_net_invoice_value,2)); 
         $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, ""); 
 

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="CR Sales Report.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>