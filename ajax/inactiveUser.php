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
    
    $userid = isset($_GET['userid']) ? ($_GET['userid']) : false;
    if(!$userid){ $error['missing_userid'] = "Not able to get User Id"; }

    
    if(count($error) == 0){
        $updatedId = $dbl->inactivateUserByUserId($userid);
        
        if($updatedId != 0){
            $resp = array(
            "error" => "0",
            "msg" => "success"
        );
        echo json_encode($resp);
        } else {
            $resp = array(
            "error" => "1",
            "msg" => "Not able to update user details"
        );
        echo json_encode($resp);
        }

    }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get User Id"
        );
        echo json_encode($resp);
    }

    
}catch(Exception $xcp){
    print($xcp->getMessage());
}

