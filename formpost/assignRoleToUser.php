<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/core/Constants.php";
require_once 'lib/user/clsUser.php';
require_once "lib/db/DBLogic.php";

$errors = array();
$user = getCurrStore();
$by_user = getCurrStoreId();
$userpage = new clsUser();
extract($_GET);
$_SESSION['form_id'] = $form_id;
$allEnabledRoles = explode(",",$to_enable_roles);
$allDisabledRoles = explode(",",$to_disable_roles);

$dbl = new DBLogic();
$errors = array();
$cnt = 0;
$success = "";
try{   
    
    
    //delete all previous roles by userid
    $delete = $dbl->deleteAllRolesByuserId($user_id);
    
    //for to enable pages
    foreach($allEnabledRoles as $role){
        if(trim($role)!=""){
            $delete = $dbl->assignRoleToUser($user_id,$role,$by_user);
            if(isset($delete) && ! empty($delete) && $delete != null){
                    $cnt++; 
            }                      
       }
    }
    
}catch(Exception $xcp){
   $errors['xcp'] = $xcp->getMessage();
}

if($cnt > 0){
  $success = "$cnt Role assigned successfully ";
}else{
  $errors['pg'] =  "No Role Assigned to Users";
}  
if (count($errors)>0) {
        unset($_SESSION['form_success']);       
        $_SESSION['form_errors'] = $errors;
  } else {
        unset($_SESSION['form_errors']);
        $_SESSION['form_success'] = $success;        
  }
  
  header("Location: ".DEF_SITEURL."assign/user/roles");
  exit;



