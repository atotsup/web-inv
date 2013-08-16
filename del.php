<?php
require('head.php');
title('Удаление записи');

//DEBUG START
try {

if (!isset($_REQUEST['table']) || !isset($_REQUEST['id'])) {
	throw new Exception('no arguments ("table" or "id") passed');
}
//DEBUG END
del($_REQUEST['table'], $_REQUEST['id']);

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
