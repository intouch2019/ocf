<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";

extract($_GET);
$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    $dcid = "NULL";
    $reason=1;
    $sign=1;
    $crid = isset($crid) && trim($crid) != "" ? $crid : NULL;
    $prodid = isset($prodid) && trim($prodid) != "" ? $prodid : NULL;
    $id = isset($id) && trim($id) != "" ? $id : NULL;
    $addedstock = isset($addedstock) && trim($addedstock) != "" ? $addedstock : NULL;
    $tranid=$id;
$qty=$addedstock;
$rate=0;
    if($crid == NULL){ 
        $resp = array(
            "error" => "1",
            "msg" => "Please Select CR / Select All"
        );
        echo json_encode($resp);
        
    }else{
        $prodName=$dbl->getProductById($prodid);
        
        $dbl->approveStockAdjustment($id,$crid,$userid,$prodid);
        $obj= $dbl->getLatestBatchCodeByProdID($prodid,$crid);
        $batchcode=$obj->batchcode;
        $dbl->updateStock($crid, $dcid, $prodid, $batchcode, $reason, $sign,$qty, $rate, $tranid);
        $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        echo json_encode($resp);
       
        $currDate = date("Y-m-d");
        $usertype = UserType::HO;
        $obj_user = $dbl->getUserInfoByType($usertype);
        $crname = "";
        if($crid > 0){
            $objcr = $dbl->getCRInfoById($crid);
            if($objcr != NULL){
                $crname = $objcr->dispname;
            }
        }else{
            $crname = "All CR";
        }
          
        $emailid = "";
        if($obj_user != NULL && isset($obj_user)){
            $emailid = $obj_user->email;
        }
         
        //email sending
        
        $subject = "Approved Product Stock : Stock Adjustment Approved (".ddmmyy($currDate).")";
        $body = '<p>New stock adjustment Approved for '.strtoupper($crname).'<br>'
                 . '<b>'.$addedstock.'MT</b> Stock added for product <b>'.$prodName->name.' '.$prodName->desc1.' x '.$prodName->desc2.' x '.$prodName->thickness.'</b><br>'
              . ' Please Check</p>';
        
        $emailHelper = new EmailHelper();
        $emailHelper->send(array($emailid), $subject, $body);
        
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
