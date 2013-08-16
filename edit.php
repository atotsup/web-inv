<?php
require('head.php');
title('Редактирование записи');

//DEBUG START
function nosql($var) {
	return PDO::quote($var);
}

function nohtml($var) {
	return htmlentities($var);
}

try {

if (isset($_REQUEST['table']) && isset($_REQUEST['id']) && ctype_lower($_REQUEST['table']) && ctype_digit($_REQUEST['id'])) {
	edit($_REQUEST['table'], $_REQUEST['id']);
} elseif (isset($_REQUEST['table']) && isset($_REQUEST['proceed']) && checkint($_REQUEST['proceed']) == 1 && count($_REQUEST)>3) {
	update($_REQUEST);
} else {
	throw new Exception('no arguments ("table" or "id") passed');
}
/*if (!isset($_REQUEST['table']) || !isset($_REQUEST['id']) || !ctype_lower($_REQUEST['table']) || !ctype_digit($_REQUEST['id'])) {
} elseif (!isset($_REQUEST['table']) || !isset($_REQUEST['proceed'])) {
}*/
//DEBUG END
//edit(nohtml($_REQUEST['table']), nohtml($_REQUEST['id']));
if (DEBUG) viewDebugInfo();

//DEBUG START
} catch (Exception $e) {
	if (DEBUG) {
		debugger(DEBUG_ERROR, $e->getFile() . ":" . $e->getLine(), $e->getMessage());
		viewDebugInfo();
		die();
	} else showError();
}
//DEBUG END
	
require('foot.php');
?>
