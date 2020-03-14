<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
//require_once "lib/logger/clsLogger.php";
require_once "lib/core/Constants.php";
require_once "lib/php/Classes/PHPExcel.php";
require_once  'lib/php/Classes/PHPExcel/Writer/Excel2007.php';


$poid = isset($_GET['poid']) ? ($_GET['poid']) : false; 
$pono = isset($_GET['pono']) ? ($_GET['pono']) : false; 

$db = new DBConn();
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
$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
$objPHPExcel->getActiveSheet()->setCellValue('A1','Products');
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headerStyle);


//$query = "select parent_location_id,count(child_location_id) as cnt from it_purchase_order_items where po_id = 1 group by parent_location_id";
$query = "select l.name,parent_location_id,count( distinct child_location_id) as cnt from it_purchase_order_items p , it_locations l where  p.parent_location_id = l.id and po_id = $poid group by parent_location_id";
$pobjs = $db->fetchAllObjects($query);

$ascii_value = 66; // B ascii value
$e_ascii_value = 66;
foreach($pobjs as $pobj){
    if(isset($pobj) && !empty($pobj) && $pobj != null){
        $len = $pobj->cnt;
        $len = $len + 1;
        $str = "";
        $alpha1 = "";$alphal="";
        $cnt = 1;
        for($i=1;$i<=$len;$i++){
            
            if(trim($alpha1)==""){
                $alpha1 = chr($ascii_value);
                $alpha1 .= "1";
            
                $alpha = chr($ascii_value);
                $alpha_no = $alpha."1";
                $str .=  $alpha_no.":";
            }
            
//            print "<br>LEN = $len , I= $i , ascii = $ascii_value ";
            $ascii_value++;
            if($cnt==$len-1){
                $alphal = chr($ascii_value);
                $alphal .= "1";
                           
                $str .=  $alphal;
            }
            
            $cnt++;
            
        }
        $str = rtrim($str, ":");
//        print "<br>APLHA 1: $alpha1 :: str => $str ";
        $objPHPExcel->getActiveSheet()->mergeCells($str);
        $objPHPExcel->getActiveSheet()->setCellValue($alpha1,$pobj->name);
        $objPHPExcel->getActiveSheet()->getStyle($str)->applyFromArray($headerStyle);
        
        
        // to insert events header
        
        $qry = "select l.name,child_location_id,qty_in_packets from it_purchase_order_items p left join it_locations l on  p.child_location_id = l.id where  po_id = $poid and parent_location_id = $pobj->parent_location_id group by child_location_id order by child_location_id desc ";
        $eobjs = $db->fetchAllObjects($qry);
        foreach($eobjs as $eobj){
            $eaplha = "";
            if(isset($eobj) && !empty($eobj) && $eobj != null){
               $eaplha = chr($e_ascii_value);
               $eaplha .= "2";
               if(trim($eobj->name)!=""){
                $objPHPExcel->getActiveSheet()->setCellValue($eaplha,$eobj->name);
                
               }else{
                   if($eobj->child_location_id == -1){
                       $objPHPExcel->getActiveSheet()->setCellValue($eaplha,"Home Delivery");
                   }else if($eobj->child_location_id == -2){
                       $objPHPExcel->getActiveSheet()->setCellValue($eaplha,"Buffer");
                   }
               }
               
               $objPHPExcel->getActiveSheet()->getStyle($eaplha)->applyFromArray($headerStyle);
               $e_ascii_value++;
            }
        }
        
        
        $eaplha = chr($e_ascii_value);
        $eaplha .= "2";
        $objPHPExcel->getActiveSheet()->setCellValue($eaplha,"Total");
        $objPHPExcel->getActiveSheet()->getStyle($eaplha)->applyFromArray($headerStyle);
        $e_ascii_value++;
    }
    //$ascii_value++;
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
$rowCount = 3;
$query = "select p.name ,product_id from it_purchase_order_items pi, it_products p where pi.product_id = p.id and pi.po_id = $poid group by product_id";
$poiobjs = $db->fetchAllObjects($query);
if(!empty($poiobjs)){
    foreach($poiobjs as $poiobj){
        //$rpcol = chr($r_pascii_value);
        $rpcol = 0;
        if(isset($poiobj) && !empty($poiobj) && $poiobj!= null){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rpcol, $rowCount, $poiobj->name);
            $r_ascii_value = 1;
            $rcol = 1;
            // parent location loop
            foreach($pobjs as $pobj){
                if(isset($pobj) && !empty($pobj) && $pobj != null){
                  // fetch item for this row
                    $query = "select l.name,child_location_id,qty_in_packets from it_purchase_order_items p left join it_locations l on  p.child_location_id = l.id where  po_id = $poid and parent_location_id = $pobj->parent_location_id and product_id = $poiobj->product_id order by child_location_id desc";
//                    print "<br> $query";
                    $robjs = $db->fetchAllObjects($query);
                    $tot = 0;
                      foreach($robjs as $robj){
                          if(isset($robj) && !empty($robj) && $robj != null){
                             //  $rcol =  chr($r_ascii_value);
                              $tot = $tot + $robj->qty_in_packets;
                               $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rcol, $rowCount, $robj->qty_in_packets);
                               $r_ascii_value++;
                               $rcol++;
                          }
                      }
                      if($tot > 0){
                          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rcol, $rowCount, $tot);                               
                      }
                      
                      $rcol++;
                }            
            }
        }
        $rowCount++;
    }
}


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$pono.'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');