<?php
$currStore = getCurrStore();
if ($currStore) {
    header(DEF_BASEURL."/admin/overview");
} else {
require_once "inc.storehome.php";
}
?>
