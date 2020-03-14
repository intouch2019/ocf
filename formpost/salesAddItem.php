<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
//return;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$crid = $user->crid; 
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $salesid = isset($sid) && trim($sid) != "" ? intval($sid) : false;
    $custid = isset($custid) && trim($custid) != "" ? intval($custid) : 0;
    
//    $prodid = isset($prodsel) && trim($prodsel) != "" ? intval($prodsel) : false;
//    if(!$prodsel){ $error['missing_product'] = "Not able to get Product"; }
    
    if(isset($prodsel)){
        $arr = explode("::", $prodsel);
        $stockcurrid = $arr[0];
        $prodid = $arr[1];
    }
    
    $qty = isset($qty) && trim($qty) != "" ? trim($qty) : false;
    if(!$qty){ $error['missing_qty'] = "Enter qty"; 
    
    }
    
    $mtqty = isset($mt) && trim($mt) != "" ? trim($mt) : false;
    if(!$mtqty){ $error['missing_mtqty'] = "Enter MT qty"; 
    
    }
    
    
    if(!isset($batchcodes)){
        if(!$batchcodes){ $error['missing_batchcodes'] = "Please select Batchcodes"; }
    }
    $batchcodes1 = "";
    $batchqty = "";
    /*if(isset($batchcodes)){
        $batchcodes1 = "";
        $batchqty = "";
        $len = sizeof($batchcodes);
        for ($i = 0; $i<$len; $i++){
            ${"batchlist".$i} = $batchcodes[$i];
            $batchcodearr = explode("::",$batchcodes[$i]);
            if(isset($batchcodes1) && trim($batchcodes1)!= ""){
                $batchcodes1 =  $batchcodes1.",".$batchcodearr[1];
            }else{
                $batchcodes1 =  $batchcodearr[1];
            }
            if(isset($batchqty) && trim($batchqty)!= ""){
            $batchqty = $batchqty.",".$batchcodearr[3];
            }else{
                $batchqty =  $batchcodearr[3];
            }
        }
    }*/



    $rate = isset($rate) && trim($rate) != "" ? trim($rate) : false;
    if(!$rate){ $error['missing_rate'] = "Enter rate"; }
    if($rate == "NaN"){$error['missing_rate'] = "Rate Not Found. Please Enter rate";}
    
    
    $cutting_charge = isset($cutcharge) && trim($cutcharge) != "" ? trim($cutcharge) : 0;
    if(isset($cuttingcharges)){
        $cutting_charge = $cutting_charge;
    }else{
        $cutting_charge = 0;
    }
    $actual_rate = isset($orgrate) && trim($orgrate) != "" ? trim($orgrate) : 0;
    
    
    $invoicetype = InvoiceType::Cash;
    $invoicestatus = InvoiceStatus::Open;
    
    if(count($error) == 0){
        $saleid = 0;
        if($salesid == 0){
            //$saleid = $dbl->saveInvoice($userid,$custid,$invoicetype,$invoicestatus,$saledate);
            $saleid = $dbl->saveInvoice($userid,$custid,$invoicetype,$invoicestatus, $crid);
        }else{
            $saleid = $salesid;
        }
        if($saleid > 0){
            $sale_item_id = $dbl->insertInvoiceItem($userid,$saleid,$prodid,$stockcurrid,$mtqty,$qty,$rate,$cutting_charge,$actual_rate,$batchcodes);
            
        }
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    //echo "reeeeeee";
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'sales/create/salesid='.$salesid."/custid=".$custid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = 'sales/create/salesid='.$saleid."/custid=".$custid;
}

session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;