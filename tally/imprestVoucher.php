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

            $imprest_obj = $dbl->getImprestByDate($datetime);
            if ($imprest_obj) {
                $envelope = new SimpleXMLElement('<ENVELOPE/>');
                $name = "ImprestVoucher_" . $datetime . ".xml";
                $header = $envelope->addChild("HEADER");
                $header->addChild("TALLYREQUEST", "Import Data");
                $body = $envelope->addChild("BODY");
                $importdata = $body->addChild("IMPORTDATA");
                $reqdesc = $importdata->addChild("REQUESTDESC"); //"REPORTNAME","Vouchers"
                $reqdesc->addChild("REPORTNAME", "Imprest Voucher");
                $staticvariable = $reqdesc->addChild("STATICVARIABLES");
                $staticvariable->addChild("SVCURRENTCOMPANY", "Sarotam 2018-19");
                $reqdata = $importdata->addChild("REQUESTDATA");
                foreach ($imprest_obj as $obj) {
                    if (isset($obj) && !empty($obj) && $obj != null) {
                        $tallymsg = $reqdata->addChild("TALLYMESSAGE");
                        $voucher = $tallymsg->addChild("IMPREST");
                        $voucher->addChild("ID", $obj->id);
                        $crdetails = $dbl->getCRInfoById($obj->crid);
                        $voucher->addChild("CRCODE", htmlspecialchars(strtoupper($crdetails->dispname)));
                        $voucher->addChild("AMOUNT", $obj->amount);
                        $voucher->addChild("VOUCHERNUMBER", htmlspecialchars($obj->voucher_no));
                        $date = date('d-m-Y', strtotime($obj->ctime));
                        $voucher->addChild("VOUCHERDATE", $date);
                        $voucher->addChild("DESCRIPTION", htmlspecialchars($obj->description));
                        $userInfo = $dbl->getUserInfoById($obj->by_user);
                        $voucher->addChild("SPENDBY", htmlspecialchars($userInfo->name));
                        $createDate = date('Y-m-dh:i:s', strtotime($obj->ctime));
                        $voucher->addChild("CREATEDATETIME", $createDate);
//                        $voucher->addChild("CREATEDATETIME", $obj->ctime);
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
                file_put_contents('./log/imprestvoucherlive_'.$local->format("Y.m.d_H:i:s.u").'.log', $log, FILE_APPEND);
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




