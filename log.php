<?php

require_once "../it_config.php";
require_once "lib/db/DBConn.php";

$page = 0;
if (isset($_GET['page'])) { $page = $_GET['page']; }

$db=new DBConn();
$page = $page * 50;
$objs = $db->fetchObjectArray("select * from it_logs order by id desc limit $page,50");
?>
<table border="1">
<th>
<td>ID</td>
<td>API</td>
<td>Message</td>
<td>Time</td>
</th>
<?php
foreach ($objs as $obj) { ?>
<tr>
<td><?php echo trim($obj->id); ?></td>
<td><?php echo trim($obj->apiname); ?></td>
<td><?php echo trim($obj->message); ?></td>
<td><?php echo trim($obj->createtime); ?></td>
</tr>
<?php } ?>
</table>
