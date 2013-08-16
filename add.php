<?php
require('head.php');
title('Добавление записи');

//DEBUG START
try {

if (!isset($_REQUEST['table'])) {
	throw new Exception('no argument ("table") passed');
}
//DEBUG END
add($_REQUEST['table']);

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
