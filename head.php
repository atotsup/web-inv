<?php
require('func.php');

function title($title) {
	//DEBUG START
	if (DEBUG) debugger(DEBUG_CALL, "head.php : title($title)");
	//DEBUG END
	$charset = "utf-8";
	$css = "main.css";
	$js = "main.js";

	echo "<!DOCTYPE HTML>\n";
	echo "<html>\n";
	echo "<head>\n";
	echo "<meta charset='$charset'>\n";
	echo "<title>$title</title>\n";
	echo "<link rel='stylesheet' type='text/css' href='$css'>\n";
	echo "<script type='text/javascript' src='$js'></script>\n";
	echo "</head>\n";
	echo "<body>\n\n";
	//DEBUG START
	if (!isset($title)) {
		if (DEBUG) {
			debugger(DEBUG_ERROR, 'head.php : title($title)', "no argument 'title' passed");
			viewDebugInfo();
			die();
		} else {
			showError();
			die();
		}
	}
	//DEBUG END
}

?>
