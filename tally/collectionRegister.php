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

            
                $prods_obj = $dbl->getCollectionRegisterByDate($datetime);
//                print_r($prods_obj);
//                return;
                if ($prods_obj) {
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "COLLECTIONREGISTER_" . $datetime . ".xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "Collection Register");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    //echo "gererrere";
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($prods_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $voucher = $tallymsg->addChild("COLLECTIONREGISTER");
                            //$CRObj = $dbl->getCRInfoById($obj->crid);
                            //$CRCODE=strtoupper($CRObj->crcode);
                            //temporary
                            $voucher->addChild("CRCODE",htmlspecialchars($obj->crcode));
                            $invNumArr = explode("-", $obj->invoice_no);
                            $invRef = "";
                            $invNum = "";
                            if (sizeof($invNumArr) == 2) {
                                $invRef = $invNumArr[0];
                                $invNum = $invNumArr[1];
                            }
                            $voucher->addChild("INVOICENO",htmlspecialchars($obj->invoice_no));
                            $voucher->addChild("SALESINVOICENUMBER",htmlspecialchars($invNum));
                            $invoiceDate = date("d-m-Y", strtotime($obj->saledate));
                            $cust_name = "";
                            if($obj->sale_reg_type == 1){
                                $cust_obj = $dbl->getCustomerById($obj->customer_id);
                                if($cust_obj){
                                    $cust_name = $cust_obj->name;
                                }
                                
                            } else {
                                $cust_obj = $dbl->getDefaultCustByCRid($obj->crid);
                                if($cust_obj){
                                    $cust_name = $cust_obj->cust_name;
                                }
                            }                           
                            $voucher->addChild("CUSTOMERNAME",htmlspecialchars($cust_name));
                            $voucher->addChild("INVOICEDATE", htmlspecialchars($invoiceDate));
                            $TOTALAMOUNT= $obj->total_amount;
                            $roundTotalPOVal= round($TOTALAMOUNT);
                            $voucher->addChild("TOTALAMOUNT", htmlspecialchars($roundTotalPOVal));
                          
                            $paymentMode=strtoupper($obj->chargetypedesc);
                            $pmode=str_replace('CHARGES',' ',$paymentMode);
                            $voucher->addChild("PAYMENTMODE", htmlspecialchars($pmode));
                            $createDate = date('Y-m-dh:i:s', strtotime($obj->createtime));
                            $voucher->addChild("CREATEDATETIME", $createDate);                            
//                            $voucher->addChild("CREATEDATETIME", $obj->createtime);                            
                            if($obj->isregister == 0){
                                $obj->isregister = 2;
                            }
                            $voucher->addChild("CUSTOMERTYPE",$obj->isregister);
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
                    file_put_contents('./log/collectionregisterlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
                } else {
                    echo "No Record found for given date range";
                }
                //print_r($customer_obj);
//            } else {
//                echo '<span style="color:red;text-align:center;">Incorrect Username or Password </span>';
//            }
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




