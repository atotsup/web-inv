<?php
require('db.php');

function makemenu() {
	global $pdo;
	$menuitems = [
		['/', 'Главная'],
		['init.php', 'Инициализация'],
	];
	echo "<div id='header_mouse_activity'>\n<div id='header_bg'></div>\n<div id='header'>\n";
	echo "\t<table class='menuitems'><tr>\n";
	foreach($menuitems as $menuitem) {
		echo "\t<th><a href='{$menuitem[0]}'>{$menuitem[1]}</a>\n";
	}
	echo "\t</tr></table>\n";
    echo "</div>\n</div>\n\n";
}

function msg($msg) {
	echo "<h3><pre>\n";
	echo $msg;
	echo "</pre></h3>\n";
}

?>
