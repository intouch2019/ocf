<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

extract($_GET);
$userid = getCurrStoreId();
$user = getCurrStore();
//print_r($user);
$username = $user->name;
$error = array();
try{
    $dbl = new DBLogic();
    $crid = isset($crid) && trim($crid) != "" ? $crid : NULL;
        $dbl->approveCR($crid,$userid);
        $objPO = $dbl->getUserInfoById($createdbyid);
//        print_r($objPO);
        if (isset($objPO->email) && trim($objPO->email) != "") {
            $arr_to = explode(",", $objPO->email);
            foreach ($arr_to as $to) {
                $subject = "CR Approval Done";
                $body = '<p> Your Request has been Approved by '.$username .'</p>
                    
                              <p>Thanks & Regards,</p>
                               <p>Sarotam</p>
                              <p><b>Note : This is computer generated email do not reply.  </b></p>';

                $emailHelper = new EmailHelper();
                $success = $emailHelper->send(array($to), $subject, $body);
            }
        }        
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        echo json_encode($resp);

}catch(Exception $xcp){
    print($xcp->getMessage());
}
