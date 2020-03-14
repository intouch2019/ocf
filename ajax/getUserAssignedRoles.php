<?php 
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once("session_check.php");

$userid = isset($_GET['userid']) ? ($_GET['userid']) : false;
if (!$userid) { return error("missing parameters"); }

try {
    $rolelist = array();
    //$count=0;
    $db = new DBConn(); 
    //note : page sequence -1 means page is not in use anymore
   // $query=" select id, menuhead, pagename from it_pages where id in (select page_id from it_usertype_pages where usertype = $utype) and sequence != -1 group by menuhead,pagename";
    $query="select ur.userid, r.* from it_user_roles ur, it_roles r where r.id = ur.roleid and ur.userid = $userid"; 
    //print $query;
   // error_log("\nPG QRY: $query\n",3,"tmp.txt");
    $rolesObj = $db->fetchObjectArray($query);
    
    foreach ($rolesObj as $role) {
        $rolelist[] = $role->id."::".$role->id.".".$role->name;    
    }    
    if ($rolelist) { success($rolelist); }
    else { error("Page Not Found"); }
} catch(Exception $xcp){
    echo "error:There was a problem processing your request. Please try again later.";
 //   return;
}

function error($msg) {
    print json_encode(array(
            "error" => "1",
            "message" => $msg
            ));
}

function success($rolelist) {
    print json_encode($rolelist);
}
?>