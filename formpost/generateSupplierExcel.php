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
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Supp Code');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Date of entry');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'KYC Number');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Company Name');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Bank Name');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Bank A/C No');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Bank branch');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Firm Type');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Address');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'GR Address');
$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Pincode');
$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Country');
$objPHPExcel->getActiveSheet()->setCellValue('N1', 'State');
$objPHPExcel->getActiveSheet()->setCellValue('O1', 'District');
$objPHPExcel->getActiveSheet()->setCellValue('P1', 'PAN No');
$objPHPExcel->getActiveSheet()->setCellValue('Q1', 'CIN No');
$objPHPExcel->getActiveSheet()->setCellValue('R1', 'GST Applicable');
$objPHPExcel->getActiveSheet()->setCellValue('S1', 'GST No');
$objPHPExcel->getActiveSheet()->setCellValue('T1', 'Contact Person1');
$objPHPExcel->getActiveSheet()->setCellValue('U1', 'Phone1');
$objPHPExcel->getActiveSheet()->setCellValue('V1', 'Email1');
$objPHPExcel->getActiveSheet()->setCellValue('W1', 'Contact Person2');
$objPHPExcel->getActiveSheet()->setCellValue('X1', 'Phone2');
$objPHPExcel->getActiveSheet()->setCellValue('Y1', 'Email2');
$objPHPExcel->getActiveSheet()->setCellValue('Z1', 'Contact Person3');
$objPHPExcel->getActiveSheet()->setCellValue('AA1', 'Phone3');
$objPHPExcel->getActiveSheet()->setCellValue('AB1', 'Email3');
$objPHPExcel->getActiveSheet()->setCellValue('AC1', 'Contact Person4');
$objPHPExcel->getActiveSheet()->setCellValue('AD1', 'Phone4');
$objPHPExcel->getActiveSheet()->setCellValue('AE1', 'Email4');
$objPHPExcel->getActiveSheet()->setCellValue('AF1', 'MSMED REG NO');
$objPHPExcel->getActiveSheet()->setCellValue('AG1', 'Currency');
$objPHPExcel->getActiveSheet()->setCellValue('AH1', 'Created datetime');
$objPHPExcel->getActiveSheet()->setCellValue('AI1', 'Created by');
$objPHPExcel->getActiveSheet()->setCellValue('AJ1', 'Updated datetime');
$objPHPExcel->getActiveSheet()->setCellValue('AK1', 'Update by');


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
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(20);   
$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);


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
$objPHPExcel->getActiveSheet()->getStyle('X')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('Y')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('Z')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AA')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AB')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AC')->applyFromArray($styleArray);  
$objPHPExcel->getActiveSheet()->getStyle('AD')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AE')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AF')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AG')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AH')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AI')->applyFromArray($styleArray);  
$objPHPExcel->getActiveSheet()->getStyle('AJ')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('AK')->applyFromArray($styleArray);

$rowCount=2;

$result = $dbl->getAllActiveSuppliers();
    $sr_no = 1;
    foreach ($result as $obj) {
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $rowCount, $sr_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $rowCount, $obj->supplier_code);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $rowCount, ddmmyy($obj->date_of_entry));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $rowCount, $obj->kyc_number);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $rowCount, $obj->company_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $rowCount, $obj->bank_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $rowCount, $obj->bank_ac_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $rowCount, $obj->bank_branch);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $rowCount, $obj->firm_type);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $rowCount, $obj->address);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $rowCount, $obj->graddress);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $rowCount, $obj->pincode);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $rowCount, $obj->country);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $rowCount, $obj->state);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $rowCount, $obj->district);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $rowCount, $obj->pan_no);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $rowCount, $obj->cin_no);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $rowCount, $obj->is_gst_applicable);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $rowCount, $obj->gst_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $rowCount, $obj->contact_person1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $rowCount, $obj->phone1);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $rowCount, $obj->email1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $rowCount, $obj->contact_person2);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $rowCount, $obj->phone2);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $rowCount, $obj->email2);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $rowCount, $obj->contact_person3);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $rowCount, $obj->phone3);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $rowCount, $obj->email3);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(28, $rowCount, $obj->contact_person4);  
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(29, $rowCount, $obj->phone4);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $rowCount, $obj->email4);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $rowCount, $obj->msmed_reg_no);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(32, $rowCount, $obj->currency);  

        $createtime = "";
        if(isset($obj->createtime) && $obj->createtime != NULL){
            $createtime = $obj->createtime;
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(33, $rowCount, $createtime);


        $created_by = "";
        if(isset($obj->created_by) && $obj->created_by != NULL){
            $obj_user = $dbl->getUserInfoById($obj->created_by);
            if(isset($obj_user) && $obj_user != NULL){
                $created_by = $obj_user->name;
            }
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(34, $rowCount, $created_by);
        
        $updated_by = "";
        if(isset($obj->updated_by) && $obj->updated_by != NULL){
            $obj_user = $dbl->getUserInfoById($obj->updated_by);
            if(isset($obj_user) && $obj_user != NULL){
                $updated_by = $obj_user->name;
            }
        }
        
        if($updated_by != ""){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(35, $rowCount, $updated_by);
            $updatetime = "";
            if(isset($obj->updatetime) && $obj->updatetime != NULL){
                $updatetime = $obj->updatetime;
            }
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(36, $rowCount, $updatetime);
        }
        
        $sr_no++;    
        $rowCount++;
    }
 
 // Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Supplier Master.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}catch(Exception $xcp){
    print $xcp->getMessage();
}
?>