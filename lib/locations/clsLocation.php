<?php
require_once "lib/db/dbobject.php";
require_once "lib/logger/clsLogger.php";

class clsLocation extends dbobject {
    
    public function getLocationByname($name, $id=FALSE) {		
                $name_trim = str_replace(" ","",$name);
                $name_db = $this->safe($name_trim);
                $clause = "replace(name,' ','')=$name_db";
                $idcls = "";
                if($id != FALSE){
                     $idcls = "and id != $id";
                }
		$query = "select id,name from it_locations where $clause $idcls";
                //echo $query;
		$user = $this->fetchObject($query);
		return $user;
	}
        public function addLocation($name,$address,$city,$pincode,$userid,$createdat_location_id,$ltype,$lcode,$is_dependant){
            $name_db = $this->safe(trim($name));
            $address_db = $this->safe(trim($address));    
            $city_db = $this->safe(trim($city));
            $lcode_db = $this->safe(trim($lcode));
            $qry = "insert into it_locations set name=$name_db,address=$address_db,city=$city_db,pincode=$pincode,is_active=1,created_by=$userid,createtime=now(),createdat_location_id = $createdat_location_id , location_type_id = $ltype , location_code = $lcode_db ";
            if(isset($is_dependant) && trim($is_dependant)=="on"){
                $qry .= " , is_dependant = 1 ";
            }
            $id = $this->execInsert($qry);
            return $id;
        }
        public function updateLocation($lid,$name,$address,$city,$pincode,$userid,$actv,$updatedat_location_id,$lcode ){
            $name_db = $this->safe(trim($name));
            $address_db = $this->safe(trim($address));    
            $city_db = $this->safe(trim($city));             
            $lcode_db = $this->safe(trim($lcode));             
            $qry = "update it_locations set name = $name_db, address = $address_db, city = $city_db, pincode = $pincode, is_active = $actv, updated_by = $userid , updatedat_location_id = $updatedat_location_id , location_code = $lcode_db where id= $lid";
            //echo $qry;
            $no = $this->execUpdate($qry);
            return $no;
        }
        
         public function getLocations($type=false) {		
		$query = "select id,name from it_locations where is_active=1";
                if(isset($type) && trim($type)!=""){
                    $query .= " and location_type_id in ( $type ) ";
                }
                //echo $query;
		$user = $this->fetchAllObjects($query);
		return $user;
	}
        
        
         public function getLocationTypes() {		
		$query = "select id,name from it_location_types ";
                //echo $query;
		$user = $this->fetchAllObjects($query);
		return $user;
	}
        
        public function getHubLocations() {		
		$query = "select id,name from it_locations where is_active=1 and location_type_id = 2";
                //echo $query;
		$user = $this->fetchAllObjects($query);
		return $user;
	}
        
        public function insertLocationDependancy($parent_location_id,$child_location_id,$userid,$createdat_location_id){            
            $qry = "insert into it_location_dependancy set parent_location_id=$parent_location_id,child_location_id=$child_location_id,createdby=$userid,createtime=now(),createdat_location_id = $createdat_location_id ";
//            print "<br>$qry";
            $id = $this->execInsert($qry);
            return $id;
        }
        
         public function updateLocationDependancy($parent_location_id,$child_location_id,$userid,$updatedat_location_id){            
            $qry = "update it_location_dependancy set parent_location_id=$parent_location_id ,updatedby=$userid,updatetime=now(),updatedat_location_id = $updatedat_location_id  where child_location_id=$child_location_id";
//            print "<br>$qry";
            $this->execUpdate($qry);            
        }
        
        public function fetchLocationById($locid){
           $query = "select l.* , lt.name as location_type from it_locations l , it_location_types lt where l.location_type_id = lt.id and l.id= $locid "; 
           $obj = $this->fetchObject($query);
           return $obj;
        }
        
        public function fetchDependantHub($child_location_id){
           $query = "select parent_location_id from it_location_dependancy where child_location_id = $child_location_id "; 
           $obj = $this->fetchObject($query);
           return $obj;
        }
        
         public function insertEventInfo($location_id,$day_of_week,$event_time,$userid,$createdat_location_id){            
            $event_time_db = $this->safe(trim($event_time));
            $qry = "insert into it_events_info set location_id=$location_id,day_of_week=$day_of_week,event_time=$event_time_db,createdby=$userid,createtime=now(),createdat_location_id = $createdat_location_id ";
//            print "<br>$qry";
            $id = $this->execInsert($qry);
            return $id;
        }
        
         public function getLocationByCode($code, $id=FALSE) {		
            $code_trim = str_replace(" ","",$code);
            $code_db = $this->safe($code_trim);
            $clause = "replace(location_code,' ','')=$code_db";
            $idcls = "";
            if(isset($id) && trim($id) != ""){
                 $idcls = " and id != $id ";
            }
            $query = "select id,location_code from it_locations where $clause $idcls";
            //echo $query;
            $obj = $this->fetchObject($query);
            return $obj;
	}
        
        public function fetchEventInfo($location_id,$day_of_week=false){
            $query = "select id,location_id,day_of_week,event_time,is_active from it_events_info where location_id = $location_id ";
            //echo $query;
            if(isset($day_of_week) && trim($day_of_week)!=""){
              $query .= " and day_of_week = $day_of_week ";
            }else{
              $query .= " and is_active = 1 ";  
            }
            $objs = $this->fetchAllObjects($query);
            return $objs; 
        }
        
        public function inactiveEventInfo($location_id){
            $query = "update it_events_info set is_active = 0  where location_id = $location_id and is_active = 1";
            //echo $query;
            $this->execUpdate($query);
            
        }
        
        public function updateEventInfo($location_id,$day_of_week,$event_time,$userid,$updatedat_location_id){            
            $event_time_db = $this->safe(trim($event_time));
            $qry = "update it_events_info set event_time=$event_time_db,is_active= 1,updatedby=$userid,updatetime=now(),updatedat_location_id = $updatedat_location_id where location_id = $location_id and day_of_week = $day_of_week";
//            print "<br>$qry";
            $this->execUpdate($qry);            
        }
        
        public function updateLocationDependancyFlag($lid,$flag,$userid,$updatedat_location_id){                        
            $qry = "update it_locations set is_dependant = $flag,updated_by=$userid,updatetime=now(),updatedat_location_id = $updatedat_location_id  where  id= $lid";
//            echo $qry;
            $this->execUpdate($qry);            
        }
        
        public function inactivateLocationDependancy($child_location_id,$userid,$updatedat_location_id){            
            $qry = "update it_location_dependancy set is_active = 0,updatedby=$userid,updatetime=now(),updatedat_location_id = $updatedat_location_id  where child_location_id=$child_location_id ";
//            print "<br>$qry";
            $this->execUpdate($qry);            
        }
        
        public function getProductCnt(){
            $qry = "select count(*) as cnt from it_products where is_active = 1";
            $obj = $this->fetchObject($qry);
            return $obj;
        }
        
        // to get hub and event type of locations
        public function getHubEventLocations() {		
            $query = "select id,name from it_locations where is_active=1 and location_type_id in (1,2) and is_dependant = 0";
            //echo $query;
            $user = $this->fetchAllObjects($query);
            return $user;
	}     
        
}