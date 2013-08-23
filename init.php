<?php
require('head.php');
require('func_init.php');

title('Инициализация');
makemenu();
init_db();
init_tables();
fill_meta();

require('foot.php');
?>
