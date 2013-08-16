<?php
require('db.php');

define('CT_INT', 'int(11)');
define('CT_TEXT', 'text');
define('CT_DATE', 'date');

if (isset($_GET['debug']) && ctype_digit($_GET['debug']) && ($_GET['debug'] == 1)) {
	$debugscript = "func.php";
	define ('DEBUG', true);
	define ('DEBUG_CALL', 0);
	define ('DEBUG_ERROR', 1);
	define ('DEBUG_INFO', 2);
	define ('DEBUG_QUERY', 3);
	$debugInfo = array();
	$debugQueries = array();
} else {
	define ('DEBUG', false);
}

function makemenu() {
	//DEBUG START
	global $debugscript;
	$debugfunc = "makemenu()";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	//DEBUG END
	global $pdo;
	echo '<div id="header_mouse_activity"><div id="header_bg"></div><div id="header">';
	echo "	\t<table class='menuitems'><tr>\n";
	echo "	\t<th><a href='/'>Главная</a>\n";
	$query = "select `table`,`label` from tables";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, $debugfunc, $query, "debugQueries");
	//DEBUG END
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_NUM)) {
		echo "	\t<th><a href='#" . $row[0]. "'>" . $row[1]. "</a>\n";
	}
	echo "	\t</tr></table>\n";
    echo "</div></div>\n\n";
    echo '<div style="margin-top: 100px;">' . "\n";
}

function view($table) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "view($table)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	global $pdo;
	if (isset($_GET['show_deleted']) && $_GET['show_deleted'] == 1) {
		$suffix = "_deleted";
	} else {
		$suffix = "";
	}
	$query = "select * from `" . $table . "_view" . $suffix . "`";
	if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "", "end", "debugQueries");
	//DEBUG END
	echo "<div class='center'>\n";
	echo "<table id='$table' class='test1'>\n";
	$label = getTableLabel($table);
	echo "<caption><h3>$label</h3></caption>";
	echo "<tr>\n";
	foreach($row as $key => $field) {
		echo "<th>$key\n";
	}
	if(DEBUG) {$debug = "&debug=1";} else {$debug = "";}
	echo "<th><a href='add.php?table=$table$debug'>[Добавить]</a>\n";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
	//DEBUG END
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "", "end", "debugQueries");
	//DEBUG END
	$i = 0;
	foreach($rows as $row) {
		echo "<tr>\n";
		foreach($row as $key => $field) {
			if($i==0) {
				$id = $key;
				$value = $field;
			}
			echo "<td>$field\n";
			$i++;
		}
		echo "<td>\n";
		echo "<a href='edit.php?table=$table&id=$value$debug'>[Ред.]</a>\n";
		echo "<a href='del.php?table=$table&id=$value$debug'>[Удалить]</a>\n";
		$i = 0;
	}
	echo "</table>\n";
	echo "</div>\n";
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
	echo "<div class='center'><form action='edit.php' method='get'><table class='test1'><tr>\n";
	echo "<input type='hidden' name='proceed' value='1'>\n";
	echo "<input type='hidden' name='table' value='$table'>\n";
	foreach($row as $key => $field) {
		echo "<tr><td>$key<td><input type='text' name='$key' value='$field'>\n";
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
	$query = "select * from $table limit 1";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
	//DEBUG END
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	echo "<div class='center'><form action='add.php'><table class='test1'><tr>\n";
	foreach($row as $key => $field) {
		echo "<tr><td>$key<td><input type='text'>\n";
	}
	echo "</table></form></div>\n";
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

function form_del($table, $value) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "form_del($table, $value)";
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
	echo "<div class='center'><table class='test1'><tr>\n";
	foreach($row as $key => $field) {
		echo "<tr><td>$key<td>$field\n";
	}
	echo "</table><a href='del.php?proceed=1&table=$table&$id=$value'>Удалить</a></div>\n";
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

function checkstr($var) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "checkstr($var)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($var)) {
		throw new Exception('no argument "var" passed');
	}
	//DEBUG END
	if (ctype_alpha($var)) {
		return strval($var);
	} else {
		return false;
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

function checkint($var) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "checkint($var)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($var)) {
		throw new Exception('no argument "var" passed');
	}
	//DEBUG END
	if (ctype_digit($var)) {
		return intval($var);
	} else {
		return false;
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

function edit($table, $id) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "edit($table, $id)";
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

function add($table) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "add($table)";
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

function del($table, $id) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "del($table, $id)";
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
		form_del($table, $id);
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

function checkTable($table) {
	global $pdo;
	global $debugscript;
	$debugfunc = "checkTable($table)";
	$query = "select `table` from `tables` where `table` = :table";
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':table', $table, PDO::PARAM_STR);
	$stmt->execute();
	if ($stmt->fetch()) {
		$check = true;
	} else {
		$check = false;
	}
	return $check;
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
	echo "<pre>";
	foreach ($fields as $field) {
		echo "$field[0], $field[1]\n";
		$stmt->bindValue(":".$field[0], $field[1]);
	}
	$stmt->execute();
	echo $query;
	echo "</pre>\n";
	echo "<p>Вернуться на <a href='/'>главную</a>";
}

function exec_del($record) {
	global $pdo;
	$table = $record['table'];
	if (!checkTable($table)) {
		echo "<h1>checktable false</h1>\n";
		die;
	}
	$query = "update $table set deleted = 1\n";
	$i = 0;
	foreach($record as $key => $field) {
		$i++;
		if($i > 2) {
			if($i == 3) {
				$fieldid = $key;
				$id = $field;
			}
			$fields[] = [$key, $field];
		}
	}
	$query = $query . " where $fieldid = :$fieldid";
	$stmt = $pdo->prepare($query);
	echo "<pre>";
	foreach ($fields as $field) {
		echo "$field[0], $field[1]\n";
		$stmt->bindValue(":".$field[0], $field[1]);
	}
	$stmt->execute();
	echo $query;
	echo "</pre>\n";
	echo "<p>Вернуться на <a href='/'>главную</a>";
}

function getTableLabel($table) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "getTableLabel($table)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	global $pdo;
	$query = "select tables.label from tables where tables.table = :table";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, $debugfunc, $query, "debugQueries");
	//DEBUG END
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':table', $table, PDO::PARAM_STR);
	$stmt->execute();
	if($row = $stmt->fetch(PDO::FETCH_NUM)) {
		$label = $row[0];
	} else {
		$label = $table;
	}
	return $label;
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

function getIDname($table) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "getIDname($table)";
	if (DEBUG) debugger(0, $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	return substr($table, 0, -1) . "ID";
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

function createtable($table) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "createtable(table)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	global $pdo;
	$i = 0;
	$tname = $table[0];
	$tlabel = $table[1];
	$fields = $table[2];
	$querycreatetable_middle = "";
	$queryupdatemeta_middle = "";
	$querycreatetable_start = "CREATE TABLE IF NOT EXISTS `" . $tname . "` (\n";
	foreach($fields as $field) {
		$fname = $field[0];
		$ftype = $field[1];
		$fpkey = $field[2];
		$ffkey = $field[3];
		$flabel = $field[4];
		$fvisible = $field[5];
		$flookup = $field[6];
		$forder = $field[7];
		if($fpkey == 1) {
			$pkey = $fname;
			$autoincr = "AUTO_INCREMENT";
			$comment = "";
		} else {
			$autoincr = "";
			$comment = "COMMENT '" . $flabel . "'";
		}
		$querycreatetable_middle = $querycreatetable_middle . "  `" . $fname . "` " . $ftype . " NOT NULL " . $autoincr . $comment . ",\n";
		$queryupdatemeta_middle = $queryupdatemeta_middle . "  ('" . $tname . "', '" . $fname . "', " . $fpkey . ", " . $ffkey . ", '" . $flabel . "', " . $fvisible . ", '" . $flookup . "', " . $forder . ")";
		$i++;
		if ($i != count($fields)) {
			$queryupdatemeta_middle = $queryupdatemeta_middle . ",\n";
		}
	}
	$querycreatetable_end = "  PRIMARY KEY (`" . $pkey . "`)\n) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='" . $tlabel . "';";
	$querycreatetable = $querycreatetable_start . $querycreatetable_middle . $querycreatetable_end;
	
	$queryupdatemeta_start = "insert into `meta` (`table`, `field`, `primary`, `index`, `label`, `visible`, `lookup`, `order`) values \n";
	$queryupdatemeta_end = ";";
	$queryupdatemeta = $queryupdatemeta_start . $queryupdatemeta_middle . $queryupdatemeta_end;

	$queryupdatetables = "insert into `tables` (`table`, `label`) values \n  ('" . $tname . "', '" . $tlabel . "');";
	/*echo "<h3>table</h3><pre>\n";
	print_r($table);
	echo "\n\n</pre>";*/
	echo "<h3>querycreatetable</h3><pre>\n$querycreatetable\n\n</pre>";
	echo "<h3>queryupdatemeta</h3><pre>\n$queryupdatemeta\n\n</pre>";
	echo "<h3>queryupdatetables</h3><pre>\n$queryupdatetables\n\n</pre>";
	
	//*
	$stmt = $pdo->prepare($querycreatetable);
	$stmt->execute();
	$stmt = $pdo->prepare($queryupdatemeta);
	$stmt->execute();
	$stmt = $pdo->prepare($queryupdatetables);
	$stmt->execute();
	//*/
	
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

function createview($table, $test = false) {
	//DEBUG START
	global $debugscript;
	$debugfunc = "createview(table)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	global $pdo;
	$i = 0;
	$tname = $table[0];
	$tjoins = $table[1];
	$torder = $table[2];
	$fields = $table[3];
	$querycreateview_create = "CREATE OR REPLACE VIEW `" . $tname;
	$querycreateview_create1 = $querycreateview_create . "_view`";
	$querycreateview_create2 = $querycreateview_create . "_view_deleted`";
	$querycreateview_select = " AS SELECT \n";
	foreach($fields as $field) {
		$fname = $field[0];
		$flabel = $field[1];
		$querycreateview_select .= "  `" . $fname . "` AS `" . $flabel . "`";
		$i++;
		if ($i != count($fields)) {
			$querycreateview_select .= ",\n";
		}
	}
	if ($tjoins == null) {
		$querycreateview_from = " \n" . 'FROM `' . $tname . "`";
	} else {
		$querycreateview_from = " \n" . 'FROM { oj `' . $tname . "`";
		foreach($tjoins as $tjoin) {
			$querycreateview_from .= "\n natural left join `" . $tjoin . "`";
		}
		$querycreateview_from .= '}';
	}
	$querycreateview_where = " \nWHERE deleted = ";
	$querycreateview_where1 = '0';
	$querycreateview_where2 = '1';
	$querycreateview_order = " \nORDER BY " . $torder . ';';
	$querycreateview1 = $querycreateview_create1 . $querycreateview_select . $querycreateview_from . $querycreateview_where . $querycreateview_where1 . $querycreateview_order;
	$querycreateview2 = $querycreateview_create2 . $querycreateview_select . $querycreateview_from . $querycreateview_where . $querycreateview_where2 . $querycreateview_order;
	
	/*echo "<h3>table</h3><pre>\n";
	print_r($table);
	echo "\n\n</pre>";*/
	echo "<h3>querycreateview1</h3><pre>\n$querycreateview1\n\n</pre>";
	echo "<h3>querycreateview2</h3><pre>\n$querycreateview2\n\n</pre>";
	
	if (!$test) {
		$stmt = $pdo->prepare($querycreateview1);
		$stmt->execute();
		$stmt = $pdo->prepare($querycreateview2);
		$stmt->execute();
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

function debugger($type, $function, $comment = "", $array = 'debugInfo') {
	global $$array;
	$debug =& $$array;
	$debug[] = [microtime(true), $type, $function, $comment];
}

function viewDebugInfo($array = 'debugInfo') {
	global $$array;
	$debug =& $$array;
	echo "<div class='center'>\n";
	echo "<table class='test1'>\n";
	echo "<caption><h3>Debug info</h3></caption>\n";
	echo "<tr>\n";
	echo "<th>Time\n";
	echo "<th>Type\n";
	echo "<th>Function\n";
	echo "<th>Comment\n";
	$prevtime = $debug[0][0] * 1000;
	$starttime = $debug[0][0] * 1000;
	$endtime = end($debug)[0] * 1000;
	$totaltime_round = round($endtime - $starttime, 4);
	$totaltime = $endtime - $starttime;
	foreach($debug as $row) {
		echo "<tr>\n";
		if ($row[1] == 1) {
			$class = " class='error'";
			$type = "error";
		} elseif ($row[1] == 0) {
			$class = "";
			$type = "call";
		} elseif ($row[1] == 2) {
			$class = "";
			$type = "info";
		} elseif ($row[1] == 3) {
			$class = "";
			$type = "query";
		}
		$currtime = $row[0] * 1000;
		$timediff_round = round($currtime - $prevtime, 2);
		$timediff = $currtime - $prevtime;
		if($totaltime != 0) {
			$percent = round($timediff * 100 / $totaltime, 1);
		} else {
			$percent = 100;
		}
		echo "<td$class style='font-size:".min(max((7+round($percent,0)*2),8),40)."px'>$percent % ($timediff_round ms)\n";
		echo "<td$class>$type\n";
		echo "<td$class>$row[2]\n";
		echo "<td$class>$row[3]\n";
		$prevtime = $row[0] * 1000;
	}
	echo "<tr>\n";
	echo "<td class='bold'>Total time - ".round($totaltime_round,2)." ms\n";
	echo "<td>\n";
	echo "<td>\n";
	echo "<td>\n";
	echo "</table>\n";
	echo "</div>\n\n";
	$debug = null;
}

function showError() {
	$msg = [
		"Вы сделали что-то не так :)",
		"На поле танки грохотали...",
		"Солдат вели в последний бой...",
		"Как у леса на опушке...",
		"А у вас молоко убежало...",
		"Есть только миг...",
		"Ошибка выполнила недопустимую операцию и будет исправлена...",
		"Освободиться от забот? Освободиться от свободы...",
		"We all live in the yellow submarine...",
		"А где мои семнадцать лет...",
	];
	echo "<div class='center'>\n";
	echo "<h1>" . $msg[rand(0,count($msg)-1)] . "</h1>\n";
	echo "<p>Попробуйте начать <a href='/'>сначала</a>";
	echo "</div>\n";
}

?>
