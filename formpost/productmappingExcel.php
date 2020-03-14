<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once 'lib/php/Classes/PHPExcel/Writer/Excel2007.php';
require_once "lib/core/strutil.php";
//print"in excel";
extract($_GET);
//print_r($_GET);
$db = new DBConn();
$lid = isset($_GET['locid']) ? $_GET['locid'] : false;
$status = isset($_GET['status']) ? $_GET['status'] : false;
$today = date('Y-m-d');
$today_db = $db->safe($today);
$errors = array();
//print "<br>$locid------$status<br>";
try{
    $locname ="";
    $selq = "select name from it_locations where id = $lid limit 1";
    $lobj = $db->fetchObject($selq);
    if(isset($lobj)){
        $locname = trim($lobj->name);
        $locname = str_replace(" ","_",$locname);
    }
    $onClause = "";
    if(trim($lid)!="" && trim($lid)!="0" && $lid != null ){
        $onClause .= " and lp.location_id = $lid ";
    }
    $sClause= "";
    if(trim($status)!=""  && $status != null && trim($status)!="0"){
        $sClause .= "  where lp.is_mapped = $status ";
    }
    $query ="select p.name prodname ,lp.is_mapped from it_products p left join it_location_products lp on p.id = lp.product_id $onClause $sClause";      
//    print $query;
    $mobjs= $db->fetchObjectArray($query);
    if(isset($mobjs)){
        $sheetIndex = 0;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex($sheetIndex);
        $objPHPExcel->getActiveSheet()->setTitle('Product Mapping');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr.No');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Product Name');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Mapping Status');
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);

        $styleArray = array(
                'font' => array(
                    'bold' => false,
                        'size' => 10,
            ));
            $headerstyleArray = array(
                'font' => array(
                    'bold' => true,
                       'size' => 10,
            ));
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($headerstyleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C')->applyFromArray($styleArray); 
        $colCount = 0;
        $rowCount = 2;
        $srno = 1;
        foreach ($mobjs as $mobj){
            $statusval = "Not Define";
            if(trim($mobj->is_mapped) == LocationProductStatus::mapped){
                $statusval= "Mapped";
            }else if(trim($mobj->is_mapped) == LocationProductStatus::unmapped){
                $statusval= "Unmapped";
            }
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $srno);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $mobj->prodname );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $statusval);
 
            $colCount = 0;
            $rowCount++;
            $srno++;
        }
     
        $filename = 'Product_Mapping_'.$locname.'.xls';
//      } 
//      print $filename;
//              
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
       // header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
//print count($sobjs);
//print"<br>";
//print_r($sobjs);
//print"<br>";
} catch (Exception $xcp) {
    print $xcp->getMessage();
}