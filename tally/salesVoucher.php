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
//    $salesId = isset($_GET['salesid']) ? ($_GET['salesid']) : false;
    if (!$datetime) {
        $error['datetime'] = "Not able to get date and time";
    }
//    if (!$salesId) {
//        $error['salesid'] = "Not able to get sales id";
//    }

    if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'shradhatally' && $_SERVER['PHP_AUTH_PW'] == 'intouch@2k18') {
        if (count($error) == 0) {

            $sales_obj = $dbl->getSaleDetailsByDate($datetime);
            
//            return;
            if ($sales_obj) {
                $envelope = new SimpleXMLElement('<ENVELOPE/>');
                $name = "SalesVoucher_" . $datetime . ".xml";
                $header = $envelope->addChild("HEADER");
                $header->addChild("TALLYREQUEST", "Import Data");
                $body = $envelope->addChild("BODY");
                $importdata = $body->addChild("IMPORTDATA");
                $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                $reqdesc->addChild("REPORTNAME", "Sales Voucher");
                $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                //echo "gererrere";
                $reqdata = $importdata->addChild("REQUESTDATA");
                foreach ($sales_obj as $obj) {
                    if (isset($obj) && !empty($obj) && $obj != null) {

                        $crdetails = $dbl->getCRInfoById($obj->crid);
                        $invNumArr = explode("-", $obj->invoice_no);
                        $invRef = "";
                        $invNum = "";
                        if (sizeof($invNumArr) == 2) {
                            $invRef = $invNumArr[0];
                            $invNum = $invNumArr[1];
                        }

                        $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                        $salesdata = $tallymsg->addChild("INVOICE");

                        $voucher = $salesdata->addChild("INVOICEINFO");
                        $voucher->addChild("ID", $obj->id);
                        $voucher->addChild("CRID", htmlspecialchars($obj->crid));
                        $voucher->addChild("CRCODE", htmlspecialchars(strtoupper($crdetails->dispname)));
                        $voucher->addChild("REFERENCENUMBER", htmlspecialchars($invRef));
                        $voucher->addChild("INVOICENUMBER", htmlspecialchars($invNum));
                        $date = date('d-m-Y', strtotime($obj->createtime));
                        $voucher->addChild("INVOICEDATE", $date);

                        $cust_name = "";
                        $cust_phone = "";
                        if ($obj->sale_reg_type == 1) {
                            $cust_obj = $dbl->getCustomerById($obj->customer_id);
                            if ($cust_obj) {
                                $cust_name = $cust_obj->name;
                                $cust_phone = $cust_obj->phone;
                                $voucher->addChild("CUSTOMERID", $cust_obj->id);
                            }
                        } else {
                            $cust_obj = $dbl->getDefaultCustByCRid($obj->crid);
                            if ($cust_obj) {
                                $cust_name = $cust_obj->cust_name;
                                $cust_phone = $cust_obj->cust_phone;
                                $voucher->addChild("CUSTOMERID", null);
                            }
                        }

                        $voucher->addChild("CUSTOMERNAME", htmlspecialchars($cust_name));
                        $voucher->addChild("CUSTOMERPHONE", htmlspecialchars($cust_phone));
                        if($obj->isregister == 0){
                            $obj->isregister = 2;
                        }
                        $voucher->addChild("CUSTOMERTYPE",$obj->isregister);
                        if ($obj->invoice_type == 0) {
                            $voucher->addChild("INVOICETYPE", "SALE");
                        }
                        if ($obj->paymentmode == 0) {
                            $voucher->addChild("PAYMENTMODE", "CASH");
                        } elseif ($obj->paymentmode == 1) {
                            $voucher->addChild("PAYMENTMODE", "DEBIT CARD");
                        } elseif ($obj->paymentmode == 2) {
                            $voucher->addChild("PAYMENTMODE", "CREDIT CARD");
                        } elseif ($obj->paymentmode == 4) {
                            $voucher->addChild("PAYMENTMODE", "NET BANKING");
                        }                        

                        $invitemsobjs = $dbl->getInvoiceItemsbySaleid($obj->id);
                        $cnt = 0;
                        $srno = 0;
                        $items_per_page = 20;
                        $total_qty = 0;
                        $total_tot = 0;
                        $total_disc = 0;
                        $total_taxable_amt = 0;
                        $total_gst_rate = 0;
                        $total_gst_amt = 0;
                        $total_line_total = 0;
                        $total_sgst_amt = 0;
                        $total_cgst_amt = 0;
                        $all_total_val = 0;
                        $taxable_value = 0;
                        $cgst_value = 0;
                        $sgst_value = 0;
                        $rounde_line_total = 0;

                        if (!empty($invitemsobjs)) {
                            foreach ($invitemsobjs as $invitemobj) {
                                if (isset($invitemobj) && !empty($invitemobj) && $invitemobj != null) {
                                    $cnt++;
                                    $srno++;

                                    $baseRate = round($invitemobj->rate, 2, PHP_ROUND_HALF_UP);
                                    $baseRate = sprintf("%.2f", $baseRate);
                                    $roundQty = round($invitemobj->qty, 4, PHP_ROUND_HALF_UP);
                                    $roundQty = sprintf("%.4f", $roundQty);

                                    $total = $roundQty * $baseRate;
                                    $total_tot = $total_tot + $total;
                                    $total_disc = 0;

                                    $total_gst_rate = $total_gst_rate + $invitemobj->sgst_percent;
                                    $lineTotal = $invitemobj->cgst_amt + $invitemobj->cgst_amt + $baseRate;
                                    $total_line_total = $total_line_total + $lineTotal;

//                                    $desc1 = isset($invitemobj->desc_1) && trim($invitemobj->desc_1) != "" ? " , " . $invitemobj->desc_1 . " mm" : "";
//                                    $desc2 = isset($invitemobj->desc_2) && trim($invitemobj->desc_2) != "" ? " x " . $invitemobj->desc_2 . " mm" : "";
//                                    $thickness = isset($invitemobj->thickness) && trim($invitemobj->thickness) != "" ? " , " . $invitemobj->thickness . " mm" : "";
//                                    $spec = isset($invitemobj->spec) && trim($invitemobj->spec) != "" ? " ,spec-" . $invitemobj->spec . "" : "";
//                                    $itemname = $invitemobj->product . $desc1 . $desc2 . $thickness . $spec;
                                    $roundTaxableAmt = round($invitemobj->taxable, 2);
                                    $roundCgstAmt = round($invitemobj->cgst_amt, 2);
                                    $roundSgstAmt = round($invitemobj->sgst_amt, 2);
                                    $roundLineTotal = round($lineTotal, 2, PHP_ROUND_HALF_UP);
                                    $roundLineTotal = sprintf("%.2f", $roundLineTotal);
                                    $total_qty = $total_qty + $roundQty;
                                    $tot_val = round($roundLineTotal * $roundQty, 2);
                                    $taxable_value = round(($baseRate * $roundQty), 2, PHP_ROUND_HALF_UP);
                                    $taxable_value = sprintf("%.2f", $taxable_value);
                                    $total_taxable_amt = $total_taxable_amt + $taxable_value;
                                    $cgst_value = $taxable_value * ($invitemobj->cgst_percent / 100);
                                    $round_cgst_value = round($cgst_value, 2, PHP_ROUND_HALF_UP);
                                    $round_cgst_value = sprintf("%.2f", $round_cgst_value);
                                    $total_cgst_amt = $total_cgst_amt + $round_cgst_value;
                                    $sgst_value = $taxable_value * ($invitemobj->sgst_percent / 100);
                                    $round_sgst_value = round($sgst_value, 2, PHP_ROUND_HALF_UP);
                                    $round_sgst_value = sprintf("%.2f", $round_sgst_value);
                                    $total_sgst_amt = $total_sgst_amt + $round_sgst_value;
                                    $line_Total = $taxable_value + $round_cgst_value + $round_sgst_value;
                                    $rounde_line_total = round($line_Total, 2, PHP_ROUND_HALF_UP);
                                    $rounde_line_total = sprintf("%.2f", $rounde_line_total);
                                    $all_total_val = $all_total_val + $rounde_line_total;

                                    $voucher2 = $salesdata->addChild("INVOICEITEM");
                                    
                                    $voucher2->addChild("PRODUCTID", $invitemobj->product_id);
//                                    $voucher2->addChild("PRODUCT", $itemname);
                                    $voucher2->addChild("BATCHCODE", $invitemobj->batchcode);
                                    $voucher2->addChild("QUANTITY", $roundQty);
                                    $voucher2->addChild("ACTUALRATE", $baseRate);
                                    $voucher2->addChild("MRP", $roundLineTotal);
                                    $voucher2->addChild("CUTTINGCHARGES", $invitemobj->cuttingcharges);
                                    if (isset($invitemobj->paymentcharges)) {
                                        $voucher2->addChild("PAYMENTCHARGES", $invitemobj->paymentcharges);
                                    }
                                    $voucher2->addChild("TAXABLEVALUE", $taxable_value);
                                    if ($crdetails->state == "22") {
                                        $voucher2->addChild("CGSTPERCENTAGE", trim($invitemobj->cgst_percent));
                                        $voucher2->addChild("CGSTOUTPUT", $round_cgst_value);
                                        $voucher2->addChild("SGSTPERCENTAGE", trim($invitemobj->sgst_percent));
                                        $voucher2->addChild("SGSTOUTPUT", $round_sgst_value);
                                    } else {
                                        $voucher2->addChild("IGSTPERCENTAGE", trim($invitemobj->igst_percent));
                                        $voucher2->addChild("IGSTOUTPUT", trim($invitemobj->igst_amt));
                                    }
                                    $voucher2->addChild("TOTAL", $rounde_line_total);
                                }
                            }
                        }
                        $round_all_total_val = round($all_total_val, 2, PHP_ROUND_HALF_UP);
                        $round_all_total_val = sprintf("%.2f", $round_all_total_val);
                        $full_rounded_total = round($round_all_total_val, 0, PHP_ROUND_HALF_UP);
                        $roundVal = $full_rounded_total - $round_all_total_val;
                        $roundVal = sprintf("%.2f", $roundVal);
                        $voucher->addChild("NETVALUE", $round_all_total_val);
                        $voucher->addChild("ROUNDOFF", $roundVal);
                        $voucher->addChild("INVOICEVALUE", $full_rounded_total);
                        $createDate = date('Y-m-dh:i:s', strtotime($obj->createtime));
                        $voucher->addChild("CREATEDATETIME", $createDate);
//                        $voucher->addChild("CREATEDATETIME", $obj->createtime);                      
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
                file_put_contents('./log/salesvoucherlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
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




