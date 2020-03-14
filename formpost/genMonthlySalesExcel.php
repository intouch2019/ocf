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
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';

extract($_GET);
$curr = getCurrStore();

$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : false;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : false;

$errors = array();
try {

    $db = new DBConn();
    $dbl = new DBLogic();

    $dateArr = explode(" ", $startDate);
    $strArr = explode("-", $dateArr[0]);

    $month = $strArr[1];
    $monthName = "";

    if ($month == Months::January) {
        $monthName = "January";
    } else if ($month == Months::February) {
        $monthName = "February";
    } else if ($month == Months::March) {
        $monthName = "March";
    } else if ($month == Months::April) {
        $monthName = "April";
    } else if ($month == Months::May) {
        $monthName = "May";
    } else if ($month == Months::June) {
        $monthName = "June";
    } else if ($month == Months::July) {
        $monthName = "July";
    } else if ($month == Months::August) {
        $monthName = "August";
    } else if ($month == Months::September) {
        $monthName = "September";
    } else if ($month == Months::October) {
        $monthName = "October";
    } else if ($month == Months::November) {
        $monthName = "November";
    } else if ($month == Months::December) {
        $monthName = "December";
    }

    $shortnameArr = array('RHS', 'CHS', 'SHS', 'DHS', 'Gate Channel', 'I Beam', 'Square Bar', 'Round Bar', 'Flat', 'T Bar', 'Rebar', 'PPGI Sheet', 'Equal Angle', 'HR Sheet', 'Plate', 'Chequered Plate', 'Plain Binding Wire', 'GI Binding Wire', 'GI Barbed Wire', 'GI Wire Mesh', 'Plain Wire Mesh', 'Plain PPGI Sheet', 'Plain Binding Wire 20 gauge', 'Channel');


    $sheetIndex = 0;
// Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
// Create a first sheet, representing points details data
    $objPHPExcel->setActiveSheetIndex($sheetIndex);
    $objPHPExcel->getActiveSheet()->setTitle('Stock Details');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', $monthName);
    
    $alphabets1 = 'B';
    foreach ($shortnameArr as $shortNameItem) {
        $columnNameQty = $shortNameItem." (QTY)";
        $objPHPExcel->getActiveSheet()->setCellValue($alphabets1."1", $columnNameQty);
        $alphabets1++;
        $columnNameVal = $shortNameItem." (Value)";
        $objPHPExcel->getActiveSheet()->setCellValue($alphabets1."1", $columnNameVal);
        $alphabets1++;
    }
    
    


    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    
    $alphabets2 = 'B';
    foreach ($shortnameArr as $shortNameItem) {
        $objPHPExcel->getActiveSheet()->getColumnDimension("$alphabets2")->setWidth(20);
        $alphabets2++;
        $objPHPExcel->getActiveSheet()->getColumnDimension("$alphabets2")->setWidth(20);
        $alphabets2++;
    }




    $styleArray = array(
        'font' => array(
            'bold' => false,
//        'color' => array('rgb' => 'FF0000'),
            'size' => 10,
    ));
    $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
    
    $alphabets3 = 'B';
    foreach ($shortnameArr as $shortNameItem) {
        $objPHPExcel->getActiveSheet()->getStyle("$alphabets3")->applyFromArray($styleArray);
        $alphabets3++;
        $objPHPExcel->getActiveSheet()->getStyle("$alphabets3")->applyFromArray($styleArray);
        $alphabets3++;
    }
    

    $rowCount = 2;
    $rowCountIncremental = 2;
    $sr_no = 1;

    $query1 = "select id,dispname from it_rfc_master";
    $crlist = $db->fetchObjectArray($query1);

    foreach ($crlist as $crInfo) {
        $crid = $crInfo->id;
        $invoiceStatus = InvoiceStatus::Created;
        $crName = $crInfo->dispname;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $crName);
        $rowCount++;
        $countColumnQty = 1;
        $countColumnVal = 2;
        foreach ($shortnameArr as $shortName) {
            $rowCount2 = $rowCountIncremental;
            $query2 = "select p.shortname, s.crid, sum(round(si.qty,4)) as tot_qty, sum( round((round(si.qty,4) * round(si.rate,2)),2)  +  round(((round( ((round((round(si.qty,4) * round(si.rate,2)),2))* (si. cgst_percent / 100)),2))*2),2)  ) as tot_value from it_cr270001_items si, it_cr270001 s, it_products p where p.id = si.product_id and s.id = si.invoice_id and s.createtime > '$startDate' and s.createtime < '$endDate' and s.status = $invoiceStatus and p.shortname = '$shortName' and s.crid = $crid";
            $data = $db->fetchObject($query2);

            if($shortName == $data->shortname && ($data->shortname != null || trim($data->shortname) == "") ){
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($countColumnQty, $rowCount2, $data->tot_qty);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($countColumnVal, $rowCount2, $data->tot_value);
                $rowCount2++;
            }else{
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($countColumnQty, $rowCount2, "-");
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($countColumnVal, $rowCount2, "-");
                $rowCount2++;
            }
        $countColumnQty = $countColumnQty + 2;
        $countColumnVal = $countColumnVal + 2;
        }
        
        
        $rowCountIncremental++;
    }
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, "Total");
    

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Monthly Sales Report_'.$monthName.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
} catch (Exception $xcp) {
    print $xcp->getMessage();
}
?>
