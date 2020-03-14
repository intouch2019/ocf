<?php
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once "lib/logger/clsLogger.php";
require_once "lib/core/Constants.php";
require_once ("session_check.php");

extract($_GET);
//print_r($_GET);
$user = getCurrStore();   
//print_r($user);
$user_id = $user->id;
$loginlocid = $user->location_id;
//print "<br>$user_id &&&&$loginlocid<br>";
$cnt = 1;
$products = array();
foreach ($_GET as $prodid => $status){   
    if($cnt > 3){ //as 1st param is distid n 2nd is table content count, so itms start frm 3rd      
     $products[$prodid]=$status;
    }
    $cnt++;
}
$errors=array();
$success=array();   
//print "<br>";
//print_r($products);
$msg="Items Status changed";
try{
    $db = new DBConn();
    $today = date('Y-m-d H:i:s');
    $today_db = $db->safe($today);
    if(! empty($products)){
        foreach($products as $key => $value){
            $selqry= "select id from it_location_products where product_id = $key and location_id = $locsel limit 1";
//            print"<br>SELECT:::::$selqry<br>";
            $pobj = $db->fetchObject($selqry);
            if(isset($pobj)){
                //update product mapping
                $updtqry= "update it_location_products set is_mapped = $value, updatedby = $user_id, updatedat_location_id = $loginlocid where id = $pobj->id";
                $db->execUpdate($updtqry);
//                print"<br>UPDATE:::::$updtqry<br>";
            }else{
                //insert product mapping
                $insrtqry = "insert into it_location_products set product_id = $key,location_id = $locsel,is_mapped = $value, createtime = $today_db, createdby = $user_id, createdat_location_id = $loginlocid";
                $db->execInsert($insrtqry);
//                print"<br>INSERT:::::$insrtqry<br>";
            }
        }
    }else{
        //products array empty
         $msg = "No Items mark. Please mark item status and then click save";
    }    
} catch (Exception $xcp) {
    echo json_encode(array("error"=>"1","message" => "problem in changing status"));
}
echo json_encode(array("error"=>"0","message" => $msg));