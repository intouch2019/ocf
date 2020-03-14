<?php

require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

class clsUser extends dbobject {
    
        public function isAuthentic($username, $password) {
		$username = $this->safe($username);
		$password = $this->safe(md5($password));
		$query = "select * from it_users where username=$username and password=$password";
                return $this->fetchObject($query);
	}
        
        public function isAuthorized($user_id, $pagecode) {
            $page = $this->fetchObject("select * from it_functionality_pages where pagecode = '$pagecode'");
            $query ="select * from it_user_location_functionalities up , it_location_functionalities lp where up.location_functionality_id = lp.id and up.user_id = $user_id and lp.functionality_id = $page->id ";
//            print "<br>".$query;
            $allowed = $this->fetchObject($query);
            if(!$allowed){ return false; }else{ return true; }
        }
        
        public function pageExists($pageuri){
            $pageuridb = $this->safe($pageuri);
            $obj = $this->fetchObject("select * from it_functionality_pages where pageuri = $pageuridb");
            $qry = "select * from it_functionality_pages where pageuri = $pageuridb ";         
//            print "<br>".$qry;
            if($obj){ return $obj; }  
        }
    

         public function getUserByname($name,$id=FALSE) {		
                $name_trim = str_replace(" ","",$name);
                $name_db = $this->safe($name_trim);
                $clause = "replace(name,' ','')=$name_db";
                $idcls = "";
                if($id != FALSE){
                     $idcls = "and id != $id";
                }
		$query = "select id,name from it_users where $clause $idcls";
                //echo $query;
		$user = $this->fetchObject($query);
		return $user;
	}
        
        public function getUserByUsername($username,$id=FALSE) {		
                $username_trim = str_replace(" ","",$username);
                $username_db = $this->safe($username_trim);
                $clause = "replace(username,' ','')=$username_db";
                $idcls = "";
                if($id != FALSE){
                     $idcls = "and id != $id";
                }
		$query = "select id,name from it_users where $clause $idcls";
                //echo $query;
		$user = $this->fetchObject($query);
		return $user;
	}
        
        public function getUserByPhoneno($phone,$id) {		
                $phone_db = $this->safe(trim($phone));
               	$query = "select id from it_users where phone = $phone_db and id != $id";
                //echo $query;
		$user = $this->fetchObject($query);
		return $user;
	}
        
        public function addUser($name,$address,$phone,$username,$password,$userid,$createdat_location_id){
            $name_db = $this->safe($name);
            $address_db = $this->safe($address);     
            $username_db = $this->safe($username);
            $pwd_db = $this->safe(md5($password));
            $qry = "insert into it_users set name=$name_db,address=$address_db,phone=$phone,username=$username_db,password=$pwd_db,is_active=1,createdby=$userid,createtime=now(),createdat_location_id = $createdat_location_id ";
            //echo $qry;
            $id = $this->execInsert($qry);
            return $id;
        }
        
        public function getAllActiveUsers() {		                
		$query = "select id,name from it_users where is_active = 1";
                //echo $query;
		$objs = $this->fetchAllObjects($query);
		return $objs;
	}
        
        public function updateUser($uid,$name,$address,$phone,$username,$password,$isactive,$userid,$updatedat_location_id){
            $name_db = $this->safe($name);
            $address_db = $this->safe($address);     
            $username_db = $this->safe($username);
            $pwdcls="";
            if($password !=""){
                  $pwd_db = $this->safe(md5($password));
                  $pwdcls = ",password=$pwd_db";
            }
          
            $qry = "update it_users set name=$name_db,address=$address_db,phone=$phone,username=$username_db,is_active=$isactive,updatedby=$userid,createtime=now(), updatedat_location_id = $updatedat_location_id $pwdcls where id= $uid";
//            echo $qry;
            $id = $this->execUpdate($qry);
            return $id;
        }
        
}

?>
