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
    $usertype = $user->usertype;
    if(count($error) == 0){
        $availableqty = 0;
        $batchcode = "";
        $objBatchcodes = $dbl->getBatchCodes($prodid,$usertype,$userid);
        if($objBatchcodes != NULL){
//            $availableqty = $obj->qty;
//            $batchcode = $obj->batchcode;
            ?><?php
	foreach($objBatchcodes as $batchcodes){
		?>
<!--        <option value="<?php echo $batchcodes->qty; ?>"><?php echo $batchcodes->batchcode.", Qty - ".$batchcodes->qty.", Length - ".$batchcodes->length; ?></option>-->
        <option value="<?php echo $batchcodes->id."::".$batchcodes->batchcode."::".$batchcodes->length."::".$batchcodes->qty; ?>"><?php echo $batchcodes->batchcode.", Qty - ".$batchcodes->qty.", Length - ".$batchcodes->length; ?></option>
        <?php
	}
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
