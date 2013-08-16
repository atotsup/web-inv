<?php
require('head.php');
title("МБК инвентарь: главная");

makemenu();
view('items');
view('users');
view('places');
view('models');
view('types');
view('accounts');
view('providers');
view('states');
view('userstates');
view('rmmodels');
view('rmprinters');
if(DEBUG) {
	viewDebugInfo();
	viewDebugInfo('debugQueries');
}

require('foot.php');
?>
