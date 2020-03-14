<?php
require_once "../it_config.php";
require_once "session_check.php";
require_once("UriHandler.class.php");
$uri = null;
//print_r($_GET);
//return;
if (isset($_GET['uri'])) { $uri = $_GET['uri']; }
//print_r($_GET);
//print_r($_POST);
//$ip = null;
/*if(isset($_POST['ip'])){ $ip = $_POST['ip']; }
if($ip != null){
	if($ip == "116.75.187.67"){
	   $uriHandler = new UriHandler($uri);
	   $uriHandler->displayContent();
	}
}else{
	echo "Access denied.";
}*/
$uriHandler = new UriHandler($uri);
$uriHandler->displayContent();

?>
