<?php
require('db.php');

function view_old($table) {
	debugger(0, "view($table)");
	$table_label = getTableLabel($table);
	echo "<div class='center'>\n";
	echo "<table class='test1'>\n";
	echo "<caption id='$table'><h3>$table_label</h3></caption>\n";
	echo "<tr>\n";
	foreach(getFieldLabels($table) as $label) {
		echo "<th>$label\n";
	}
	foreach(getData($table) as $row) {
		echo "<tr>\n";
		foreach($row as $field) {
			echo "<td>$field\n";
		}
	}
	echo "</table>\n";
	echo "</div>\n\n";
}

function getData($table) {
	debugger(0, "getData($table)");
	global $pdo;
	$query = "select * from $table";
	debugger(3, "getData($table)", $query, "debugQueries");
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$fields = getFields($table);
	foreach ($rows as $row) {
		foreach ($fields as $field) {
			if (isID($field) and ! isPrimary($field, $table)) {
				$value = getValueByID($field, $row[$field]);
			} else {
				$value = $row[$field];
			}
			$output[] = $value;
		}
		$data[] = $output;
		unset($output);
	}
	return $data;
}

function getTableLabel($table) {
	debugger(0, "getTableLabel($table)");
	global $pdo;
	$query = "select tables.label from tables where tables.table = :table";
	debugger(3, "getTableLabel($table)", $query, "debugQueries");
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':table', $table, PDO::PARAM_STR);
	$stmt->execute();
	if($row = $stmt->fetch(PDO::FETCH_NUM)) {
		$label = $row[0];
	} else {
		$label = $table;
	}
	return $label;
}

function getFieldLabels($table) {
	debugger(0, "getFieldLabels($table)");
	global $pdo;
	$query = "select meta.label from meta where meta.table = :table order by meta.order";
	debugger(3, "getFieldLabels($table)", $query, "debugQueries");
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':table', $table, PDO::PARAM_STR);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_NUM)) {
		$labels[] = $row[0];
	}
	return $labels;
}

function getValueByID($fieldid, $id) {
	debugger(0, "getValueByID($fieldid, $id)");
	global $pdo;
	$table = substr($fieldid, 0, -2) . "s";
	$query1 = "select meta.lookup from meta where meta.field = :field";
	debugger(3, "getValueByID($fieldid, $id)", $query1, "debugQueries");
	$stmt = $pdo->prepare($query1);
	$stmt->bindValue(':field', $fieldid, PDO::PARAM_STR);
	$stmt->execute();
	$lookup = $stmt->fetch(PDO::FETCH_NUM)[0];
	$query2 = "select $lookup from $table where $fieldid  = :id";
	debugger(3, "getValueByID($fieldid, $id)", $query2, "debugQueries");
	$stmt = $pdo->prepare($query2);
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$value = $stmt->fetch(PDO::FETCH_NUM)[0];
	return $value;
}

function getFields($table) {
	debugger(0, "getFields($table)");
	global $pdo;
	$query = "select meta.field from meta where meta.table = :table order by meta.order";	
	debugger(3, "getFields($table)", $query, "debugQueries");
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':table', $table, PDO::PARAM_STR);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_NUM)) {
		$fields[] = $row[0];
	}
	return $fields;
}

function isID($field) {
	debugger(0, "isID($field)");
	if (substr($field,-2) == "ID") {
		return true;
	} else {
		return false;
	}
}

function isPrimary($field, $table) {
	debugger(0, "isPrimary($field, $table)");
	if (getIDname($table) == $field and isPrimaryFlag($field, $table)) {
		return true;
	} else {
		return false;
	}
}

function isPrimaryFlag($field, $table) {
	debugger(0, "isPrimaryFlag($field, $table)");
	global $pdo;
	$query = "select meta.primary from meta where meta.table = :table and meta.field = :field and meta.primary = :primary";
	debugger(3, "isPrimaryFlag($field, $table)", $query, "debugQueries");
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':table', $table, PDO::PARAM_STR);
	$stmt->bindValue(':field', $field, PDO::PARAM_STR);
	$stmt->bindValue(':primary', true, PDO::PARAM_BOOL);
	$stmt->execute();
	if($stmt->fetch()) {
		$isResult = true;
	} else {
		$isResult = false;
	}
	return $isResult;
}

?>
