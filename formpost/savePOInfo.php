<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/db/DBLogic.php';

$errors = array();

extract($_POST);
$_SESSION['form_post'] = $_POST;
$_SESSION['form_id'] = $form_id;
//print_r($_POST);
//return;exit;
$user = getCurrStore();
$userid = getCurrStoreId();
$dblogic = new DBLogic();
$cnt=0;

foreach($prod as $prod_id => $arrs){
//  print "<br> PROD ID: ".$prod_id."<br>";
//  print_r($arrs);
//  print "<br>";
    $flg = 0;
  foreach($arrs as $child_location_id => $qty_in_packets){      
    if(trim($qty_in_packets)!=""){  
//      print "<br> KEY: $child_location_id => VAL: $qty_in_packets ";
      if(is_numeric($child_location_id)){
              //save PO Items
              //fetch 
             $flg = 1;
//             print "<br>IN IF FLG : $flg <br>";
              $obj = $dblogic->fetchPOItem($po_id,$parent_location_id,$child_location_id,$prod_id);
              if(isset($obj) && !empty($obj) && $obj != null){
                $dblogic->updatePurchaseOrderItems($obj->id,$qty_in_packets,$user->id,$user->location_id);
                $cnt++;  
              }else{              
                $dblogic->savePurchaseOrderItems($po_id,$parent_location_id,$child_location_id,$prod_id,$qty_in_packets,$user->id,$user->location_id);
                $cnt++;
              }
      }else{
          if(strcmp($child_location_id, "hd") == 0){
              $child_location_id_hd = -1;
              //fetch 
              $obj = $dblogic->fetchPOItem($po_id,$parent_location_id,$child_location_id_hd,$prod_id);
              if(isset($obj) && !empty($obj) && $obj != null){
                $dblogic->updatePurchaseOrderItems($obj->id,$qty_in_packets,$user->id,$user->location_id);
                $cnt++;  
              }else{  
                $dblogic->savePurchaseOrderItems($po_id,$parent_location_id,$child_location_id_hd,$prod_id,$qty_in_packets,$user->id,$user->location_id);
                $cnt++;
              }
          }if(strcmp($child_location_id, "buffer") == 0){
              $child_location_id_bf = -2;
              //fetch 
              $obj = $dblogic->fetchPOItem($po_id,$parent_location_id,$child_location_id_bf,$prod_id);
              if(isset($obj) && !empty($obj) && $obj != null){
                $dblogic->updatePurchaseOrderItems($obj->id,$qty_in_packets,$user->id,$user->location_id);
                $cnt++;  
              }else{  
                $dblogic->savePurchaseOrderItems($po_id,$parent_location_id,$child_location_id_bf,$prod_id,$qty_in_packets,$user->id,$user->location_id);
                $cnt++;
              }  
          }
      }
    }else{
        //when no value is entered for hd and buffer keep qty = 0
//        print "<br>IN ELSE FLG : $flg <br>";
        if($flg == 1){
            if(strcmp($child_location_id, "hd") == 0){
                  $child_location_id_hd = -1;
                  $qty_in_packets = 0;
                  //fetch 
                  $obj = $dblogic->fetchPOItem($po_id,$parent_location_id,$child_location_id_hd,$prod_id);
                  if(isset($obj) && !empty($obj) && $obj != null){
                    $dblogic->updatePurchaseOrderItems($obj->id,$qty_in_packets,$user->id,$user->location_id);
                    $cnt++;  
                  }else{  
                    $dblogic->savePurchaseOrderItems($po_id,$parent_location_id,$child_location_id_hd,$prod_id,$qty_in_packets,$user->id,$user->location_id);
                    $cnt++;
                  }
              }if(strcmp($child_location_id, "buffer") == 0){
                  $child_location_id_bf = -2;
                  $qty_in_packets = 0;
                  //fetch 
                  $obj = $dblogic->fetchPOItem($po_id,$parent_location_id,$child_location_id_bf,$prod_id);
                  if(isset($obj) && !empty($obj) && $obj != null){
                    $dblogic->updatePurchaseOrderItems($obj->id,$qty_in_packets,$user->id,$user->location_id);
                    $cnt++;  
                  }else{  
                    $dblogic->savePurchaseOrderItems($po_id,$parent_location_id,$child_location_id_bf,$prod_id,$qty_in_packets,$user->id,$user->location_id);
                    $cnt++;
                  }  
              } 
        }  
    }  
  }
  
}
if(trim($cnt)==0){
   $errors[] = "Please enter values" ;
}

if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    $redirect = "purchase/order/lid=".$lid."/dt=".$dtsel."/heid=".$parent_location_id;   
} else {
    //echo $tmpName;
    $success="Info Saved Successfully. Please select another hub or event to proceed further. Else click 'confirm' to complete the PO .";
   // unset($_SESSION['form_id']);
    unset($_SESSION['form_errors']);       
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    //$redirect = "purchase/order";    
    $redirect = "purchase/order/lid=".$lid."/dt=".$dtsel; 
}
//print_r($_SESSION);
session_write_close();
header("Location: ".DEF_SITEURL.$redirect);
exit;