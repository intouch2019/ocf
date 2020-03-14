<?php

//Create a public URL at http://example.com/whatever.php, where example.com is your domain name and whatever.php is a PHP file that you can edit.

//Then put this code into whatever.php:

$webhookContent = "";

$webhook = fopen('php://input' , 'rb');
while (!feof($webhook)) {
    $webhookContent .= fread($webhook, 4096);
}
fclose($webhook);

error_log($webhookContent);

