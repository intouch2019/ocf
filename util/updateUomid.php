<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";

$db = new DBConn();
$cnt = 0;
//$q1 = "select gi.id,u.multply,gi.rate,g.uom_id from it_grnitems gi, it_grn g, it_uom u  where gi.grnid =g.id and g.uom_id = u.id and g.uom_id = 1";
$q1 = "select gi.id from it_grnitems gi, it_grn g, it_uom u  where gi.grnid =g.id and g.uom_id = 1";
$objs = $db->fetchAllObjects($q1);
//print_r($objs);
foreach($objs as $obj){
//    $rate =trim($obj->rate)*trim($obj->multply);
    $q2="update it_grnitems set uom_id = 1 where id = $obj->id";//rate = $rate ,
    print"\n$q2\n";
    $no = $db->execUpdate($q2);
    $cnt = $cnt+$no;
}
print"\n No of rows Updated= $cnt";