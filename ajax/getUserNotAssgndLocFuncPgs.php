<?php 
require_once "../../it_config.php";
require_once "lib/db/DBConn.php";
require_once("session_check.php");

$userid = isset($_GET['userid']) ? ($_GET['userid']) : false;
$locationid = isset($_GET['locationid']) ? ($_GET['locationid']) : false;
if (!$userid) { return error("missing parameters"); }

try {
    $pagelist = array();
    //$count=0;
    $db = new DBConn(); 
    //note : page sequence -1 means page is not in use anymore
    //$query=" select id, menuhead, pagename from it_pages where id not in (select distinct page_id from it_user_pages where user_id = $user_id) and sequence != -1 group by menuhead,pagename";
    $query=" select p.id, menuhead, p.pagename from it_functionality_pages p , it_location_functionalities lp  where lp.functionality_id = p.id and  p.id not in (select distinct functionality_id from it_location_functionalities  lp , it_user_location_functionalities up where up.location_functionality_id = lp.id and up.user_id = $userid and lp.location_id = $locationid and up.is_active = 1 and lp.is_active = 1 ) and lp.location_id = $locationid group by menuhead,pagename";
   // print $query;
//    error_log("\nPG QRY: $query\n",3,"tmp.txt");
    $pageobjs = $db->fetchObjectArray($query);
    
    foreach ($pageobjs as $pageob) {
        $pagelist[] = $pageob->id."::".$pageob->menuhead."::".$pageob->pagename;    
    }    
    if ($pagelist) { success($pagelist); }
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

function success($pagelis) {
    print json_encode($pagelis);
}
?>