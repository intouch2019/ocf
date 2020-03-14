<?php
require_once("../../it_config.php");
//require_once("/var/www/html/sarotam/it_config.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';
require_once "lib/email/EmailHelper.php";


$error = array();
$db = new DBConn();
$dbl = new DBLogic(); 

try {
  
   $crquery = "select * from it_rfc_master where is_auto_price_carryover_set = 1";
    $crqryObj = $db->fetchObjectArray($crquery);
    if(isset($crqryObj)){
        
        $crList = "";
        for ($x = 0; $x < count($crqryObj); $x++) {
            $crObj = $crqryObj[$x];
            $crList .= $x+1 .". $crObj->dispname <br>";
        }
    foreach ($crqryObj as $crobj)
        {
 
            $crID=$crobj->id;
        
            $query = "select max(applicable_date)as applicable_date,product_id from it_product_price  where crid=$crID and is_approved=1 group by product_id"; 
            print_r($query);
            $qryObj = $db->fetchObjectArray($query);
            $newDate=date("Y-m-d H:i", time());
            $yesterday=date('jS F Y',strtotime("-1 days"));
            $today=date('jS F Y');
            print_r($newDate);
            $rowUpdated = 0;

    if(isset($qryObj)){
        foreach ($qryObj as $obj){
            
            
            $applicableDate = $obj->applicable_date;
            $prodid = $obj->product_id;
            
           $prodDetailsQry="select price,lastprice,createdby,crid from it_product_price where applicable_date='".$applicableDate."' and product_id=$prodid and crid=$crID";
           
           $qryObj1 = $db->fetchObjectArray($prodDetailsQry); 
           foreach ($qryObj1 as $obj2){
            $price=$obj2->price;
            $userid=$obj2->createdby;
            $cr=$obj2->crid;
            $last_price=$obj2->lastprice;
            $uploaddate = $newDate;
            
              
           $id=$dbl->uploadYesterdaysPrices($prodid,$price,$userid,$cr,$last_price,$uploaddate);
            $rowUpdated++;
            }
        }
    }
        }
        echo "Total Rows Updated ". $rowUpdated;
       $objDirector = $dbl->getUserInfoByType(UserType::Director);

   if ($rowUpdated > 0) {

        if (isset($objDirector->email) && trim($objDirector->email) != "") {
            $arr_to = explode(",", $objDirector->email);


           foreach ($arr_to as $to) {

               $subject = "Price Carryover ";
               $body = '<p>The price on ' . $yesterday . ', is being carried over to ' . $today . ', at Consignment Retail Outlets: <br>'. $crList .'</p>
                    
                                <p>Thanks & Regards,</p>
                                <p>Sarotam</p>
                                <p><b>Note : This is computer generated email do not reply.  </b></p>';


                $emailHelper = new EmailHelper();
                $success = $emailHelper->send(array($to), $subject, $body);

           }
     }
        
    }
    $objHO = $dbl->getUserInfoByType(UserType::HO);
    if ($rowUpdated > 0) {
     
     if (isset($objHO->email) && trim($objHO->email) != "") {
          $arr_to = explode(",", $objHO->email);

         foreach ($arr_to as $to) {

               $subject = "Price Carryover "; 
               $body = '<p>The price on ' . $yesterday . ', is being carried over to ' . $today . ', at Consignment Retail Outlets: <br>'. $crList .'</p>
                    
                              <p>Thanks & Regards,</p>
                               <p>Sarotam</p>
                              <p><b>Note : This is computer generated email do not reply.  </b></p>';

              $emailHelper = new EmailHelper();
               $success = $emailHelper->send(array($to), $subject, $body);

           }
     }
       
       
    }
    
    }
    
} catch (Exception $ex) {
    
}


