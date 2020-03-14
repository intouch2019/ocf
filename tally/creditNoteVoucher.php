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

                $creditnote_obj = $dbl->getCreditNoteDetailsByDate($datetime);
                if ($creditnote_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "CreditNoteVoucher_" . $datetime . "xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "CreditNote Voucher");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    //echo "gererrere";
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($creditnote_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $salesdata = $tallymsg->addChild("CREDITNOTE");
                            $voucher = $salesdata->addChild("CREDITNOTEINFO");
                            
                            
                            $invoiceitems_obj = $dbl->getCNItems($obj->id);
                            
                            $total_qty = 0;
                            $total_tot = 0;
                            $total_disc = 0;
                            $total_taxable_amt =0;
                            $total_gst_rate =0;
                            $total_gst_amt =0;
                            $total_line_total = 0;
                            $total_sgst_amt = 0;
                            $total_cgst_amt = 0;
                            $all_total_val = 0;
                            $crdetails = $dbl->getCRInfoById($obj->crid);
                            
                            foreach ($invoiceitems_obj as $invitemobj) {
                                
//                                $desc1 = isset($invitemobj->desc_1) && trim($invitemobj->desc_1) != "" ? " , ".$invitemobj->desc_1." mm" : "";
//                                $desc2 = isset($invitemobj->desc_2) && trim($invitemobj->desc_2) != "" ? " x ".$invitemobj->desc_2." mm" : "";
//                                $thickness = isset($invitemobj->thickness) && trim($invitemobj->thickness) != "" ? " , ".$invitemobj->thickness." mm" : "";
//                                $spec  = isset($invitemobj->spec) && trim($invitemobj->spec) !="" ? " ,spec-".$invitemobj->spec."":""; 
//                                $itemname = $invitemobj->product.$desc1.$desc2.$thickness.$spec;

                                $qty = round($invitemobj->qty, 4, PHP_ROUND_HALF_UP);
                                $roundedQty = sprintf("%.4f", $qty);

                                $rate = round($invitemobj->rate, 2, PHP_ROUND_HALF_UP);
                                $roundedRate = sprintf("%.2f", $rate);

                                $taxableValue = $roundedQty * $roundedRate;
                                $taxableValue = round($taxableValue, 2, PHP_ROUND_HALF_UP);
                                $roundedTaxableValue = sprintf("%.2f", $taxableValue);

                                $cgstRate = $roundedRate * ($invitemobj->cgstpct / 100);
                                $roundcgstRate = round($cgstRate, 2, PHP_ROUND_HALF_UP);
                                $roundedcgstRate = sprintf("%.2f", $roundcgstRate);

                                $sgstRate = $roundedRate * ($invitemobj->sgstpct / 100);
                                $roundsgstRate = round($sgstRate, 2, PHP_ROUND_HALF_UP);
                                $roundedsgstRate = sprintf("%.2f", $roundsgstRate);
                                
                                $igstRate = $roundedRate * ($invitemobj->igstpct / 100);
                                $roundigstRate = round($igstRate, 2, PHP_ROUND_HALF_UP);
                                $roundedigstRate = sprintf("%.2f", $roundigstRate);

                                $cgstVal = $roundedTaxableValue * ($invitemobj->cgstpct / 100);
                                $roundcgstVal = round($cgstVal, 2, PHP_ROUND_HALF_UP);
                                $roundedcgstVal = sprintf("%.2f", $roundcgstVal);

                                $sgstVal = $roundedTaxableValue * ($invitemobj->sgstpct / 100);
                                $roundsgstVal = round($sgstVal, 2, PHP_ROUND_HALF_UP);
                                $roundedsgstVal = sprintf("%.2f", $roundsgstVal);

                                $itemRate = $roundedRate + $roundedcgstRate + $roundedsgstRate;
                                $roundItemRate = round($itemRate, 2, PHP_ROUND_HALF_UP);
                                $roundedItemRate = sprintf("%.2f", $roundItemRate);

                                $itemValue = $roundedTaxableValue + $roundedcgstVal + $roundedsgstVal;
                                $roundItemValue = round($itemValue, 2, PHP_ROUND_HALF_UP);
                                $roundeditemValue = sprintf("%.2f", $roundItemValue);

                                $total_qty = $total_qty + $roundedQty;
                                $total_tot = $total_tot + $roundedTaxableValue;
                                $total_disc = 0;

                                $total_gst_rate = $total_gst_rate + $invitemobj->sgstpct;
//                                $tot_cgst_val = $tot_cgst_val + $roundedcgstVal;
//                                $tot_igst_val = $tot_igst_val + $invitemobj->igstval;
                                $total_line_total = $total_line_total + $roundeditemValue ;
                                $total_taxable_amt = $total_taxable_amt + $roundedTaxableValue;
                                $total_sgst_amt = $total_sgst_amt + $roundedsgstVal;
                                $total_cgst_amt = $total_cgst_amt + $roundedcgstVal;
                                $all_total_val = $all_total_val + $roundeditemValue;
                                
                                
                                
                                $voucher2 = $salesdata->addChild("CREDITNOTEITEM");
                                $voucher2->addChild("PRODUCTID", $invitemobj->product_id);
                                $voucher2->addChild("BATCHCODE", $invitemobj->batchcode);
                                $voucher2->addChild("QUANTITY", sprintf("%.4f",$roundedQty));
                                $voucher2->addChild("ACTUALRATE", $roundedRate);
                                $voucher2->addChild("MRP", $roundedItemRate);
                                $voucher2->addChild("TAXABLEVALUE", $roundedTaxableValue);
                                if ($crdetails->state == "22") {
                                    $voucher2->addChild("CGSTPERCENTAGE", $invitemobj->cgstpct);
                                    $voucher2->addChild("CGSTAMOUNT", $roundedcgstVal);
                                    $voucher2->addChild("SGSTPERCENTAGE", $invitemobj->sgstpct);
                                    $voucher2->addChild("SGSTAMOUNT", $roundedsgstVal);
                                } else {
                                    $voucher2->addChild("IGSTPERCENTAGE", $invitemobj->igstpct);
                                    $voucher2->addChild("IGSTAMOUNT", $roundedigstRate);
                                }
                                $voucher2->addChild("TOTAL", sprintf("%.2f",$invitemobj->total));
                                $newDate1 = date("d-m-Y h:i:s", strtotime($invitemobj->createtime));
                                
                            }
                            
                            

                            $voucher->addChild("ID", $obj->id);
                            $voucher->addChild("CREDITNOTENUMBER", $obj->cnno);
                            $voucher->addChild("CRID", $obj->crid);
                            $voucher->addChild("CRCODE", strtoupper($crdetails->dispname));
                            $date = date('d-m-Y', strtotime($obj->cndate));
                            $voucher->addChild("CREDITNOTEDATE", $date);
                            $voucher->addChild("CUSTOMERID", $obj->customerid);
                            $voucher->addChild("CUSTOMERNAME", $obj->cname);
                            $voucher->addChild("CUSTOMERPHONE", $obj->cphone);
                            $voucher->addChild("TOTALVALUE", sprintf("%.2f", $all_total_val));
                            $roundTotalPOVal = round($all_total_val);
                            $roundoff = $roundTotalPOVal - $all_total_val;
                            $voucher->addChild("ROUNDOFF", sprintf("%.2f", $roundoff));
                            $voucher->addChild("GRANDTOTALVALUE", $roundTotalPOVal);
                            
                            
                            foreach ($invoiceitems_obj as $objs) {
                                $newDate1 = date("Y-m-dh:i:s", strtotime($objs->createtime));
                            }
                            $voucher->addChild("CREATETIME", $newDate1);
                            //print_r($voucher);
                            //return;
                            foreach ($invoiceitems_obj as $obj) {
                                
                                //$voucher2->addChild("CREATETIME", $newDate1);
                            }
                        }
                    }

//                    return;
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
                file_put_contents('./log/creditNoteVoucherlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
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




