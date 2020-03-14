<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
//error_log("\nHere in delivery sync\n",3,"../ajax/tmp.txt");
extract($_GET);
$params=$_GET['params'];
try{
   $db = new DBConn();
   if(trim($params)!=""){
       $records = explode(",",$params);       
       foreach($records as $record){                      
        if(trim($record)=="") {continue;}
        $arr = explode(":", $record);       
        if(trim($arr[0])=='id'){$id=$arr[1];}
        if(trim($arr[0])=='status'){$status=$db->safe(trim($arr[1]));}
        if(trim($arr[0])=='delivery_date'){$delivery_date=$db->safe(trim($arr[1]));}         
       }
        $query="update it_sms set status=$status,delivery_date=$delivery_date where id = $id";
//        print "<br/>$query";
//        error_log("\nSMS QUERY: $query\n",3,"../ajax/tmp.txt");
         $db->execUpdate($query);
       
   }

}catch(Exception $xcp){
   print $xcp->getMessage(); 
}

