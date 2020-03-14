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
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    //$salesid = isset($_GET['salesid']) ? ($_GET['salesid']) : false;
    $salesid = isset($salesid) && trim($salesid) != "" ? intval($salesid) : false;
    if(!$salesid){ $error['salesid'] = "Not able to get Sales Id"; }
    
    if(count($error) == 0){
        $invoicestatus = InvoiceStatus::Created;
        $stockdiarystatus = StockDiaryReason::Sale;
        $rows_affected = $dbl->completeSales($salesid,$userid,$invoicestatus,$stockdiarystatus);
        if($rows_affected <= 0){
            $error['incomplete_transction'] = "Problem while completing sales transaction";
        }else{
            
        }
    }
    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
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
    $redirect = 'sales/status='.$invoicestatus;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
