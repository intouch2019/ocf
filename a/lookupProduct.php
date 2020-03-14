<?php

$letters = $_GET['letters'];
$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
if (strlen($letters) < 3) {
	echo "1###Enter first 3 letters|";
	return; 
}

echo "1###Watermelon|";
echo "1###Apple Juice|";
echo "1###Protein Booster [L]|";

?>
