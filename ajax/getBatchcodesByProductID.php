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
    if(!$prodid){ $error['prodid'] = "Not able to get prod Id"; }
   
    $obj_rfc = $dbl->getCRDetailsByUserId($userid);
    if(!isset($obj_rfc)){
        if(!$obj_rfc){ $error['rfc'] = "Not able to get RFC master"; }
    }
    $usertype = $user->usertype;
    
    if(count($error) == 0){
        $availableqty = 0;
        $batchcode = "";
        $objBatchcodes = $dbl->getBatchCodes($prodid,$usertype,$obj_rfc->id);
        if($objBatchcodes != NULL){
//            $availableqty = $obj->qty;
//            $batchcode = $obj->batchcode;
            ?><option selected="selected">Select Batch Code :</option><?php
	foreach($objBatchcodes as $batchcodes){
		?>
        <option value="<?php echo $batchcodes->id."::".$batchcodes->length; ?>"><?php echo $batchcodes->batchcode; ?></option>
        <?php
	}
//            $resp = array(
//                "error" => "0",
//                "availableqty" => $availableqty,
//                "batchcode" => $batchcode
//            );
//            echo json_encode($resp);
        }else{
        $resp = array(
            "error" => "1",
            "msg" => "Not able to get RFC master"
        );
        echo json_encode($resp);
    }
    }
}catch(Exception $xcp){
    print($xcp->getMessage());
}
