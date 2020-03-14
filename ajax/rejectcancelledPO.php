<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

$userid = getCurrStoreId();
$user = getCurrStore();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $poid = isset($_GET['poid']) ? ($_GET['poid']) : false;
    if(!$poid){ $error['poid'] = "Not able to get PO Id"; }
    
    $remarks = isset($_GET['remarks']) ? ($_GET['remarks']) : false;
    $postatus = POStatus::Submitted;
    $objPurchaseOfficer = $dbl->getUserInfoByType(UserType::PurchaseOfficer);
    $objpo = $dbl->getPODetails($poid);
    //print_r($objpo);
    
    //if ($objpo->po_status == $postatus) {
            if (isset($objPurchaseOfficer->email) && trim($objPurchaseOfficer->email) != "") {
                $arr_to = explode(",", $objPurchaseOfficer->email);

                //$arr_to = $objpo->email;
                foreach ($arr_to as $to) {
                    //echo $to;
                    $subject = "Purchase Order No " . $objpo->pono." is rejected from Cancellation."; //." Date ". ddmmyy_date($objpo->submitdate);
                    $body = '<p>New Purchasse Order No '.$objpo->pono.' is rejected from Cancellation. PO Status is Maintain as Submitted.</p>
                             <p>Total Quantity :' . $objpo->totalqty . ' &nbsp;&nbsp;&nbsp;&nbsp; Total Amount :' . $objpo->totalvalue . '</p>
                                <p>Thanks & Regards,</p>
                                <p>Sarotam</p>
                                <p><b>Note : This is computer generated email do not reply. Kindly reply to '. $user->email.'</b></p>';

                    $emailHelper = new EmailHelper();
                    $success = $emailHelper->send(array($to), $subject, $body);
                    //$dbl->updateEmailStatus($objpo->id);
                }
            }
       // }

    if(count($error) == 0){
        $dbl->rejectcancelledPO($poid,$postatus,$userid,$remarks);
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        echo json_encode($resp);
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get PO Id"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
