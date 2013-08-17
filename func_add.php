<?php

function add($request) {
	try {

	if (!isset($request['table'])) {
		throw new Exception('no argument ("table") passed');
	}
	//DEBUG END
	if (isset($request['table']) && isset($request['proceed']) && checkint($request['proceed']) == 1 && count($request)>3) {
		exec_add($request);
	} elseif (isset($request['table']) && ctype_lower($request['table']) ) {
		prep_add($request['table']);
	} else {
		throw new Exception('no arguments ("table" or "id") passed');
	}

	//DEBUG START
	} catch (Exception $e) {
		if (DEBUG) {
			debugger(DEBUG_ERROR, $e->getFile() . ":" . $e->getLine(), $e->getMessage());
			viewDebugInfo();
			die();
		} else showError();
	}
}

function form_add($table) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "form_add($table)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	global $pdo;
	$query = "select `field`, `label` from `meta` where `table`='$table' order by `order`";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
	//DEBUG END
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_NUM);
	echo "<div class='center'><form action='add.php' method='get'><table class='test1'><tr>\n";
	echo "<input type='hidden' name='proceed' value='1'>\n";
	echo "<input type='hidden' name='table' value='$table'>\n";
	foreach($rows as $row) {
		$field = $row[0];
		$label = $row[1];
		echo "<tr><td><label for='$field'>$label</label><td><input type='text' name='$field' id='$field'>\n";
	}
	echo "</table><button type='submit'>Save</button></form></div>\n";
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

function prep_add($table) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "prep_add($table)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	$table = checkstr($table);
	$tableexist = false;
	if ($table) {
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
		form_add($table);
	} else {
		throw new Exception("'table' value is not valid");
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

function exec_add($record) {
	global $pdo;
	$table = $record['table'];
	if (!checkTable($table)) {
		echo "<h1>checktable false</h1>\n";
		die;
	}
	$query = "insert into $table \n";
	$i = 0;
	$comma = ", ";
	$qv = "values(";
	$qf = "(";
	foreach($record as $key => $field) {
		$i++;
		if($i > 3) {
			if(count($record) == $i) {
				$comma = "";
			}
			$fields[] = [$key, $field];
			$qv .= ":$key" . $comma;
			$qf .= "`$key`" . $comma;
		}
	}
	$qv .= ")";
	$qf .= ") ";
	$query .= $qf . $qv . ";";
	$stmt = $pdo->prepare($query);
	foreach ($fields as $field) {
		$stmt->bindValue(":".$field[0], $field[1]);
	}
	$stmt->execute();
	echo "<h1>Запись сохранена</h1>\n";
	echo "<p>Вернуться на <a href='/'>главную</a>";
}

?>
