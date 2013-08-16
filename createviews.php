<?php
require('head.php');
title('Создание представлений');
makemenu();

/*
select userID as ID, name as Пользователь, userstate as Статус from {oj users natural left join userstates} where deleted = 1 order by userstate,name

modelID 	typeID Тип	vendorID Производитель	model Модель

*/

$users = ['users', ['userstates'], '`userstate`,`name`', [
	['userID', 'ID'],
	['name', 'Пользователь'],
	['userstate', 'Статус']
]];

$models = ['models', ['types', 'vendors'], '`type`, `vendor`, `model`', [
	['modelID', 'ID'],
	['type', 'Тип'],
	['vendor', 'Производитель'],
	['model', 'Модель']
]];

//createview($models);
//list_tables();
mass_create_views();

function mass_create_views() {
	global $pdo;
	$mass = array();
	$querytables = "select `table`, `joins` from `tables`";
	$stmt = $pdo->prepare($querytables);
	$stmt->execute();
	$records = $stmt->fetchAll(PDO::FETCH_NUM);
	foreach($records as $record) {
		$one = array();
		$two = "";
		$three = array();
		$table = $record[0];
		if ($record[1] != "") {
			$joins = explode(",", $record[1]);
		} else {
			$joins = null;
		}
		$one[] = $table;
		$one[] = $joins;
		$querymeta = "select `field`, `label`, `lookup` from `meta` where `table` = :table order by `order`";
		$querymeta2[] = $querymeta;
		$stmt2 = $pdo->prepare($querymeta);
		$stmt2->bindValue(':table', $table);
		$stmt2->execute();
		$result = $stmt2->fetchAll(PDO::FETCH_NUM);
		$i = 0;
		foreach($result as $meta) {
			$field = $meta[0];
			$label = $meta[1];
			$lookup = $meta[2];
			$two .= "`" . $lookup . "`, ";
			if ($lookup == "") {
				$three[] = [$field, $label];
			} else {
				$three[] = [$lookup, $label];
			}
			$i++;
		}
		$one[] = $two;
		$one[] = $three;
		$mass[] = $one;
	}
	foreach($mass as $table) {
		createview($table, true);
	}
}

function list_tables() {
	global $pdo;
	$query = "show tables from inv";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$tables = $stmt->fetchAll(PDO::FETCH_NUM);
	echo "<table>";
	foreach($tables as $fields) {
		echo "<tr>";
		foreach($fields as $field) {
			if ( (!strstr($field,"view")) AND ($field != "meta") AND ($field != "tables") AND ($field != "users") AND ($field != "models") ) {
				echo "<td>$field\n";
				add_deleted($field);
			}
		}
	}
	echo "</table>";
}

function add_deleted($table) {
	global $pdo;
	$query = "alter table $table add deleted tinyint(1) default 0 comment 'Удалено';";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
}

?>
