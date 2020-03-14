<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
//require_once "lib/logger/clsLogger.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once  'lib/php/Classes/PHPExcel/Writer/Excel2007.php';




$db = new DBConn();
$user = getCurrStore();
$po_date = date('Y-m-d');
$query = "select id,po_no from it_purchase_orders where pur_location_id = ".$user->location_id." and po_date = '$po_date' and status = ".POStatus::Completed;
$pobj = $db->fetchObject($query);
$file_name = "-";


$sheetIndex=0;
$headerStyle = array(
    'font' => array(
        'bold' =>true,
        'size' => '8',
        'align' => 'center',
    )
);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Create a first sheet, representing sales data
$objPHPExcel->setActiveSheetIndex($sheetIndex);
$objPHPExcel->getActiveSheet()->setTitle('PO Excel');
//$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Products');
$objPHPExcel->getActiveSheet()->setCellValue('A1','Product Name');
$objPHPExcel->getActiveSheet()->setCellValue('B1','Product UOM');
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($headerStyle);

$prodobjs = null;

if(isset($pobj) && !empty($pobj) && $pobj != null){
$file_name = $pobj->po_no;    
    
//to fetch products
$query = "select pi.product_id,p.name as product,p.uom_id,u.name as uom, p.pack_size_id, ps.pack_size from it_purchase_order_items pi , it_products p , it_uom u , it_pack_size ps  where pi.product_id = p.id and p.uom_id = u.id and p.pack_size_id = ps.id and pi.po_id = $pobj->id group by product_id order by product";//limit 10                                    
$prodobjs = $db->fetchAllObjects($query);

//qry for dynamic cols
$query = "select l.name,parent_location_id from it_purchase_order_items p , it_locations l where   p.parent_location_id = l.id  and   po_id = $pobj->id  group by parent_location_id order by parent_location_id ";
$cobjs = $db->fetchAllObjects($query);

$ascii_value = 67; // C ascii value

foreach($cobjs as $cobj){
    if(isset($cobj) && !empty($cobj) && $cobj != null){                                           
        $eaplha = chr($ascii_value);
        $eaplha .= "1";
        $objPHPExcel->getActiveSheet()->setCellValue($eaplha,$cobj->name);
        $objPHPExcel->getActiveSheet()->getStyle($eaplha)->applyFromArray($headerStyle);
        $ascii_value++;
    }
    //$ascii_value++;
}
    $eaplha = chr($ascii_value);
    $eaplha .= "1";
    $objPHPExcel->getActiveSheet()->setCellValue($eaplha,"Total packets");
    $objPHPExcel->getActiveSheet()->getStyle($eaplha)->applyFromArray($headerStyle);
    $ascii_value++;
    
    $eaplha = chr($ascii_value);
    $eaplha .= "1";
    $objPHPExcel->getActiveSheet()->setCellValue($eaplha,"Total");
    $objPHPExcel->getActiveSheet()->getStyle($eaplha)->applyFromArray($headerStyle);
    $ascii_value++;
    
}
//select l.id,l.name from it_locations l, it_location_dependancy ld where l.id=ld.child_location_id and ld.parent_location_id = $this->heid
//$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Pune');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);

$styleArray = array(
    'font'  => array(
        'bold'  => false,
//        'color' => array('rgb' => 'FF0000'),
        'size'  => 10,
    ));
$headerStyle = array(
    'font' => array(
        'bold' =>true,
        'size' => '8',
        'align' => 'center',
    )
);


//$objPHPExcel->getActiveSheet()->getStyle('A1:AE1')->applyFromArray($headerStyle);
//$objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->applyFromArray($headerStyle);
//$objPHPExcel->getActiveSheet()->getStyle('A3:AE3')->applyFromArray($headerStyle);

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

//fetch products list from po items
$r_pascii_value = 0;
$r_ascii_value = 1;
$rowCount = 2;
if(isset($prodobjs) && !empty($prodobjs) && $prodobjs != null){
    foreach($prodobjs as $prodobj){
        if(isset($prodobj) && !empty($prodobj) && $prodobj != null){
            $rpcol = 0;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rpcol, $rowCount, $prodobj->product);
            
            $rpcol = 1;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rpcol, $rowCount, $prodobj->uom);
            
            $rpcol++;
            $query = "select id,product_id,parent_location_id, sum(qty_in_packets) as tot_packets from it_purchase_order_items where po_id = $pobj->id and product_id = $prodobj->product_id group by product_id,parent_location_id order by parent_location_id ";
            $dobjs = $db->fetchAllObjects($query);
            if(!empty($dobjs)){
                $tot_packets = 0;
                foreach($dobjs as $dobj){
                    //$rpcol = chr($r_pascii_value);                   
                    if(isset($dobj) && !empty($dobj) && $dobj!= null){
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rpcol, $rowCount, $dobj->tot_packets);                                              
                        $rpcol++;
                        $tot_packets = $tot_packets + $dobj->tot_packets;
                    }
                    
                }
                
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rpcol, $rowCount, $tot_packets);                                              
                $rpcol++;
                
                //conversion logic
                $tot_kg = "-";
                if(strcmp($prodobj->uom, "gms") == 0 || strcmp($prodobj->uom, "gm") ==  0 || strcmp($prodobj->uom, "grams") == 0){
                    $pack_size_arr = explode(" ", $prodobj->pack_size);
                    $pack_size_no = $pack_size_arr[0];

                    $tot_grms = $tot_packets * $pack_size_no;
                    $tot_kg = round(($tot_grms/1000),2);
                    $tot_kg .= " (kg) ";

                }else{
                    $tot_kg = $tot_packets;
                    $tot_kg .= " (".$prodobj->uom.") ";
                }
                
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rpcol, $rowCount, $tot_kg);                                              
                $rpcol++;
                                    
                $rowCount++;
            }
      }  
    }
}

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$file_name.'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');