<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$error = array();
$clsBin = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;
//print_r($_POST);
$name = $_POST['name'];
$loctn_id = $_POST['locsel'];
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();

try{
    if(trim($name)=="" && trim($loctn_id)==""){
        $error['missing_name_location'] = "Enter bin name and  location";
    }else if(trim($name)==""){
        $error['missing_name'] = "Enter bin name";
    }else if(trim($loctn_id)==""){
        $error['missing_location'] = "Select location ";
    }else{  
        if(count($error) == 0){
            $obj = $clsBin->getBinByName($name,$loctn_id);
            if(isset($obj) && !empty($obj)){
                $error['already_exist'] = "'$name' already exist. Try to create new bin";
            }else{
                $last_inserted_id = $clsBin->insertBin($name,$loctn_id,$user->id,$user->location_id);
                if(trim($last_inserted_id)<=0){
                    $error['insert_fail'] = "New Bin is not created. Try to create it again.";
                }
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
    $redirect = 'bin/create';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'bins';
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;