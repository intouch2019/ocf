<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once ("lib/db/DBLogic.php");
require_once 'session_check.php';
require_once 'lib/user/clsUser.php';

$error = array();
$clsUser = new clsUser();

extract($_POST);
print_r($_POST);
$_SESSION['form_id'] = $form_id;
$name = $_POST['name'];
$prevName = $_POST['prevName'];
$uid = $_POST['u_id'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$username = $_POST['username'];
$prevUsername = $_POST['prevUsername'];
$password = $_POST['password'];
$inactive = trim($_POST['inactive']);
$_SESSION['form_post'] = $_POST;
//$success = "$name location is created successfully";
$success = "";
$user = getCurrStore();
$userId= $user->id;
$dbl = new DBLogic();

try{
    
    if($username != $prevUsername){
        
        $obj1 = $dbl->getUserByUsername($username);
        if (isset($obj1) && !empty($obj1)) {
                $error['username_already_exist'] = "$username already exist. Try to create new user";
        }
    }
    if($name != $prevName){
        $obj2 = $dbl->getUserByName($name);
            if (isset($obj2) && !empty($obj2)) {
                $error['name_already_exist'] = "$name already exist. Try to create new user";
        }
    }
    
    if(count($error) == 0) {
                $password = md5($password);
                $last_inserted_id = $dbl->updateUser($userId, $name, $email, $phone, $username, $password, $inactive, $uid);
                if (trim($last_inserted_id) <= 0) {
                    $error['insert_fail'] = "Error in update user information.";
                } else {
                    $success = "User Created Successfully.";
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
    $redirect = 'user/edit/userid='.$uid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'users';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;