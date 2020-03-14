<?php
require_once "../../../it_config.php";
require_once "DBConn.php";

$db = new DBConn();

//$conn =  new mysqli("localhost","sarotam","int0uch2718","sarotam_db");
$query = "select * from it_users where id = 2";
$obj = $db->fetchObject($query);
echo $obj->id." ".$obj->username."\n";

?>
