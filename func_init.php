<?php
require_once('func.php');
require('def_tables.php');

function init_db() {
	list($name, $charset, $collate) = db_info();
	$query_drop = "drop database if exists `{$name}`";
	$query_create = "create database if not exists `{$name}` default character set = '{$charset}' default collate = '{$collate}'";
	$server = connect();
	$server->exec($query_drop);
	$server->exec($query_create);
}

function init_tables() {
	$db = db_connect();
	$tables = def_tables();
	foreach($tables as $table) {
		list($tname, $tlabel, $fields) = $table;
		$query_drop = "drop table if exists `{$tname}`";
		$query_create = "create table if not exists `{$tname}` (\n";
		$i = 0;
		foreach($fields as $field) {
			list($ftype, $fname, $flabel, $flookup) = $field;
			if($i == 0) {
				$fargs = "auto_increment";
				$pkey = " primary key (`{$fname}`)";
				$i++;
			} else {
				$fargs = "comment '{$flabel}'";
			}
			$query_create .= "  `{$fname}` {$ftype} not null {$fargs},\n";
		}
		$query_create .= "{$pkey}\n) engine=InnoDB comment='{$tlabel}'\n";
		$db->exec($query_drop);
		$db->exec($query_create);
	}
}

function fill_meta() {
	$db = db_connect();
	$tables = def_tables();
	foreach($tables as $table) {
		list($tname, $tlabel, $fields) = $table;
		$query_insert_tables = "insert into `tables` (`table`) values ('{$tname}')\n";
		$db->exec($query_insert_tables);
		$tableID = getValue("select `tableID` from `tables` where `table` = '{$tname}'");
		$query_insert_fields = "insert into `fields` (`tableID`, `field`, `label`, `order`) values (\n";
		$query_insert_lookups = "insert into `lookups` (`fieldID`, `lookup_fieldID`, `lookup_tableID`, `lookup_order`) values (\n";
		$forder = 0;
		foreach($fields as $field) {
			list(, $fname, $flabel, $flookup) = $field;
			$query_insert_field = "{$tableID}, '{$fname}', '{$flabel}', {$forder})";
			$db->exec($query_insert_fields . $query_insert_field);
			$fieldID = getValue("select `fieldID` from `fields` where `tableID` = {$tableID} and `field` = '{$fname}'");
			if( $flookup != "" ) {
				$lookup_in_tables = split(';', $flookup);
				foreach($lookup_in_tables as $lookup_in_table) {
					list($lookup_table, $lookup_fields) = split(":", $lookup_in_table);
					$lookup_fields = split(",", $lookup_fields);
					$lorder = 0;
					foreach($lookup_fields as $lookup_field) {
						$lookup_tableID = getValue("select `tableID` from `tables` where `table` = '{$lookup_table}'");
						$lookup_fieldID = getValue("select `fieldID` from `fields` where `tableID` = {$lookup_tableID} and `field` = '{$lookup_field}'");
						$query_insert_lookup = "{$fieldID}, '{$lookup_fieldID}', '{$lookup_tableID}', {$lorder})";
						$db->exec($query_insert_lookups . $query_insert_lookup);
						$lorder++;
					}
				}
			}
			$forder++;
		}
	}
}

function getValue($query) {
	$db = db_connect();
	$st = $db->prepare($query);
	$st->execute();
	list($value) = $st->fetch(PDO::FETCH_NUM);
	return $value;
}

?>
