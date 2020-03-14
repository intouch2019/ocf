<?php
require_once "../../it_config.php";
extract($_GET);
foreach ($_GET as $key => $value) {	
    $params[] = "$key:$value";
}
$params_str = implode(",",$params);
$redirect = DEF_SITEURL."getSMS/smsServe.php?params=".$params_str;
header("Location: ".$redirect);
