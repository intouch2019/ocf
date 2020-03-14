<?php
require_once "../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";

if (!$gCurrStore) { return; }
extract($_GET);
if (!$id && !$billno) { return; }

$db = new DBConn();
if (isset($id)) {
$query = "select * from it_invoices where id=$id and distid=$gCurrStore->dist_ids";
} else {
$query = "select * from it_orders where invoice_no=$billno and distid=$gCurrStore->dist_ids";
}
$obj = $db->fetchObject($query);
if (!$obj) { return; }
$orderinfo = $obj->invoicetext;
/*
$fh = fopen("r$id.txt", "w");
fputs($fh, $orderinfo);
fclose($fh);
*/
if (isset($parsed)) { ?>
<table border="0">
<tr><td>Receipt Number: </td><td><?php echo $obj->bill_no; ?></td></tr>
<tr><td>Date-time: </td><td><?php echo $obj->bill_datetime; ?></td></tr>
<tr><td>Amount: </td><td><?php echo $obj->bill_amount; ?></td></tr>
<tr><td>Quantity: </td><td><?php echo $obj->bill_quantity; ?></td></tr>
<?php if ($obj->bill_discountval) { ?>
<tr><td>Discount Val: </td><td><?php echo $obj->bill_discountval; ?></td></tr>
<?php } ?>
<?php if ($obj->bill_discountpct) { ?>
<tr><td>Discount %: </td><td><?php echo $obj->bill_discountpct; ?></td></tr>
<?php } ?>
</table>
<table border="0">
<tr><th>Item Name</th><th>Quantity</th><th>Price</th></tr>
<?php
$query = "select * from it_rawitems r, it_rawitemlines ri where ri.orderid = $id and ri.rawitemid = r.id";
$items = $db->fetchObjectArray($query);
foreach ($items as $item) {
$style="";
if ($item->font) {
	$style='style="font-family:'.$item->font.';font-size:1.4em;"';
}
?>
<tr><td <?php echo $style; ?>><?php echo $item->itemname; ?></td><td><?php echo $item->linequantity; ?></td><td><?php echo $item->linetotal; ?></td></tr>
<?php } ?>
</table>
<?php
} else {
$orderinfo = str_replace("\n","<br />",$orderinfo);
print '<pre>';
print $orderinfo;
print '</pre>';
}
?>
