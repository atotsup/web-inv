<?php
define('BOOL', 'tinyint(1)');
define('INT', 'int(11)');
define('TXT', 'text');
define('DATE', 'date');

function def_tables() {
	/*	[ NAME, LABEL, [
			[ TYPE, FIELD, LABEL, LOOKUP]]],*/
	$tables = [
		[ 'tables', 'Таблицы', [
			[ INT,	'tableID',	'ID',		''],
			[ TXT,	'table',	'Таблица',	'']]],
		[ 'fields', 'Поля', [
			[ INT,	'fieldID',	'ID',		''],
			[ INT,	'tableID',	'Таблица',	'tables:table'],
			[ TXT,	'field',	'Поле',		''],
			[ TXT,	'label',	'Метка',	''],
			[ INT,	'order',	'Порядок',	'']]],
		[ 'lookups', 'Подстановки', [
			[ INT,	'lookupID',			'ID',		''],
			[ INT,	'fieldID',			'Поле',		'fields:field'],
			[ INT,	'lookup_fieldID',	'Значение',	'fields:field'],
			[ INT,	'lookup_tableID',	'Таблица',	'tables:table'],
			[ INT,	'lookup_order',		'Порядок',	'']]],
	];
	return $tables;
}

function def_values() {
	$values = [];
	return $tables;
}

?>
