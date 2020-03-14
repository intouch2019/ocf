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

            $po_obj = $dbl->getPODetailsByDate($datetime);

            if ($po_obj) {
                $envelope = new SimpleXMLElement('<ENVELOPE/>');
                $name = "PurchaseOrderVoucher_" . $datetime . ".xml";
                $header = $envelope->addChild("HEADER");
                $header->addChild("TALLYREQUEST", "Import Data");
                $body = $envelope->addChild("BODY");
                $importdata = $body->addChild("IMPORTDATA");
                $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                $reqdesc->addChild("REPORTNAME", "Purchase Order Voucher");
                $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                $reqdata = $importdata->addChild("REQUESTDATA");
                foreach ($po_obj as $obj) {
                    if (isset($obj) && !empty($obj) && $obj != null) {
                        $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                        $salesdata = $tallymsg->addChild("PURCHASEORDER");
                        $voucher = $salesdata->addChild("PURCHASEORDERINFO");
                        $voucher->addChild("ID", $obj->id);
                        $voucher->addChild("PONUMBER", htmlspecialchars($obj->pono));
                        $poDate = date('d-m-Y', strtotime($obj->createtime));
                        $voucher->addChild("PODATE", $poDate);
                        $voucher->addChild("SUPPLIER", htmlspecialchars($obj->supplierName));
                        $voucher->addChild("QUANTITY", sprintf("%.4f",$obj->tot_qty));
                        $polines_obj = $dbl->getPOItems($obj->id);
                        
                        $gstTotal = 0;
                        $taxable_value = 0;
                        $cgst_value = 0;
                        $sgst_value = 0;
                        $total_value = 0;
                        $tot_value = 0;
                        foreach($polines_obj as $itemobj) {

                            $taxable_value = round($itemobj->rate * $itemobj->qty,2);
                            $cgst_value = round($taxable_value * $itemobj->cgstpct,2);
                            $sgst_value = round($taxable_value * $itemobj->sgstpct,2);
                            $gstTotal = $gstTotal + ($cgst_value + $sgst_value);
                            $tot_value = $tot_value + ($taxable_value + $cgst_value + $sgst_value);  
                        }
                        $voucher->addChild("GSTINPUT", sprintf("%.2f", $gstTotal));
                        $totalValue = $tot_value;
                        $voucher->addChild("TOTALVALUE", sprintf("%.2f", $totalValue));
                        $roundTotalPOVal = round($totalValue);
                        $roundoff = $roundTotalPOVal - $totalValue;
                        $voucher->addChild("ROUNDOFF", sprintf("%.2f", $roundoff));
                        $voucher->addChild("GRANDTOTALVALUE", $roundTotalPOVal);
                        $voucher->addChild("PAYMENTTERM", htmlspecialchars($obj->paymentterm));
                        $createDate = date('Y-m-dh:i:s', strtotime($obj->createtime));
                        $voucher->addChild("CREATEDATETIME", $createDate);
//                        $voucher->addChild("CREATEDATETIME", $obj->createtime);

                        foreach ($polines_obj as $obj) {
                            $taxable_value = round($obj->rate * $obj->qty,2);
                            $cgst_value = round($taxable_value * $obj->cgstpct,2);
                            $sgst_value = round($taxable_value * $obj->sgstpct,2);
                            $total_value = $taxable_value + $cgst_value + $sgst_value; 
                            
                            $voucher2 = $salesdata->addChild("POITEM");
                            $voucher2->addChild("CATEGORY", htmlspecialchars($obj->category));
//                            $desc1 = isset($obj->desc_1) && trim($obj->desc_1) != "" ? " , " . $obj->desc_1 . " mm" : "";
//                            $desc2 = isset($obj->desc_2) && trim($obj->desc_2) != "" ? " x " . $obj->desc_2 . " mm" : "";
//                            $thickness = isset($obj->thickness) && trim($obj->thickness) != "" ? " , " . $obj->thickness . " mm" : "";
//                            $spec = isset($obj->speci) && trim($obj->speci) != "" ? " ,spec-" . $obj->speci . "" : "";
//                            $itemname = $obj->prod . $desc1 . $desc2 . $thickness . $spec;
//                            $voucher2->addChild("PRODUCT", htmlspecialchars($itemname));
                            $voucher2->addChild("PRODUCTID", $obj->product_id);
                            $voucher2->addChild("HSNCODE", htmlspecialchars($obj->hsncode));
                            $voucher2->addChild("COLOR", htmlspecialchars($obj->color));
                            $voucher2->addChild("MANUFACTURER", htmlspecialchars($obj->manufacturer));
                            $voucher2->addChild("BRAND", htmlspecialchars($obj->brand));
                            $voucher2->addChild("SKU", htmlspecialchars($obj->sku));
                            $voucher2->addChild("LENGTH", htmlspecialchars($obj->length));
                            $voucher2->addChild("QUANTITY", sprintf("%.4f",$obj->qty));
                            $voucher2->addChild("NUMBEROFPIECES", round($obj->no_of_pieces, 2));
                            $voucher2->addChild("BASERATE", sprintf("%.2f", $obj->rate));
                            $taxableVal = sprintf("%.2f",$taxable_value);
                            $voucher2->addChild("TAXABLEVALUE", $taxableVal);
                            //$voucher2->addChild("LCRATE", $obj->lcrate);
                            $voucher2->addChild("CGSTPERCENTAGE", $obj->cgstpct);
                            $taxValcgst =  sprintf("%.2f", $taxableVal * $obj->sgstpct);
                            $voucher2->addChild("CGSTINPUT",$taxValcgst);
                            $voucher2->addChild("SGSTPERCENTAGE", $obj->sgstpct);
                            $voucher2->addChild("SGSTINPUT", $taxValcgst);
                            $voucher2->addChild("TOTALRATE", sprintf("%.2f", $obj->totalrate));
                            $itemTotalVal = $taxableVal + ($taxValcgst * 2);
                            $voucher2->addChild("TOTALVALUE", sprintf("%.2f", $total_value));
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
                file_put_contents('./log/purchaseOrderVoucherlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
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




