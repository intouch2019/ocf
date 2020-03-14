<?php 
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once("session_check.php");

$roleid = $_GET['roleid'];
if ($roleid == "" || $roleid == null) { return error("missing parameters"); }

try {
    $permissionlist = array();
    //$count=0;
    $db = new DBConn(); 
    //note : page sequence -1 means page is not in use anymore
    //$query=" select id, menuhead, pagename from it_pages where id not in (select distinct page_id from it_user_pages where user_id = $user_id) and sequence != -1 group by menuhead,pagename";
    $query="select p1.id,p1.pagename from it_permission p1 where p1.id not in (select p.id from it_permission p,it_role_permissions rp where rp.permissionid = p.id and rp.roleid = $roleid)";
//    print $query;
//    error_log("\nPG QRY: $query\n",3,"tmp.txt");
    $permissionsObj = $db->fetchObjectArray($query);
    
    foreach ($permissionsObj as $permission) {
        $permissionlist[] = $permission->id."::".$permission->id.".".$permission->pagename;    
    }    
    if ($permissionlist) { success($permissionlist); }
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

function success($permissionlist) {
    print json_encode($permissionlist);
}
?>