<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

extract($_GET);
$user = getCurrStore();

try{
    $db = new DBConn();
    
    $query = "select p.shopify_name,p.name,p.current_rate,p.is_active from it_products p order by p.name";
   //$result = $db->execQuery($query);
    $objs = $db->fetchAllObjects($query);
    
    header('Content-type: text/csv');
    //header('Content-disposition: attachment;filename=' . $location_id . '_InvoiceDetails.csv');
    header('Content-disposition: attachment;filename=Stock_File.csv');
    $headers = array('Product Name', 'Current Stock');
    //chmod('php://output', 0775);
    $fh = @fopen('php://output', 'w+');
    //chmod('php://output', 0777);
    fputcsv($fh, $headers);
    
    //while($obj = $result->fetch_object()){        
    foreach($objs as $obj){
        if(isset($obj) && !empty($obj) && $obj != null){
            $row = array();
           // $row[] = $obj->shopify_name;
            $row[] = $obj->name;
            //$row[] = $obj->current_rate;
//            if(trim($obj->is_active) == 1){
//              $row[] = "A";  
//            }else{
//              $row[] = "D";    
//            }
            fputcsv($fh, $row);
            unset($row);
        }
    }
    
    
    
}catch(Execption $xcp){
 print $xcp->getMessage();   
}

