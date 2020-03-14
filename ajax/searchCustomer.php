<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$userid = getCurrStoreId();
$error = array();
try{
    $db = new DBConn();
    $dbl = new DBLogic();
    
    $cphone = isset($_GET['cphone']) ? ($_GET['cphone']) : false;
    if(!$cphone){ $error['cphone'] = "Not able to get Customer Phone Number"; }
    
    if(count($error) == 0){
        $obj_customer = $dbl->getCustomerByPhone($cphone);
        if($obj_customer == NULL){
            $resp = array(
                "error" => "1",
                "msg" => "No customer record found with this phone number",
                "phone" => $cphone,
            );
        }else{
            $resp = array(
                "error" => "0",
                "id" => $obj_customer->id,
                "name" => $obj_customer->name,
                "address" => $obj_customer->address,
                "state_id" => $obj_customer->state_id,
                "city" => $obj_customer->city,
                "phone" => $obj_customer->phone,
                "email" => $obj_customer->email,
                "gstno" => $obj_customer->gstno,
                "panno" => $obj_customer->panno,
		"isregister" => $obj_customer->isregister
            );
        }
        echo json_encode($resp);
    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get Prod Id"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
