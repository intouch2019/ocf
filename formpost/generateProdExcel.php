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
$dstatus = isset($_GET['dstatus'])?$_GET['dstatus']:false;
$errors = array();
try{
    
$db = new DBConn();
$dbl = new DBLogic();

$sheetIndex=0;
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Create a first sheet, representing points details data
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('Products Overview');
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. no');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Category');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Product Description');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Short Form');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Description 1(If Dimension then in mm)');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Description 2(If Dimension then in mm)');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Thickness (mm)');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Default Standard Length (Meter)');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Default Specification');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Default HSN Code');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Standard (kg/m) or (kg/Bundle)');
$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Created By');
$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Creation Date');
$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Updated By');
$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Updated Date');
  
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);


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

$rowCount=2;

$result = $dbl->getAllActiveProducts();
    $sr_no = 1;
    foreach ($result as $obj) {
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->ctg);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, $obj->prod);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->shortname);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->desc1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->desc2);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->thickness);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->stdlength);
        if($obj->spec_id > 0){
            $spec_name = "";
            $obj_specification = $dbl->getSpecificationById($obj->spec_id);
            if($obj_specification != NULL && isset($obj_specification)){
                $spec_name = $obj_specification->name;
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $spec_name);  
            }
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, "");  
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->hsncode);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->kg_per_pc);  
        
        $created_by = "";
        if(isset($obj->created_by) && $obj->created_by != NULL){
            $obj_user = $dbl->getUserInfoById($obj->created_by);
            if(isset($obj_user) && $obj_user != NULL){
                $created_by = $obj_user->name;
            }
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $created_by);
        
        $createtime = "";
        if(isset($obj->createtime) && $obj->createtime != NULL){
            $createtime = ddmmyy($obj->createtime);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $createtime);
        
        $updated_by = "";
        if(isset($obj->updated_by) && $obj->updated_by != NULL){
            $obj_user = $dbl->getUserInfoById($obj->updated_by);
            if(isset($obj_user) && $obj_user != NULL){
                $updated_by = $obj_user->name;
            }
        }
        
        if($updated_by != ""){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $updated_by);
            $updatetime = "";
            if(isset($obj->updatetime) && $obj->updatetime != NULL){
                $updatetime = ddmmyy($obj->updatetime);
            }
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $updatetime);
        }
        
        $sr_no++;    
        $rowCount++;
    }
 
 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Product_master.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>