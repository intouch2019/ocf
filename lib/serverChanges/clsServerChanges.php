<?php

require_once "lib/db/dbobject.php";

class clsServerChanges extends dbobject {

	

	public function insert($type, $data ,$data_id=false, $region_id=null , $store_id=null,$instance_id=null ) {
		$data = $this->safe($data);
                $addClause="";
                if(trim($region_id)!=""){ $addClause .= " , region_id = $region_id ";}
                if(trim($store_id)!=""){ $addClause .= " , store_id = $store_id ";}
                if(trim($instance_id)!=""){ $addClause .= " , instance_id = $instance_id ";}
		$query = "insert into it_server_changes set type=$type , changedata = $data , data_id = $data_id ,createtime = now() $addClause ";		
//		error_log("\nSER CH QRY:-".$query."\n",3,"../ajax/tmp.txt");
                $insertid = $this->execInsert($query);
//                print"<br>servre chang Q= $query<br>";
//                print"<br>server change id=$insertid<br>";
	}
        /**
//        public function save($type, $data, $storeid , $data_id = false , $region_id = false ) {
//		$data = $this->safe($data);
//
//		$query = "insert into it_server_changes set type=$type , changedata = $data , store_id = $storeid , data_id = $data_id ,region_id = $region_id ,createtime = now() ";		
//		error_log("\nSER CH QRY:-".$query."\n",3,"../ajax/tmp.txt");
//                $insertid = $this->execInsert($query);
//	}
//        
//        public function savewithInst($type, $data, $storeid , $data_id = false , $region_id = false , $instance_id  ) {
//		$data = $this->safe($data);
//
//		$query = "insert into it_server_changes set type=$type , changedata = $data , store_id = $storeid , data_id = $data_id ,region_id = $region_id ,instance_id  = $instance_id ,createtime = now() ";		
//		error_log("\nSER CH QRY:-".$query."\n",3,"../ajax/tmp.txt");
//                $insertid = $this->execInsert($query);
//	} 
         * 
         */

}



?>