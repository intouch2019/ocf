<?php 
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once("session_check.php");

$roleid = isset($_GET['roleid']) ? ($_GET['roleid']) : false;
if (!$roleid) { return error("missing parameters"); }

try {
    $permissionList = array();
    $db = new DBConn(); 
    $query="select p.id,p.pagename from it_permission p,it_role_permissions rp where rp.permissionid = p.id and rp.roleid = $roleid"; 
    $permissionsObj = $db->fetchObjectArray($query);
    
    foreach ($permissionsObj as $permission) {
        $permissionList[] = $permission->id."::".$permission->id.".".$permission->pagename;    
    }    
    if ($permissionList) { success($permissionList); }
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

function success($permissionList) {
    print json_encode($permissionList);
}
?>