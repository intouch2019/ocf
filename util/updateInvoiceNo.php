<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';


$error = array();
$db = new DBConn();
$dbl = new DBLogic(); 

try {
    
    $invoiceObj =  $dbl->getSalesInvoice();
    $rowUpdated = 0;
    if(isset($invoiceObj)){
        foreach ($invoiceObj as $inv){
            $nvoiceid = $inv->id;
            $invoiceno = $inv->invoice_no;
            $invoicearr = explode("-", $invoiceno);
            $invfirst = $invoicearr[0];
            $invsecond = $invoicearr[1];
            $stateTin = 27; 
            $updatedInvSec = $stateTin."/".$invsecond;
            $invoice_num = $invfirst."-".$updatedInvSec;
            $updateid = $dbl->updateInvNum($nvoiceid,$invoice_num);
            $rowUpdated++;
        }
        echo "Total Rows Updated ". $rowUpdated;
    }
    
} catch (Exception $ex) {
    
}


