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
 if($curr->usertype != UserType::RFC){       
    $crid = isset($_GET['crid'])?$_GET['crid']:false;
 }else{
    $crid =  $curr->crid;
 }
$daterange = isset($_GET['drange'])?$_GET['drange']:false;
$errors = array();
try{
    
$db = new DBConn();
$startDate = "";
$endDate = "";
if(trim($daterange)!="" && trim($daterange)!="-1"){
	$daterange = str_replace("'"," ",$daterange);
	$dates = explode("-",$daterange);
	$startDate = $dates[0];
	$startDate = str_replace('/', '-', $startDate);
	
	$endDate = $dates[1];
	$endDate = str_replace('/', '-', $endDate);
	$startDate = explode("-", $startDate);
	$nstartdate = trim($startDate[2])."-".trim($startDate[1])."-".trim($startDate[0]);
	$endDate = explode("-", $endDate);
	$nenddate = trim($endDate[2])."-".trim($endDate[1])."-".trim($endDate[0]);

	$startDate = date("Y-m-d 00:00:00", strtotime($nstartdate));
	$endDate = date("Y-m-d 23:59:59", strtotime($nenddate));
}

$sheetIndex=0;
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
//'createtime','crcode','cash','netbanking','debitcard',' creditcard','cheque','total'
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('Collection Report');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Sale Date');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Cr Code');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Total inv amt');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Total collectn');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'difference');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);   

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

$rowCount=2;
$query = " select c.createtime, r.dispname, sum(round(c.total_amount)) as invtotamt , sum(round(p.amount)) as colltotamt from it_cr270001 c, it_payments_diary p, it_rfc_master r where c.id = p.invoice_id and c.crid = r.id and c.status = 1 and c.crid=$crid and c.createtime >='$startDate' and c.createtime <= '$endDate' group by date(c.createtime) order by p.createtime desc ";
//print_r($query);
//return;
$result = $db->fetchObjectArray($query);
    $sr_no = 1;
    foreach ($result as $obj) {
        $invtotamt = "-";
        $round_iamt = "-";
        $colltotamt = "-";
        $round_camt = "-";
        $difference = "-";
        $round_diff = "-";
        $Date = "-";
        //DD-MM-YYYY
        $invtotamt = round( $obj->invtotamt, 2, PHP_ROUND_HALF_UP);
        $round_iamt  = sprintf("%.2f", $invtotamt );
        $colltotamt = round( $obj-> colltotamt, 2, PHP_ROUND_HALF_UP);
        $round_camt  = sprintf("%.2f",  $colltotamt );
        $Date = date("d-m-Y", strtotime($obj->createtime));
              
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $Date);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->dispname);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $round_iamt );    
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $round_camt);    
            
        $difference = round( $invtotamt -  $colltotamt, 2, PHP_ROUND_HALF_UP);
        $round_diff  = sprintf("%.2f",  $difference );
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount,$round_diff );  
        
        $sr_no++;    
        $rowCount++;
    }
 
    // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="CollectionPaymentReport.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>


