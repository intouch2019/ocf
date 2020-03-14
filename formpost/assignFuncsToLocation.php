<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'lib/user/clsUser.php';

$errors = array();
$user = getCurrStore();
$userid = getCurrStoreId();
$db = new DBConn();
//$dbl = new DBLogic();
$userpage = new clsUser();
$pagecode = $db->safe($_SESSION['pagecode']);
//print_r($_SESSION);
$query = "select * from it_functionality_pages where pagecode = $pagecode";
//print "<br>$query<br>";
$page = $db->fetchObject($query);

//print "<br> OBJ: <br>";
//print_r($page);
$errors = array();
$cnt = 0;
$success = "";
if($page){
    $allowed = $userpage->isAuthorized($user->id, $page->pagecode);
   // print "<br> ALLOWED: ".$allowed;
    if (!$allowed) { header("Location: ".DEF_SITEURL."unauthorized"); return; }
}
else{ header("Location:".DEF_SITEURL."nopagefound"); return; }




extract($_GET);
$_SESSION['form_id'] = $form_id;
//print_r($_GET);

//print "<br><br>";

//print_r($_SESSION);

$allEnabledPgs = explode(",",$to_enable_pgs);
$allDisabledPgs = explode(",",$to_disable_pgs);
// print_r($allpgs);
// print "<br/>USER:".$userid."<br/>";
try{   
    //for to enable pages
    foreach($allEnabledPgs as $pg){
        if(trim($pg)!=""){
            $query = "select id,is_active from it_location_functionalities where location_id = $location_id and functionality_id = $pg ";
            $pobj = $db->fetchObject($query);
            if(isset($pobj) && !empty($pobj) && $pobj != null){
                if(trim($pobj->is_active)==0){
                  //activate it
                  $qry = "update it_location_functionalities set is_active = 1 , updatedby = $user->id , updatedat_location_id = $user->location_id  where id = $pobj->id "  ;
                  $db->execUpdate($qry);
                  $cnt++;
                }
            }else{
                //insert
                $iq = "insert into it_location_functionalities set location_id = $location_id , functionality_id = $pg , createtime = now() , createdby = $user->id , createdat_location_id = $user->location_id  ";
                $insert_id = $db->execInsert($iq);
                if($insert_id){
                    $cnt++;
                }
            }                        
       }
    }
    
    //for to disable pages
    foreach($allDisabledPgs as $pg){
        if(trim($pg)!=""){
            $query = "select id,is_active from it_location_functionalities where location_id = $location_id and functionality_id = $pg ";
            $pobj = $db->fetchObject($query);
            if(isset($pobj) && !empty($pobj) && $pobj != null){
                if(trim($pobj->is_active)==1){
                  //de-activate it
                  $qry = "update it_location_functionalities set is_active = 0 , updatedby = $user->id , updatedat_location_id = $user->location_id where id = $pobj->id "  ;
                  $db->execUpdate($qry);
                  $cnt++;
                }
            }                      
       }
    }
}catch(Exception $xcp){
   $errors['xcp'] = $xcp->getMessage();
}
if($cnt > 0){
  $success = "Functionalities(s) assigned successfully ";
}else{
  $errors['pg'] =  "Error during assigning functionalities. Contact Intouch";
}  
if (count($errors)>0) {
        unset($_SESSION['form_success']);       
        $_SESSION['form_errors'] = $errors;
  } else {
        unset($_SESSION['form_errors']);
        $_SESSION['form_success'] = $success;        
  }
  
  header("Location: ".DEF_SITEURL."func/location");
  exit;



