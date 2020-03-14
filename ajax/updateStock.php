<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/email/EmailHelper.php";


$userid =1;
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    $prodId= isset($_GET['prodId']) ? ($_GET['prodId']) : false;
    $crid = isset($_GET['crid']) ? ($_GET['crid']) : false;
    $addQty = isset($_GET['addQty']) ? ($_GET['addQty']) : false;
   
       
    
    if(count($error) == 0){
        $selectStockdetailsQry="select SQL_CALC_FOUND_ROWS SUM(round(a.qty,4)) as stockQty ,max(b.hsncode) as hsncode,max(b.name) as Name,max(b.desc1) as desc1,max(b.desc2) as desc2,max(b.thickness) as thickness,max(b.stdlength) as stdlength from it_stockcurr a,it_products b where b.active=1  and a.prodid=b.id and b.id=$prodId and a.crid= $crid";
           $qryObj = $db->fetchObjectArray($selectStockdetailsQry); 
           foreach ($qryObj as $obj){
            $oldqty=$obj->stockQty;
            $hsncode=$obj->hsncode;
            $name=$obj->Name;
            $desc1=$obj->desc1;
            $desc2=$obj->desc2;
            $thickness=$obj->thickness;
            $length=$obj->stdlength;
           $id=$dbl->insertStockAdjustmentDetails($crid,$prodId,$name,$desc1,$desc2,$thickness,$length,$hsncode,$oldqty,$addQty);
           $id1=$dbl->insertStockAdjustmentHeader($crid,$prodId,$userid);
         
        $usertype = UserType::Director;
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
        $currDate = date("Y-m-d");
        //email sending
        $subject = "Approval awaiting : Stock Adjustment uploaded (".ddmmyy($currDate).")";
        $body = '<p>New stock adjustment uploaded for '.strtoupper($crname).'<br>'
              . '<b>'.$addQty.'MT</b> Stock added for product <b>'.$name.' '.$desc1.' x '.$desc2.' x '.$thickness.'</b><br>'
              . 'Please approve the Added Stock</p>';
        $emailHelper = new EmailHelper();
        $emailHelper->send(array($emailid), $subject, $body);

            }
            
         $usertype1 = UserType::PurchaseOfficer;
         $obj_user1 = $dbl->getUserInfoByType($usertype1);
          $emailid = "";
        if($obj_user1 != NULL && isset($obj_user1)){
            $emailid = $obj_user1->email;
        }
        $currDate = date("Y-m-d");
          $subject = "Approval awaiting : Stock Adjustment uploaded (".ddmmyy($currDate).")";
        $body = '<p>New stock adjustment uploaded for '.strtoupper($crname).'<br>'
              . '<b>'.$addQty.'MT</b> Stock added for product <b>'.$name.' '.$desc1.' x '.$desc2.' x '.$thickness.'</b><br>'
              . 'Please approve the Added Stock</p>';
        $emailHelper = new EmailHelper();
        $emailHelper->send(array($emailid), $subject, $body);
       $resp = array(
            "error" => "1",
            "msg" => "Success"
        );
        echo json_encode($resp);

    }else{
        $resp = array(
            "error" => "1",
           "msg" => "Not able to update stock"
        );
        echo json_encode($resp);
        }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
