<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'lib/core/strutil.php';

$error = array();
try {
    $db = new DBConn();
    $dbl = new DBLogic();


    if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'shradhatally' && $_SERVER['PHP_AUTH_PW'] == 'intouch@2k18') {

        $datetime = isset($_GET['datetime']) ? ($_GET['datetime']) : false;
        if (!$datetime) {
            $error['datetime'] = "Not able to get date and time";
        }
        if (count($error) == 0) {
            
            $stc_obj = $dbl->getStockTransferChallanDetailsByDate($datetime);
            if ($stc_obj) {
                
                $envelope = new SimpleXMLElement('<ENVELOPE/>');
                $name = "StockTransferChallan_" . $datetime . ".xml";
                $header = $envelope->addChild("HEADER");
                $header->addChild("TALLYREQUEST", "Import Data");
                $body = $envelope->addChild("BODY");
                $importdata = $body->addChild("IMPORTDATA");
                $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                $reqdesc->addChild("REPORTNAME", "Stock Transfer Challan");
                $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                $reqdata = $importdata->addChild("REQUESTDATA");
                foreach ($stc_obj as $obj) {
                    if (isset($obj) && !empty($obj) && $obj != null) {
			                        
                        $st_info = $dbl->getStockTransferInfo($obj->st_id);
                        $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                        $salesdata = $tallymsg->addChild("STOCKTRANSFERCHALLAN");
                        $voucher = $salesdata->addChild("STOCKTRANSFERCHALLANINFO");
                        $voucher->addChild("ID", $obj->id);
                        $voucher->addChild("CHALLANNUMBER", htmlspecialchars($obj->challan_no));
                        $voucher->addChild("STOCKTRANSFERNUMBER", htmlspecialchars($obj->transferno));
                        $voucher->addChild("FROMLOCATION", htmlspecialchars(strtoupper($st_info->fromloc)));
                        $voucher->addChild("TOLOCATION", htmlspecialchars(strtoupper($st_info->toloc)));
                        $challanDate = date("d-m-Y", strtotime($obj->challan_date));
                        $voucher->addChild("CHALLANDATE", $challanDate);
                        $stockTransDate = date("d-m-Y", strtotime($obj->stock_transfer_date));
                        $voucher->addChild("STOCKTRANSFERDATE", $stockTransDate);
                        $createDatetime = date("Y-m-dh:i:s", strtotime($obj->challan_date));
                        $voucher->addChild("CREATEDATETIME", $createDatetime);
//                        $voucher->addChild("CREATEDATETIME", $obj->challan_date);
                        
                        $stclines_obj = $dbl->getStockTransChallanItems($obj->id);

                        foreach ($stclines_obj as $obj) {
//                            $desc1 = isset($obj->desc1) && trim($obj->desc1) != "" ? " , " . $obj->desc1 . " mm" : "";
//                            $desc2 = isset($obj->desc2) && trim($obj->desc2) != "" ? " x " . $obj->desc2 . " mm" : "";
//                            $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
//                            $spec = isset($obj->spec) && trim($obj->spec) != "" ? " ,spec-" . $obj->spec . "" : "";
//                            $itemname = $obj->product . $desc1 . $desc2 . $thickness . $spec;
                            
                            $voucher2 = $salesdata->addChild("STOCKTRANSFERCHALLANITEM");
                            $voucher2->addChild("ITEMID", htmlspecialchars($obj->id));
                            $voucher2->addChild("CATEGORY", htmlspecialchars($obj->category));
//                            $voucher2->addChild("PRODUCTNAME", htmlspecialchars($itemname));
                            $voucher2->addChild("PRODUCTID", $obj->prodid);
                            $voucher2->addChild("SPECIFICATION", htmlspecialchars($obj->spec));
                            $voucher2->addChild("HSNCODE", htmlspecialchars($obj->hsncode));
                            $voucher2->addChild("BATCHCODE", htmlspecialchars($obj->batchcode));
                            $voucher2->addChild("ACTUALQTY", sprintf("%.4f", $obj->actual_qty));
                            $voucher2->addChild("REQUIREDQTY", sprintf("%.4f", $obj->req_qty));
                            $voucher2->addChild("RATE", sprintf("%.2f", $obj->rate));
                            $voucher2->addChild("ACTUALNOOFPIECES", htmlspecialchars($obj->actual_no_of_pieces));
                        }
                    }
                }
//		return;
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
                file_put_contents('./log/stockTransferChallanlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
            } else {
                echo "No Record found for given date range";
            }
        } else {
            foreach ($error as $key => $value) {
                echo $value;
            }
        }
    } else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Incorrect Username or password.';
        exit;
    }
} catch (Exception $xcp) {
    print($xcp->getMessage());
}




