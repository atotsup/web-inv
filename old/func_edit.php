<?php

function edit($request) {
	try {

	if (isset($request['table']) && isset($request['proceed']) && checkint($request['proceed']) == 1 && count($request)>3) {
		exec_edit($request);
	} elseif (isset($request['table']) && isset($request['id']) && ctype_lower($request['table']) && ctype_digit($request['id'])) {
		prep_edit($request['table'], $request['id']);
	} else {
		throw new Exception('no arguments ("table" or "id") passed');
	}
	if (DEBUG) viewDebugInfo();

	//DEBUG START
	} catch (Exception $e) {
		if (DEBUG) {
			debugger(DEBUG_ERROR, $e->getFile() . ":" . $e->getLine(), $e->getMessage());
			viewDebugInfo();
			die();
		} else showError();
	}
}

function prep_edit($table, $id) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "prep_edit($table, $id)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table) || !isset($id)) {
		throw new Exception('no arguments "table" or "id" passed');
	}
	//DEBUG END
	$id = checkint($id);
	$table = checkstr($table);
	$tableexist = false;
	if ($id && $table) {
		global $pdo;
		$query = "select `table` from `tables`";
		//DEBUG START
		if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
		//DEBUG END
		$stmt = $pdo->prepare($query);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_NUM)) {
			if ($row[0] == $table) {
				$tableexist = true;
			}
		}
	}
	if ($tableexist) {
		form_edit($table, $id);
	} else {
		throw new Exception("'id' or 'table' values are not valid");
	}
	//DEBUG START
	} catch (Exception $e) {
		if (DEBUG) {
			debugger(DEBUG_ERROR, $e->getFile() . ":" . $e->getLine() . ":" . $debugfunc, $e->getMessage());
			viewDebugInfo();
			die();
		} else showError();
	}
	//DEBUG END
}

function form_edit($table, $value) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "form_edit($table, $value)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table) || !isset($value)) {
		throw new Exception('no arguments "table" or "value" passed');
	}
	//DEBUG END
	global $pdo;
	$id = getIDname($table);
	$query = "select * from $table where $id = :$id";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
	//DEBUG END
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(":$id", $value, PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo "<div class='center'>\n<form action='edit.php' method='get'>\n<table class='test1'>\n\t<tr>\n";
	echo "\t\t<input type='hidden' name='proceed' value='1'>\n";
	echo "\t\t<input type='hidden' name='table' value='$table'>\n";
	foreach($row as $key => $field) {
		if($label = getLabelByField($table, $key)) {
			$label_or_key = $label;
		} else {
			$label_or_key = $key;
		}
		echo "\t<tr>\n\t\t<td><label for='$key'>$label_or_key</label>\n\t\t<td>";
		if( ($key != $id) and ($lookups = lookupvalues($key)) ) {
			drawselectbox($lookups, $key, $field);
		} else {
			echo "<input type='text' name='$key' value='" . clean($field) . "'>";
		}
		echo "\n";
	}
	echo "</table>\n<button type='submit'>Save</button>\n</form>\n</div>\n";
	//DEBUG START
	} catch (Exception $e) {
		if (DEBUG) {
			debugger(DEBUG_ERROR, $e->getFile() . ":" . $e->getLine() . ":" . $debugfunc, $e->getMessage());
			viewDebugInfo();
			die();
		} else showError();
	}
	//DEBUG END
}

function exec_edit($record) {
	global $pdo;
	$table = $record['table'];
	if (!checkTable($table)) {
		echo "<h1>checktable false</h1>\n";
		die;
	}
	$query = "update $table set \n";
	$i = 0;
	$comma = ",\n";
	foreach($record as $key => $field) {
		$i++;
		if($i > 2) {
			if($i == 3) {
				$fieldid = $key;
				$id = $field;
			}
			if(count($record) == $i) {
				$comma = "\n";
			}
			$fields[] = [$key, $field];
			$query = $query . "\t$key = :$key" . $comma;
		}
	}
	$query = $query . " where $fieldid = :$fieldid";
	$stmt = $pdo->prepare($query);
	foreach ($fields as $field) {
		$stmt->bindValue(":".$field[0], clean($field[1]));
	}
	$stmt->execute();
	makemenu();
	echo "<h1>Запись сохранена</h1>\n";
	view();
}

?>
