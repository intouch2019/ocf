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
    
     


    if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'shradhatally' && $_SERVER['PHP_AUTH_PW'] == 'intouch@2k18') {

       $datetime = isset($_GET['datetime']) ? ($_GET['datetime']) : false;
        if (!$datetime) {
            $error['datetime'] = "Not able to get date and time";
        }
        
        if (count($error) == 0) {
                $grn_obj = $dbl->getGRNDetailsByDate($datetime);

                if ($grn_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "GRNVoucher_" . $datetime . ".xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "GRN Voucher");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($grn_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $salesdata = $tallymsg->addChild("GRN");
                            $voucher = $salesdata->addChild("GRNINFO");
                            $voucher->addChild("ID", $obj->id);
                            $voucher->addChild("GRNNUMBER", htmlspecialchars($obj->grnno));
                            $voucher->addChild("DC", htmlspecialchars($obj->dc_name));
                            $voucher->addChild("SUPPLIER", htmlspecialchars($obj->supplier));
                            $voucher->addChild("PONUMBER", htmlspecialchars($obj->pono));
                            $poDate = date('d-m-Y', strtotime($obj->podate));
                            $voucher->addChild("PODATE", $poDate);
                            $date = date('d-m-Y', strtotime($obj->grndate));
                            $voucher->addChild("GRNDATE", $date);
                            $voucher->addChild("QUANTITY", sprintf ("%.4f",$obj->tot_qty));
                            $totalValue = $obj->tot_value;
                            $voucher->addChild("TOTALVALUE", sprintf ("%.2f",$totalValue));
                            $roundTotalPOVal= round($totalValue);
                            $roundoff = $roundTotalPOVal- $totalValue;
                            $voucher->addChild("ROUNDOFF", sprintf ("%.2f",$roundoff));
                            $voucher->addChild("GRANDTOTALVALUE", $roundTotalPOVal);
                            $grnlines_obj = $dbl->getGRNItems($obj->id);
                            
                            foreach ($grnlines_obj as $objs) {
                                $newDate1 = $objs->createtime;
                            }
                            $createDate = date('Y-m-dh:i:s', strtotime($newDate1));
                            $voucher->addChild("CREATEDATETIME", $createDate);
//                            $voucher->addChild("CREATEDATETIME", $newDate1);
                            
                            foreach ($grnlines_obj as $obj) {
                                $voucher2 = $salesdata->addChild("GRNITEM");
                                $voucher2->addChild("CATEGORY", "Mild Steel");
//                                $desc1 = isset($obj->desc_1) && trim($obj->desc_1) != "" ? " , " . $obj->desc_1 . " mm" : "";
//                                $desc2 = isset($obj->desc_2) && trim($obj->desc_2) != "" ? " x " . $obj->desc_2 . " mm" : "";
//                                $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
//                                $itemname = $obj->prod . $desc1 . $desc2 . $thickness;
                                
//                                $voucher2->addChild("PRODUCT", htmlspecialchars($itemname));
                                $voucher2->addChild("PRODUCTID", $obj->product_id);
                                $voucher2->addChild("HSNCODE", htmlspecialchars($obj->hsncode));
                                $voucher2->addChild("COLOR", htmlspecialchars($obj->color));
                                $voucher2->addChild("MANUFACTURER", htmlspecialchars($obj->manufacturer));
                                $voucher2->addChild("BRAND", htmlspecialchars($obj->brand));
                                $voucher2->addChild("BATCHCODE", htmlspecialchars($obj->batchcode));
                                $voucher2->addChild("LENGTH", htmlspecialchars($obj->length));
                                $voucher2->addChild("QUANTITY", sprintf("%.4f",$obj->qty));
                                $voucher2->addChild("NUMBEROFPIECES", round($obj->no_of_pieces, 2));
                                $rate = sprintf ("%.2f",$obj->rate + $obj->lcrate);
                                $voucher2->addChild("BASERATE", $rate);
                                $voucher2->addChild("TAXABLEVALUE", sprintf("%.2f",$rate * $obj->qty));
                                $voucher2->addChild("CGSTPERCENTAGE", $obj->cgstpct);
                                $voucher2->addChild("CGSTAMOUNT", sprintf ("%.2f",$obj->cgstval * $obj->qty));
                                $voucher2->addChild("SGSTPERCENTAGE", $obj->sgstpct);
                                $voucher2->addChild("SGSTAMOUNT", sprintf ("%.2f",$obj->sgstval * $obj->qty));
                                $voucher2->addChild("TOTALRATE", sprintf ("%.2f",$obj->totalrate));
                                $voucher2->addChild("TOTALVALUE", sprintf ("%.2f",$obj->totalvalue));
                            }
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
                file_put_contents('./log/grnvoucherlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
                } else {
                    echo "No Record found for given date range";
                }
        } else {
            foreach ($error as $key => $value) {
                echo $value;
            }
        }
    }else{
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Incorrect Username or password.';
        exit;
    }
} catch (Exception $xcp) {
    print($xcp->getMessage());
}




