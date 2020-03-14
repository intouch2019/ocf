<?php

require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once ("lib/db/DBLogic.php");
require_once 'session_check.php';
require_once 'lib/user/clsUser.php';

$error = array();
extract($_POST);
//print_r($_POST);
$form_id = 'createUserErrors';
$_SESSION['form_id'] = $form_id;

$crid = "";
if(isset($_POST['crsel'])){
    $crid = $_POST['crsel'];
}

if(isset($_POST['utypesel'])){
    $usertype = $_POST['utypesel'];
}else{
    $error['missing_name_addr'] = "Select User Type Correctly";
}

$name = $_POST['name'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phoneno'];
$password = $_POST['password'];
$confirmpassword = $_POST['confirmpassword'];

$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userId = getCurrStoreId();
$dbl = new DBLogic();
$last_inserted_id = "";

try {
    if (!is_numeric($phone)) {
        $error['missing_phone'] = "Phone no should be numeric";
    }else {

        if (trim($phone) != "") {
            $len = strlen($phone);
            if ($len > 10) {
                $error['missing_phone'] = "Phone no cannot be more than 10 digits.";
            }
        }
    }
    if($password != $confirmpassword){
        $error['password_not_match'] = "Password not matched.";
    }


        if (count($error) == 0) {


            $obj1 = $dbl->getUserByUsername($username);
            $obj2 = $dbl->getUserByName($name);
//            print_r($userId);
            if (isset($obj1) && !empty($obj1)) {
                $error['username_already_exist'] = "$username already exist. Try to create new user";
            }else if (isset($obj2) && !empty($obj2)) {
                $error['name_already_exist'] = "$name already exist. Try to create new user";
            } else {
                $datetime = date('Y-m-d H:i:s');
                $hashValue = md5($username.$datetime);
                $password = md5($password);
                $last_inserted_id = $dbl->addUser($userId, $username, $password, $name, $email, $phone, $usertype, $crid, $hashValue);
                if (trim($last_inserted_id) <= 0) {
                    $error['insert_fail'] = "New user is not created. Try to create it again.";
                } else {
                    $success = "User Created Successfully.";
                }
            }
        }
    
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);
    $_SESSION['form_errors'] = $error;
    $redirect = 'user/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = 'users';
    $redirect = "users";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
