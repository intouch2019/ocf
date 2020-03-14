<?php 
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once("session_check.php");

$pur_in_id = isset($_GET['pur_in_id']) ? ($_GET['pur_in_id']) : false;
if (!$pur_in_id) { return error("missing parameters"); }

try {
    $pagelist = array();
    //$count=0;
    $dbLogic = new DBLogic(); 
    $status = PurchaseInStatus::created;
    $purin_no = "";
        $piobj = $dbLogic->getpurinno();
        if(isset($hqobj)){
//            print"found";
            $purin_no = trim(str_replace("P","",$piobj->purin_no));  
//             print "<br>hqno1====$hqno";
            $purin_no = sprintf('%07d', $purin_no+1);
//            print "<br>hqno2====$hq_no";
            $purin_no = "P".$purin_no;
//            print "<br>hqno3====$hq_no";
        }else{
//            print"not found<br>";
            $purin_no =  "P0000000";    
//            print $hq_no;
        }
    
    
    $dbLogic->updatePurchaseInStatus($pur_in_id,$status,$purin_no);
    $dbLogic->updatePurInNo($purin_no);    
    success("success");
    
} catch(Exception $xcp){
    echo "error:There was a problem processing your request. Please try again later.";
 //   return;
}

function error($msg) {
    print json_encode(array(
            "error" => "1",
            "message" => $msg
            ));
}

function success($msg) {
    //print json_encode($pagelis);
    print json_encode(array(
            "error" => "0",
            "message" => $msg
            ));
}
?>