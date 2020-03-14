<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$error = array();
extract($_POST);
//print_r($_POST);
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
$dbl = new DBLogic();
try{

    $utypesel = isset($utypesel) && trim($utypesel) != "" ? intval($utypesel) : false;
    if(!$utypesel){ $error['missing_utype'] = "Select User Type"; }

    $uname = isset($uname) && trim($uname) != "" ? trim($uname) : false;
    if(!$uname){ $error['missing_uname'] = "Enter Name"; }

    $username = isset($username) && trim($username) != "" ? trim($username) : false;
    if(!$username){ $error['missing_username'] = "Enter Username"; }

    $email = isset($email) && trim($email) != "" ? trim($email) : false;
    if(!$email){ $error['missing_emailid'] = "Enter Email Id"; }

    $password = isset($password) && trim($password) != "" ? trim($password) : false;
    if(!$password){ $error['missing_password'] = "Enter Password"; }
    
    $confirmpassword = isset($confirmpassword) && trim($confirmpassword) != "" ? trim($confirmpassword) : false;
    if(!$confirmpassword){ $error['missing_confirmpassword'] = "Enter Confirm Password"; }
    
    if(isset($password) && isset($confirmpassword)){
        if($password != $confirmpassword){
            if(!$confirmpassword){ $error['missing_confirmpassword'] = "Password and Confirm Password must be same"; }
        }
    }
    
    $phoneno = isset($phoneno) && trim($phoneno) != "" ? trim($phoneno) : false;
    if(!$phoneno){ $error['missing_phoneno'] = "Enter Phone No"; }
    
    
    if(count($error) == 0){
       $user_id = $dbl->insertUser($utypesel,$uname,$username,$email,$password,$phoneno,$userid);
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
//header("Location: " . DEF_SITEURL . $redirect);
exit;
