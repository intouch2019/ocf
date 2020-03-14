<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$error = array();
$dbl = new DBLogic();
extract($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$success = "";
$user = getCurrStore();
$db = new DBConn();
$dbl = new DBLogic();


try{
    if(trim($amount) == "" || trim($receiptno) == "" || trim($description) == ""){
        $error['missing_parameters'] = "Please Enter All Required Fields";
    }
    
    if(count($error) == 0){
        $userid = $user->id;
        $obj = $dbl->checkOpenSaleStatus($userid);
        if(isset($obj)){
            if($obj->id == 0){
                $error['sale_closed'] = "Please Open Day In Sale To Perform Action";
            }else{
                $coll_reg_id = $obj->id;
                $crdetails = $dbl->getCRDetailsByUserId($userid);
                $crid = $crdetails->id;              
                $insertId = $dbl->insertDepositDiary($userid, $crid, $coll_reg_id, $amount, $receiptno, $description, $paymentType);
                if($insertId != 0){
                    $updateId = $dbl->updateDepositDetailsIntoCollReg($coll_reg_id, $amount);
                    if($updateId != -1){
                        $success = "Information Updated Successfully";
                    }else{
                    $error['update_error'] = "Unable to update data in collection register.";
                }
                    
                }else{
                    $error['deposite_error'] = "Unable to insert data in deposit diary.";
                }
//                  print_r($insertId);
                
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
    $redirect = 'deposit/details';
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'deposit/details';
}
session_write_close();
 header("Location: " . DEF_SITEURL . $redirect);
exit;