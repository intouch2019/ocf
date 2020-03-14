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
	// print_r($dates);
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
$objPHPExcel->getActiveSheet()->setTitle('Collection Payment Report');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Sale Date');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Cr Code');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Cash');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Net Banking');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'debitcard');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'creditcard');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'cheque');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'total');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
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
$query = "  select rfc.dispname,date(p.createtime)as createtime from it_cr270001 c, it_payments_diary p , it_rfc_master rfc
            where c.crid = $crid and c.status=1 and c.id=p.invoice_id and c.crid= rfc.id and p.createtime >='$startDate' and p.createtime <= '$endDate'
            group by date(p.createtime)";
//print"$query";
$result = $db->fetchObjectArray($query);
    $sr_no = 1;
    foreach ($result as $obj) {
        $cash = "-";
        $NB = "-";
        $DC = "-";
        $CC = "-";
        $CH = "-";
        $TOT = "-";
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->createtime);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->dispname);    
        $q= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 3";
//                print "CASH:$q\n";
        $cashobj = $db->fetchObject($q);
        if(isset($cashobj) && $cashobj->amt !=null){ 
            $cash = round($cashobj->amt,2);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $cash);
        $q1= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 6";
//               print "NB:$q1\n";
        $cashobj1 = $db->fetchObject($q1);                
        if(isset($cashobj1) && $cashobj1->amt !=null){ 
            $NB = round($cashobj1->amt,2);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $NB);
        $q2= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 1";
            //print "DC:$q2\n";
        $cashobj2 = $db->fetchObject($q2);
        if(isset($cashobj2) && $cashobj2->amt !=null){ 
            $DC = round($cashobj2->amt,2);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $DC);
        $q3= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 4";
            //    print "CD:$q3\n";
        $cashobj3 = $db->fetchObject($q3);
        if(isset($cashobj3) && $cashobj3->amt !=null){ 
            $row[] = round($cashobj3->amt,2);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $CC);
        $q4= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 5";
//               print "CH:$q4\n"; 
        $cashobj4 = $db->fetchObject($q4);
        if(isset($cashobj4) && $cashobj4->amt !=null ){ 
            $CH = round($cashobj4->amt,2);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $CH);
        $qt= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime'";
//               print "TOT:$qt\n";
        $objt = $db->fetchObject($qt);
        if(isset($objt) && $objt->amt !=null ){ 
            $TOT = round($objt->amt,2);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $TOT);  

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