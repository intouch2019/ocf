<?php
require_once "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once ("lib/core/Constants.php");

extract($_GET);
//print_r($_GET);
$error = array();
$forminfo = $_GET['params'];
$formarr= explode("=",$forminfo);
//print_r($formarr);
$_SESSION['form_id'] =trim($formarr[1]);
$success = "";
$db = new DBConn();
$purdt = $_GET['seldate'];
$purid = trim($_GET['pursel']);
$itemsarr = $_GET['item'];
//print_r($itemsarr);
$today= Date('Y-m-d H:i:s');
$today_db = $db->safe($today);
$coninsertedid = 0;
try{

    $query ="insert into it_conversions set purchase_in_id = $purid, createtime = $today_db";
//    print "<br>$query";
    $coninsertedid = $db->execInsert($query);
    if($coninsertedid > 0){
        foreach($itemsarr as $itemarr){
//            $insertedid = 0;
//            print_r($itemarr);
//            print "<br>";
            $prodid = trim($itemarr['prodid']);
            $reqpackets = trim($itemarr['reqpackets']);
            $purchaseqty = trim($itemarr['purchaseqty']);
            $act = trim($itemarr['act']);
            $diff = trim($itemarr['diff']);
            $reason = trim($itemarr['reason']);
            $reason_db = $db->safe($reason);
//            print "<br>prodid=$prodid,act=$act,diff=$diff,reason=$reason";     
            if($prodid != "" && $act!="" && $diff!="" && $reason!="" && $reqpackets != "" && $purchaseqty!=""){
                $qry="insert into it_conversion_items set conversion_id = $coninsertedid, product_id = $prodid, purchase_qty= $purchaseqty, req_packets = $reqpackets, tot_packets = $act, difference= $diff, reason=$reason_db , createtime = $today_db ";      
//                print "<br>$qry";
                $insertedid = $db->execInsert($qry);
//                print "<br>insertedid=$insertedid";
            }else{
                //parameter missing
                $error['insert_fail'] = "Parameters Missing.";
            }
        }   
//        $uqry= "update it_purchase_in set status =".PurchaseInStatus::converted." where id =$purid";
////        print $uqry;
//        $db->execUpdate($uqry);     
    }else{
        $error['insert_fail'] = "DB Insertion Fail.";
    }
    if(count($error)==0){
        $uqry= "update it_purchase_in set status =".PurchaseInStatus::converted." where id =$purid";
//        print $uqry;
        $db->execUpdate($uqry);    
    }
} catch (Exception $ex) {
   $error['exc'] = $ex->message; 
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'packet/conversion/purid='.$purid.'/purdt='.$purdt; //alloctndt=28-09-2017
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    $_SESSION['form_success'] = $success;
    $redirect = 'packet/conversion';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;