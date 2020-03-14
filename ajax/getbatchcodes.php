<?php 
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once("session_check.php");

$prodid = isset($_GET['prodid']) ? ($_GET['prodid']) : false;
if(!$prodid){ $error['prodid'] = "Not able to get Product Id"; }
$userid = getCurrStoreId();
$user = getCurrStore();
$error = array();
try {
    $db = new DBConn();
    $dbl = new DBLogic();
   
    $usertype = $user->usertype;
    if(count($error) == 0){
        $availableqty = 0;
        $batchcode = "";
        $objBatchcodes = $dbl->getBatchCodes($prodid,$usertype,$userid);
        foreach($objBatchcodes as $obj){  
            $pagelist[] = $obj->id."::".$obj->batchcode."::".$obj->qty;    
                
        }
        if ($pagelist) { success($pagelist); }
                else { error("Page Not Found"); }
        }
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

function success($pagelis) {
    print json_encode($pagelis);
}
?>