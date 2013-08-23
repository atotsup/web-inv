<?php
require('func.php');

function title($title) {
	$charset = "utf-8";
	$css = "main.css";

	echo "<!DOCTYPE HTML>\n";
	echo "<html>\n";
	echo "<head>\n";
	echo "<meta charset='$charset'>\n";
	echo "<title>$title</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='$css'>\n";
	echo "</head>\n";
	echo "<body>\n\n";
}

?>
