<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
//print_r($user);
$username = $user->name;
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
$cr_id = 0;
try {
    $dispname = isset($dispname) && trim($dispname) != "" ? $dispname : false;
    if (!$dispname) {
        $error['missing_dispname'] = "Enter  CR Name ";
    }

    $rfcname = isset($rfcname) && trim($rfcname) != "" ? $rfcname : false;
    if (!$rfcname) {
        $error['missing_rfcname'] = "Enter RFC Name";
    }

    $cntper = isset($cntper) && trim($cntper) != "" ? $cntper : false;
    if (!$cntper) {
        $error['missing_cntper'] = "Enter Contact Person Name";
    }

    $address = isset($address) && trim($address) != "" ? $address : false;
    if (!$address) {
        $error['missing_address'] = "Enter Address";
    }

    $email = isset($email) && trim($email) != "" ? $email : false;
    if (!$email) {
        $error['missing_email'] = "Enter Email ID";
    }

    $phone = isset($phone) && trim($phone) != "" ? $phone : false;
    if (!$phone) {
        $error['missing_ph'] = "Enter Phone Number";
    }

    $gstno = isset($gstno) && trim($gstno) != "" ? $gstno : false;
    if (!$gstno) {
        $error['missing_gst'] = "Enter GST Number";
    }

    $panno = isset($panno) && trim($panno) != "" ? $panno : false;
    if (!$panno) {
        $error['missing_panno'] = "Enter PAN number";
    }

    $custname = isset($custname) && trim($custname) != "" ? $custname : false;
    if (!$custname) {
        $error['missing_custname'] = "Enter Customer Name";
    }

    $custphone = isset($custphone) && trim($custphone) != "" ? $custphone : false;
    if (!$custphone) {
        $error['missing_custphone'] = "Enter Customer Phone";
    }

    $state = isset($state) && trim($state) != "" ? $state : false;
    if (!$state) {
        $error['missing_state'] = "Select State";
    }

    $set = isset($set) && trim($set) != "" ? $set : false;

    $address_string = str_replace("\r\n", "<br/>", $address);
    $address_string =  htmlspecialchars_decode($address_string);//htmlspecialchars
    
    if (count($error) == 0) {
       $cr_id = $dbl->insertCR($dispname,$rfcname,$cntper,$address_string,$email,$phone,$gstno,$panno,$custname,$custphone,$state,$set,$userid);                     
    }
    if ($cr_id > 0) {
        //send approval mail
        $objPO = $dbl->getUserInfoByType(UserType::HO);
        if (isset($objPO->email) && trim($objPO->email) != "") {
            $arr_to = explode(",", $objPO->email);
            foreach ($arr_to as $to) {
                $subject = "Approve CR ";
                $body = '<p> Mentioned CR '. $dispname.'_'.$rfcname . ', is being created by ' . $username . ' Please Approve</p>
                    
                        <p>Thanks & Regards,</p>
                        <p>Sarotam</p>
                        <p><b>Note : This is computer generated email do not reply.  </b></p>';

                $emailHelper = new EmailHelper();
                $success = $emailHelper->send(array($to), $subject, $body);
            }
        }
        $success = "CR created Successfully";
    } else {
        $error['Fail'] = "Failed to create CR";
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = "cr/create";
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success; 
    $redirect = "rfc";
}

session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
