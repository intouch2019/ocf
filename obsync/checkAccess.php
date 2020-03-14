<?php
require_once "lib/db/DBConn.php";

$id = false;
$hash1 = false;
$t = false;
$params = array();
ksort($_POST);
foreach ($_POST as $key => $value) {
	if ($key == 'id') { $id = $value; continue; }
	if ($key == 'hash') { $hash1 = $value; continue; }
	if ($key == 't') { $t = $value; }
	$params[] = "$key:$value";
}
//print_r($params);
$params_str = implode(",",$params);
if (!$id || !$hash1) { print "1::Authentication failure1"; exit; }
$db = new DBConn();
//$obj = $db->fetchObject("select s.*, p.PASSCODE from it_stores s, it_stores_pass p where s.ID=$id and s.ID = p.STORE_ID and p.PASSCODE is not null");
//$obj = $db->fetchObject("select i.* from it_pos_instances i where i.id =$id and i.license is not null ");
// query the region_id as well
$obj = $db->fetchObject("select i.*, s.region_id from it_pos_instances i left outer join it_stores s on i.store_id = s.id where i.id=$id and i.license is not null");
$db->closeConnection();
//if (!$obj || !isset($obj->PASSCODE) || trim($obj->PASSCODE) == "") { print "1::Authentication failure2"; exit; }
if (!$obj || !isset($obj->license) || trim($obj->license) == "") { print "1::Authentication failure2"; exit; }
//$hash2 = sha1($params_str.",".$obj->PASSCODE);
$hash2 = sha1($params_str.",".$obj->license);
//print "hash2=>".$hash2;
if ($hash1 != $hash2) { 
	print "1::Authentication failure3:"; exit;
}
$mytime = time();
if ($mytime > $t) { $diff = $mytime - $t; }
else { $diff = $t - $mytime; }
//if ($diff > 300) { // more than 5 minutes
//	print "1::Authentication failure4:$t:$mytime"; exit;
//}
$gStoreId = $id;
$obj->LICENSE=null;
$gStore = $obj;

function getCurrStoreId() {
	global $gStoreId;
	return $gStoreId;
}
