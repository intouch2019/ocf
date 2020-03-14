<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $invid = isset($invid) && trim($invid) != "" ? intval($invid) : false;
    $cnid = isset($cnid) && trim($cnid) != "" ? intval($cnid) : false;
    
    if(isset($invitem)){
        $arr = explode("::", $invitem);
        $invItemid = $arr[0];
        $prodid = $arr[1];
    }
    
    $qty = isset($qty) && trim($qty) != "" ? trim($qty) : false;
    if(!$qty){ $error['missing_qty'] = "Enter qty to order"; }
    
    $batchcode = isset($batchcode) && trim($batchcode) != "" ? trim($batchcode) : false;
    $baseratebeforedisc = isset($baserate) && trim($baserate) != "" ? trim($baserate) : false;
    $nou = isset($numberofunits) && trim($numberofunits) != "" ? trim($numberofunits) : false;
    $discrate = isset($discrate) && trim($discrate) != "" ? trim($discrate) : false;
    if(!$discrate){ $error['missing_rate'] = "Enter discounted rate"; }
    if($discrate == "NaN"){$error['missing_rate'] = "Rate Not Found. Please Enter rate";}

    $rate = isset($rate) && trim($rate) != "" ? trim($rate) : false;
    if(!$rate){ $error['missing_rate'] = "Enter rate"; }
    if($rate == "NaN"){$error['missing_rate'] = "Rate Not Found. Please Enter rate";}
    
    $cnstatus = CreditNoteStatus::Open;
    $usertype = $user->usertype;
    
        if($cnid > 0 && $invid > 0){
            $cn_item_id = $dbl->insertCNItem($userid,$invid,$cnid,$prodid,$invItemid,$qty,$baseratebeforedisc,$discrate,$rate,$batchcode,$nou,$cnstatus,$usertype);
            
        }
    //}
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    //echo "reeeeeee";
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'creditnote/additem/cnid='.$cnid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = 'creditnote/additem/cnid='.$cnid;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;