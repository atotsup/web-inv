<?php
require('head.php');
title('Создание структуры');
makemenu();

/*

*/

$devicemodels = ['devicemodels', 'Модели комплектующих', [
	[ 'devicemodelID', 	CT_INT, 	1, 0, 'ID', 			0, '', 			0],
	[ 'vendorID', 		CT_INT, 	0, 1, 'Производитель', 	1, 'vendor', 	1],
	[ 'devicemodel', 	CT_TEXT, 	0, 0, 'Модель', 		1, '', 			2]
]];

$devices = ['devices', 'Комплектующие', [
	[ 'deviceID', 		CT_INT, 	1, 0, 'ID', 				0, '', 				0],
	[ 'devicemodelID', 	CT_INT, 	0, 1, 'Модель', 			1, 'devicemodel', 	1],
	[ 'itemID', 		CT_INT, 	0, 1, 'Основное средство', 	1, '', 				2],
	[ 'warranty', 		CT_INT, 	0, 0, 'Гарантия', 			1, '', 				3],
	[ 'accountID', 		CT_INT, 	0, 1, 'Счет', 				1, 'account', 		4],
	[ 'price', 			CT_INT, 	0, 0, 'Цена', 				1, '', 				5],
	[ 'sn', 			CT_TEXT, 	0, 0, 'Сер.№', 				1, '', 				6],
	[ 'stateID', 		CT_INT, 	0, 1, 'Состояние', 			1, 'state', 		7]
]];

$rms = ['rms', 'РМ в наличии', [
	[ 'rmID', 	CT_INT, 	1, 0, 'ID', 				0, '',	0],
	[ 'itemID',	CT_INT, 	0, 1, 'Основное средство', 	1, '', 	1]
]];

$rmlogs = ['rmlogs', 'Журнал РМ', [
	[ 'rmlogID', 		CT_INT, 	1, 0, 'ID', 	0, '',				0],
	[ 'rmID', 			CT_INT, 	0, 1, 'РМ', 	1, '',				1],
	[ 'date', 			CT_DATE, 	0, 0, 'Дата',	1, '',				2],
	[ 'rmserviceID',	CT_INT, 	0, 1, 'Сервис',	1, 'rmservice', 	3]
]];

$rmservices = ['rmservices', 'Типы сервисов РМ', [
	[ 'rmserviceID', 	CT_INT, 	1, 0, 'ID', 		0, '',	0],
	[ 'rmservice',		CT_TEXT, 	0, 0, 'Сервис', 	1, '', 	1]
]];

$contragents = ['contragents', 'Контрагенты', [
	[ 'contragentID', 	CT_INT, 	1, 0, 'ID', 			0, '',	0],
	[ 'contragent',		CT_TEXT, 	0, 1, 'Контрагент', 	1, '', 	1]
]];

$softwares = ['softwares', 'ПО', [
	[ 'softwareID', 		CT_INT, 	1, 0, 'ID', 			0, '',					0],
	[ 'software',			CT_TEXT, 	0, 0, 'ПО', 			1, '', 					1],
	[ 'softwarevendorID',	CT_INT, 	0, 1, 'Производитель', 	1, 'softwarevendor', 	2]
]];

$softwarevendors = ['softwarevendors', 'Производители ПО', [
	[ 'softwarevendorID', 	CT_INT, 	1, 0, 'ID', 				0, '',	0],
	[ 'softwarevendor',		CT_TEXT, 	0, 1, 'Производитель ПО', 	1, '', 	1]
]];

$computersoftwares = ['computersoftwares', 'ПО', [
	[ 'computersoftwareID',		CT_INT, 	1, 0, 'ID', 				0, '',			0],
	[ 'softwareID',				CT_INT, 	0, 1, 'ПО', 				1, 'software', 	1],
	[ 'itemID',					CT_INT, 	0, 1, 'Основное средство', 	1, '', 			2],
	[ 'licence',				CT_TEXT, 	0, 0, 'Лицензия', 			1, '', 			3],
	[ 'accountID',				CT_INT, 	0, 1, 'Счет', 				1, 'account', 	4],
	[ 'price',					CT_INT, 	0, 0, 'Цена', 				1, '', 			5]
]];

/*
createtable($rms);
createtable($rmlogs);
createtable($rmservices);
createtable($contragents);
createtable($softwares);
createtable($softwarevendors);
createtable($computersoftwares);
*/

?>
