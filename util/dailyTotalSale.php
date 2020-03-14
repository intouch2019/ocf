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
    $crquery = "select id from it_rfc_master where inactive = 0";
    $crqryObj = $db->fetchObjectArray($crquery);
    $count = 0;
    $crList = "";
    if (function_exists('date_default_timezone_set')) {
        date_default_timezone_set("Asia/Kolkata");
    }
    $t_LastHour=0;
    $t_roundQty=0;
    $t_FirstDay=0;
    
    foreach ($crqryObj as $crobj) {
        $crid = $crobj->id;
        $today = date("Y-m-d");
        $startDate = $today . " 00:00:00";

        $firstDate = date('Y-m-d', strtotime(date('Y-m-1')));
       
        $today1 = date('Y-m-d H:i:s');
        $mintime = date('Y-m-d H:i:s', time() - 3600);

        $cquerry1 = " select  r.dispname, sum(c.total_qty) as tq from it_cr270001 c, it_rfc_master r where c.crid = r.id and c.status = 1 and c.saledate ='$today'  and c.crid = $crid";
        $crqryObj1 = $db->fetchObject($cquerry1);
        $roundQty = round($crqryObj1->tq, 4, PHP_ROUND_HALF_UP);
        $roundQty = sprintf("%.4f", $roundQty);
       
        $cquerry2 = " select  r.dispname, sum(c.total_qty) as tq from it_cr270001 c, it_rfc_master r where c.crid = r.id and c.status = 1 and c.crid = $crid and c.saledatetime is not NULL and c.saledatetime > '$mintime' and c.saledatetime < '$today1' ";
        $crqryObj2 = $db->fetchObject($cquerry2);
        
        $cquerry3 = " select  r.dispname, sum(c.total_qty) as tq from it_cr270001 c, it_rfc_master r where c.crid = r.id and c.status = 1 and c.crid = $crid and c.saledate >= '$firstDate' and c.saledate <='$today' ";
        $crqryObj3 = $db->fetchObject($cquerry3);

        $Name = $crqryObj1->dispname;

        $LastHourQty = round($crqryObj2->tq, 4, PHP_ROUND_HALF_UP);
        $LastHour = sprintf("%.4f", $LastHourQty);

        $FirstDayQty = round($crqryObj3->tq, 4, PHP_ROUND_HALF_UP);
        $FirstDay = sprintf("%.4f", $FirstDayQty);

        $count = $count + 1 ;
        
        $t_LastHour += $LastHour;
        $t_roundQty += $roundQty;
        $t_FirstDay += $FirstDay;
        
        $t_roundQty = sprintf("%.4f", $t_roundQty);
        $t_FirstDay = sprintf("%.4f", $t_FirstDay);       
        $t_LastHour = sprintf("%.4f", $t_LastHour);
     
        $crList .= "<tr>
    <td>$count</td>
    <td>$Name</td>
    <td>$LastHour</td>
    <td>$roundQty</td>
    <td>$FirstDay</td>
    </tr>";
        }  
        $crList .= "<tr><td><b>TOTAL</b></td><td></td><td>$t_LastHour</td><td>$t_roundQty</td ><td>$t_FirstDay</td></tr>";
        if ($crList != "") {

            $to= array("sunny.pawar@sarotam.com","yogessh.kumar@sarotam.com","rajesh.gupta@sarotam.com","rajeevranjan.prasad@sarotam.com","chetan.tolia@sarotam.com");

            $today = date('jS F Y');
            $time = date("H:i");
      
                $subject = "Daily Sale - $today $time";
                $body = '<p> Total sale quantity for each Consignment Retailer is as fallows : <br></p>
                    <p> 
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<table border="1" cellspacing="0" bstyle="width:100%">
  <tr>
    <th>Sr.No.</th>
    <th>Retail outlet</th>
    <th>Sale in last hour</th> 
    <th>Cumulative sales for Day</th>
     <th>Cumulative sales for Month</th>
  </tr>
  
' . $crList . '
</table>
</body>
</html>
</p>
                                <p>Thanks & Regards,</p>
                                <p>Sarotam</p>
                                <p><b>Note : This is computer generated email do not reply.  </b></p>';
                
                  $emailHelper = new EmailHelper();
                  $success = $emailHelper->send($to, $subject, $body);
    }
} catch (Exception $ex) {
    
}
