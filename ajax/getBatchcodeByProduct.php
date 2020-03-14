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
    
    $stockcurrid = isset($_GET['stockcurrid']) ? ($_GET['stockcurrid']) : false;
    if(!$stockcurrid){ $error['stockcurrid'] = "Not able to get Stock Item Id"; }
    
    $fromlocid = isset($_GET['fromlocid']) ? ($_GET['fromlocid']) : false;
    if(!$fromlocid){ $error['fromlocid'] = "Not able to get From Loc Id"; }
    
    $fromloctype = isset($_GET['fromloctype']) ? ($_GET['fromloctype']) : false;
    if(!$fromloctype){ $error['fromloctype'] = "Not able to get From Loc TYPE"; }
    
    $challanid = isset($_GET['challanid']) ? ($_GET['challanid']) : false;
    if(!$challanid){ $error['challanid'] = "Not able to get challanid"; }
    
    
    if(count($error) == 0){
        $availableqty = 0;
        $batchcode = "";
        $objBatchcodes = $dbl->getBatchCodeByProductid($stockcurrid,$fromlocid,$fromloctype,$challanid);
        // print_r($objBatchcodes);
        if($objBatchcodes != NULL){
            ?><?php
            foreach($objBatchcodes as $batchcodes){
                $qty = round($batchcodes->qty, 4, PHP_ROUND_HALF_UP);
                $roundQty = sprintf("%.4f", $qty); 
                    ?>
            <option value="<?php echo $batchcodes->id."::".$batchcodes->batchcode."::".$batchcodes->length."::".$roundQty."::".$batchcodes->no_of_pieces; ?>"><?php echo $batchcodes->batchcode.", Qty - ".$roundQty.", Length - ".$batchcodes->length; ?></option>
            <?php
            }
        }
    }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
