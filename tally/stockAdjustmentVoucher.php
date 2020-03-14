<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
try {
    $db = new DBConn();
    $dbl = new DBLogic();

    $datetime = isset($_GET['datetime']) ? ($_GET['datetime']) : false;
    if (!$datetime) {
        $error['datetime'] = "Not able to get date and time";
    }

    if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'shradhatally' && $_SERVER['PHP_AUTH_PW'] == 'intouch@2k18') {
        if (count($error) == 0) {
                $prods_obj = $dbl->getStockAdjustmentDetailsByDate($datetime);
                if ($prods_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "StockAdjustmentVoucher_" . $datetime . ".xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "STOCK ADJUSTMENT VOUCHER");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($prods_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            
//                            $desc1 = isset($obj->desc1) && trim($obj->desc1) != "" ? " , " . $obj->desc1 . " mm" : "";
//                            $desc2 = isset($obj->desc2) && trim($obj->desc2) != "" ? " x " . $obj->desc2 . " mm" : "";
//                            $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
//                            $itemname = $obj->prodname . $desc1 . $desc2 . $thickness;
                            
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $voucher = $tallymsg->addChild("STOCKADJUSTMENTVOUCHER");
                            
                            $voucher->addChild("CRCODE", htmlspecialchars($obj->dispname));
//                            $voucher->addChild("PRODUCTNAME", htmlspecialchars($itemname));
                            $voucher->addChild("PRODUCTID", $obj->prodid);
                            
                            $voucher->addChild("HSNCODE", htmlspecialchars($obj->hsncode));
                            $voucher->addChild("LENGHT", htmlspecialchars($obj->length));
                            $voucher->addChild("UOM", "MT");
                            $voucher->addChild("OLDSTOCK", sprintf ("%.4f",$obj->oldStock));
                            $voucher->addChild("ADDEDSTOCK", sprintf ("%.4f",$obj->addedstock));
                            $voucher->addChild("REQUESTEDBY", htmlspecialchars($obj->requestBy));
                            $voucher->addChild("APPROVEDBY", htmlspecialchars($obj->approvedBy));
                            $req_date = date('d-m-Y', strtotime($obj->createtime));
                            $voucher->addChild("REQUESTEDDATE", $req_date);
                            $date = date('d-m-Y', strtotime($obj->approvedate));
                            $voucher->addChild("APPROVEDDATE", $date);
                            $createDate = date('Y-m-dh:i:s', strtotime($obj->approvedate));
                            $voucher->addChild("CREATEDATETIME", $createDate);
//                            $voucher->addChild("CREATEDATETIME", $obj->approvedate);
                        }
                    }

                    header('Content-Disposition: attachment;filename=' . $name);
                    header('Content-Type: application/xml; charset=utf-8');
                    $string = $envelope->saveXML();
                    echo $string;
		    $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
                        "QUERYSTRING: ".$_SERVER['QUERY_STRING'].PHP_EOL.
                        "Intouch response: ".$string.PHP_EOL.
                        "Username: ".$_SERVER['PHP_AUTH_USER'].PHP_EOL. 
                        "-------------------------".PHP_EOL;
                $now = DateTime::createFromFormat('U.u', microtime(true));
                $local = $now->setTimeZone(new DateTimeZone('Asia/Kolkata'));
                file_put_contents('./log/stockadjustmentvoucherlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
                } else {
                    echo "No Record found for given date range";
                }
        } else {
            foreach ($error as $key => $value) {
                echo $value;
            }
        }
    }else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Incorrect Username or password.';
        exit;
    }
} catch (Exception $xcp) {
    print($xcp->getMessage());
}




