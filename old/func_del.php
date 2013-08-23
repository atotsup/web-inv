<?php

function del($request) {
	try {

	if ( isset($request['table']) && isset($request['proceed']) && checkint($request['proceed']) == 1 ) {
		exec_del($request);
	} elseif (isset($request['table']) && isset($request['id']) && ctype_lower($request['table']) && ctype_digit($request['id'])) {
		prep_del($request);
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

function prep_del($request) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "prep_del(request)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($request)) {
		throw new Exception('no arguments "table" or "id" passed');
	}
	//DEBUG END
	$table = $request['table'];
	$id = $request['id'];
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
		form_del($request);
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

function form_del($request) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "form_del(request)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($request)) {
		throw new Exception('no arguments "table" or "value" passed');
	}
	//DEBUG END
	global $pdo;
	$table = $request['table'];
	$value = $request['id'];
	$id = getIDname($table);
	$query = "select * from $table where $id = :$id";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
	//DEBUG END
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(":$id", $value, PDO::PARAM_INT);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo "<div class='center'><table class='test1'><tr>\n";
	if( isset($request['restore']) && $request['restore'] == 1 ) {
		$delmsg = "Восстановить";
		$restore = "&restore=1";
	} else {
		$delmsg = "Удалить";
		$restore = "";
	}
	foreach($row as $key => $field) {
		echo "<tr><td>$key<td>$field\n";
	}
	echo "</table><h1><a href='del.php?proceed=1$restore&table=$table&$id=$value'>$delmsg</a></h1></div>\n";
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

function exec_del($record) {
	global $pdo;
	$table = $record['table'];
	if (!checkTable($table)) {
		echo "<h1>checktable false</h1>\n";
		die;
	}
	if( isset($record['restore']) && $record['restore']==1 ) {
		$delval = 0;
		$delmsg = "Запись восстановлена";
	} else {
		$delval = 1;
		$delmsg = "Запись удалена";
	}
	$query = "update $table set deleted = $delval\n";
	$i = 0;
	foreach($record as $key => $field) {
		if( $key != "proceed" and $key != "restore" and $key != "debug" and $key != "table") {
			if($i == 0) {
				$fieldid = $key;
				$id = $field;
			}
			$fields[] = [$key, $field];
			$i++;
		}
	}
	$query = $query . " where $fieldid = :$fieldid";
	$stmt = $pdo->prepare($query);
	foreach ($fields as $field) {
		$stmt->bindValue(":".$field[0], $field[1]);
	}
	$stmt->execute();
	makemenu();
	echo "<h1>$delmsg</h1>\n";
	view();
}

?>
