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

                $customer_obj = $dbl->getSupplierMastersByDate($datetime);
                if ($customer_obj) { 
                    $envelope = new SimpleXMLElement('<ENVELOPE/>');
                    $name = "SupplierMaster" . $datetime . ".xml";
                    $header = $envelope->addChild("HEADER");
                    $header->addChild("TALLYREQUEST", "Import Data");
                    $body = $envelope->addChild("BODY");
                    $importdata = $body->addChild("IMPORTDATA");
                    $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                    $reqdesc->addChild("REPORTNAME", "Supplier Masters");
                    $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                    $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                    $reqdata = $importdata->addChild("REQUESTDATA");
                    foreach ($customer_obj as $obj) {
                        if (isset($obj) && !empty($obj) && $obj != null) {
                            $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                            $voucher = $tallymsg->addChild("SUPPLIER");
                            $voucher->addChild("ID", $obj->id);
                            $voucher->addChild("SUPPLIERCODE", $obj->supplier_code);
                            $voucher->addChild("DATEOFENTRY", $obj->date_of_entry);
                            $voucher->addChild("KYCNUMBER", $obj->kyc_number);
                            $companyname = preg_replace('/[^A-Za-z0-9\-]/', ' ', $obj->company_name);
                            $voucher->addChild("COMPANYNAME", $companyname);
                            $bankname = $obj->bank_name;
                            $bankname = preg_replace('/[^A-Za-z0-9\-]/', ' ', $bankname);
                            $voucher->addChild("BANKNAME", $bankname);
                            $voucher->addChild("BANKACCOUNTNUMBER", $obj->bank_ac_no);
                            $voucher->addChild("BANKBRANCH", $obj->bank_branch);
                            $cleanString = str_replace('<br/>', ' ',$obj->address);
                            $cleanString = str_replace('<br>', ' ', $cleanString);
                            $cleanString = preg_replace('/[^A-Za-z0-9\-]/', ' ', $cleanString);
                            $split = explode(' ', $cleanString); // Split up the whole string
                            $chunks = array_chunk($split, 8); // Make groups of 4 words
                            $address = array_map(function($chunk) {
                                return implode(' ', $chunk);
                            }, $chunks); // Put each group back together
                            $addCount = 1;
                            $length = count($address);
                            for ($i = 0; $i < $length; $i++) {
                                $voucher->addChild("ADDRESS" . $addCount, $address[$i]);
                                $addCount++;
                            }
                            
                            for($i = 9; $i > $addCount; $i-- ){
                                if($addCount > 5){
                                break;
                                } else {
                                $voucher->addChild("ADDRESS" . $addCount, "");
                                $addCount++;    
                                }
                            }
                            $voucher->addChild("COUNTRY", "India");
                            $voucher->addChild("STATE", $obj->sstate);
                            $voucher->addChild("DISTRICT", $obj->district);
                            $voucher->addChild("PINCODE", $obj->pincode);
                            $voucher->addChild("PANNUMBER", $obj->pan_no);
                            $voucher->addChild("CINNUMBER", $obj->cin_no);
                            $voucher->addChild("GSTIN", $obj->gst_no);
                            $voucher->addChild("CONTACTPERSON", $obj->contact_person1);
                            $voucher->addChild("PHONENUMBER", $obj->phone1);
                            $voucher->addChild("EMAIL", $obj->email1);
                            $createDate = date('Y-m-dh:i:s', strtotime($obj->createtime));
                            $voucher->addChild("CREATEDATETIME", $createDate);
//                            $voucher->addChild("CREATEDATETIME", $obj->createtime);
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
                file_put_contents('./log/suppliermasterlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
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




