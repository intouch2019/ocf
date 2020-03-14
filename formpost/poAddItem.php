<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
print_r($_POST);
//return;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try {

    $poid = isset($poid) && trim($poid) != "" ? intval($poid) : false;
    if (!$poid) {
        $error['missing_poid'] = "Not able to get PO referance";
    }

    $prodid = isset($prodsel) && trim($prodsel) != "" ? intval($prodsel) : false;
    if (!$prodsel) {
        $error['missing_product'] = "Not able to get Product";
    }

    $ctgid = isset($catsel) && trim($catsel) != "" ? intval($catsel) : false;
    if (!$catsel) {
        $error['missing_category'] = "Not able to get Category";
    }
    
    $uom = isset($uom) && trim($uom) != "" ? trim($uom) : false;

    $arr = explode("::", $prodid);
    $prodid = $arr[0];

    $arr = explode("::", $ctgid);
    $ctgid = $arr[0];

    $length = isset($length) && trim($length) != "" ? $length : false;
    $colorsel = isset($colorsel) && trim($colorsel) != "" ? $colorsel : false;
    $brandsel = isset($brandsel) && trim($brandsel) != "" ? $brandsel : false;
    $manfsel = isset($manfsel) && trim($manfsel) != "" ? $manfsel : false;

    $qty = isset($qty) && trim($qty) != "" ? trim($qty) : false;
    if (!$qty) {
        $error['missing_qty'] = "Enter qty to order";
    }
    
    $mtqty = isset($mtqty) && trim($mtqty) != "" ? trim($mtqty) : false;
    if (!$mtqty) {
        $error['missing_qty'] = "Enter qty in MT to order";
    }

    $pieces = isset($pieces) && trim($pieces) != "" ? trim($pieces) : false;
    $rate = isset($rate) && trim($rate) != "" ? trim($rate) : false;
    if (!$rate) {
        $error['missing_rate'] = "Enter rate";
    }

    $lcrate = isset($lcrate) && trim($lcrate) != "" ? trim($lcrate) : 0;
    $cgstpct = 0.09;
    $cgstval = isset($cgst) && trim($cgst) != "" ? trim($cgst) : false;
    if (!$cgstval) {
        $error['missing_cgst'] = "CGST value is missing.";
    }
    $sgstpct = 0.09;
    $sgstval = isset($sgst) && trim($sgst) != "" ? trim($sgst) : false;
    if (!$sgstval) {
        $error['missing_sgst'] = "SGST value is missing.";
    }

    $totalrate = isset($totalrate) && trim($totalrate) != "" ? trim($totalrate) : false;
    if (!$totalrate) {
        $error['missing_tot_rate'] = "Rate (Rs./Kg) is missing.";
    }
    $totalvalue = isset($value) && trim($value) != "" ? trim($value) : false;
    if (!$totalvalue) {
        $error['missing_tot_value'] = "Total Value (Rs.) is missing.";
    }
    //dummy comment

    /*    $exdate = isset($exdate) && trim($exdate) != "" ? trim($exdate) : false;
      if(!$exdate){ $error['missing_exdate'] = "Enter expected date"; }
     */
    $exdate = null;

    function getEANCode($item_id) {
        $code = "8902"; // EAN-India prefix - 02 for CK - 01 for Limelight
        $code .= sprintf("%08d", $item_id); // 9 digits based on the item-id
        /* last digit is a checksum calculated as 
          The checksum is a Modulo 10 calculation:

          Add the values of the digits in the even-numbered positions: 2, 4, 6, etc.
          Multiply this result by 3.
          Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
          Sum the results of steps 2 and 3.
          The check character is the smallest number which, when added to the result in step 4, produces a multiple of 10.
         */
        $total = 0;
        for ($i = 1; $i <= 12; $i++) {
            $digit = intval(substr($code, $i - 1, 1));
            if (($i % 2) > 0) { // odd
                $total += $digit;
            } else { // even
                $total += 3 * $digit;
            }
        }
        $checksum = 10 - ($total % 10);
        if ($checksum == 10)
            $checksum = 0;
        return "$code$checksum";
    }

    if (count($error) == 0) {
        $poitem_id = $dbl->insertPOItem($poid, $prodid, $ctgid, $mtqty,$qty, $rate, $exdate, $length, $colorsel, $brandsel, $manfsel, $pieces, $lcrate, $cgstpct, $cgstval, $sgstpct, $sgstval, $totalrate, $totalvalue);
    
        $barcode=getEANCode($poitem_id);
        $dbl->AddSku($poitem_id,$barcode);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'po/additem/poid=' . $poid.'/uom='.$uom;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "po/additem/poid=" . $poid."/uom=".$uom;
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
