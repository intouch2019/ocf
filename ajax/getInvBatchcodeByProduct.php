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
    
    $prodid = isset($_GET['prodid']) ? ($_GET['prodid']) : false;
    if(!$prodid){ $error['prodid'] = "Not able to get Product Id"; }
    
    $itemid = isset($_GET['itemid']) ? ($_GET['itemid']) : false;
    if(!$itemid){ $error['itemid'] = "Not able to get Item Id"; }
    
    $usertype = $user->usertype;
    if(count($error) == 0){
        $availableqty = 0;
        $batchcode = "";
        $batchcodes = $dbl->getInvItemDetails($itemid,$prodid,$usertype,$userid);
        if($batchcodes != NULL){
//            $availableqty = $obj->qty;
//            $batchcode = $obj->batchcode;
            ?><?php
	//foreach($objBatchcodes as $batchcodes){
		?>
<!--        <option value="<?php echo $batchcodes->qty; ?>"><?php echo $batchcodes->batchcode.", Qty - ".$batchcodes->qty; ?></option>-->
        <option value="<?php echo $batchcodes->id."::".$batchcodes->batchcode."::".$batchcodes->qty; ?>"><?php echo $batchcodes->batchcode.", Qty - ".$batchcodes->qty; ?></option>
        <?php
	//}
//            $resp = array(
//                "error" => "0",
//                "availableqty" => $availableqty,
//                "batchcode" => $batchcode
//            );
//            echo json_encode($resp);
        }
    }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
