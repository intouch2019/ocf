<?php
require_once "../it_config.php";
require_once "lib/db/DBConn.php";

extract($_GET);
if (!isset($invoice_id)) { print "Missing invoice_id\n"; return; }

$db = new DBConn();
$inv = $db->fetchObject("select * from it_invoices where id=$invoice_id");
if (!$inv) { print "Invoice not found [$invoice_id]\n"; return; }
$dist = $inv->distid;
$invoicetext = $inv->invoicetext;

$cls_name = "cls_".$dist."_invoiceParser";
print "Parser=$cls_name.php\n";
require_once "lib/invoices/$cls_name.php";
$parser = new $cls_name();
$response = $parser->process($invoicetext);
print_r($response);
