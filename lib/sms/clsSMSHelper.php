<?php
require_once "lib/db/DBConn.php";
require_once "lib/logger/clsLogger.php";

class clsSMSHelper {	                
        public function sendSMS($data_id,$type,$phoneno,$message){
            $db = new DBConn();
            $senderID = "WASSUP";
            //step 1 insert into sms table
            $query="insert into it_sms set data_id = $data_id,type=$type,send_sms_to = $phoneno,sms_from='".$senderID."',msg='$message',createtime=now()";            
            $stsms_id=$db->execInsert($query);
            // below is the sms delivery url
            $durl2 = "http://192.168.0.26/wassup/home/getSMS/?id=".$stsms_id."&status=%d&delivery_date=%t";
            $en_durl2 = urlencode($durl2);
            //params
            $fields2 = array(
                'username' => 'wassuphtp1',
                'password' => 'wassup4t',
                'to' => $phoneno,
                'udh' => '0',
                'from' => 'WASSUP',
                'text' => $message,
                'dlr-url' => $en_durl2
            );
            $fields_string="";
            //url-ify the data for the POST
            $params = array();
            foreach($fields2 as $key=>$value) { $params[] = $key.'='.$value; }
            $fields_string = implode('&', $params);

            $url = "http://www.myvaluefirst.com/smpp/sendsms?";        
            $url_db=$db->safe(trim($url.$fields_string));    
            //update the url created in db
            $db->execUpdate("update it_sms set url=$url_db where id = $stsms_id");
                        
            //open connection
            $ch = curl_init();
            $options = array (CURLOPT_RETURNTRANSFER => true);
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt_array ( $ch, $options );
            curl_setopt($ch,CURLOPT_POST, count($fields2));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

            //execute post
            $resp = curl_exec($ch);
            //close connection
            curl_close($ch);
            //update the resp
            $db->execUpdate("update it_sms set sms_sent_status = '$resp' where id = $stsms_id");
            $logger = new clsLogger();
            $logger->logInfo("sendSMS:$message:$resp");
//            return $resp;
        }
}

?>
