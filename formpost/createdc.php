<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';

$error = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$dbl = new DBLogic();
try {

    $name = isset($name) && trim($name) != "" ? $name : false;
    if (!$name) {
        $error['missing_name'] = "Enter DC Name";
    }

    $contact_person = isset($contact_person) && trim($contact_person) != "" ? $contact_person : false;
    if (!$contact_person) {
        $error['missing_contact_person'] = "Enter Contact Person Name";
    }

    $address = isset($address) && trim($address) != "" ? $address : false;
    if (!$address) {
        $error['missing_address'] = "Enter Address";
    }

    $statesel = isset($statesel) && trim($statesel) != "" ? $statesel : false;
    if (!$statesel) {
        $error['missing_state'] = "Select State";
    }

    $phone = isset($phone) && trim($phone) != "" ? $phone : false;
    if (!$phone) {
        $error['missing_phone'] = "Enter Phone no";
    }

    $email = isset($email) && trim($email) != "" ? $email : false;
    if (!$email) {
        $error['missing_email'] = "Enter Email";
    }

    $gstno = isset($gstno) && trim($gstno) != "" ? $gstno : false;
    if (!$email) {
        $error['missing_gstno'] = "Enter GST No";
    }

    $panno = isset($panno) && trim($panno) != "" ? $panno : false;

    $target_dir = "../images/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $baseName = basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    
    
    // Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

    if (count($error) == 0) {
        $dc_id = $dbl->insertDC($name, $contact_person, $address, $statesel, $phone, $email, $gstno, $panno, $userid,$baseName);
    }
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'dc/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "distribution/center";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
