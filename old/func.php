<?php
require('db.php');

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
	echo "<div id='header_mouse_activity'>\n<div id='header_bg'></div>\n<div id='header'>\n";
	echo "\t<table class='menuitems'><tr>\n";
	echo "\t<th><a href='/'>Главная</a>\n";
	echo "\t<th><a href='/?show_deleted=1'>Удаленные</a>\n";
	$query = "select `table`,`label` from tables";
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, $debugfunc, $query, "debugQueries");
	//DEBUG END
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_NUM)) {
		echo "\t<th><a href='#" . $row[0]. "'>" . $row[1]. "</a>\n";
	}
	echo "\t<th><a href='/?debug=1'>DEBUG</a>\n";
	echo "\t</tr></table>\n";
    echo "</div>\n</div>\n\n";
    //echo '<div style="margin-top: 100px;">' . "\n";
}

function list_tables() {
	global $pdo;
	$q = "select `table` from `tables` where ( (not `table` like '*view*') and (not `table` like 'meta') and (not `table` like 'tables') ) order by `tableID`";
	$st = $pdo->prepare($q);
	$st->execute();
	$result = $st->fetchAll(PDO::FETCH_NUM);
	foreach($result as $record) {
		$t[] = $record[0];
	}
	$query = "show tables from inv";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$tables = $stmt->fetchAll(PDO::FETCH_NUM);
	$list = array();
	foreach($tables as $fields) {
		foreach($fields as $field) {
			if ( (!strstr($field,"view")) AND ($field != "meta") AND ($field != "tables") ) {
				$list[] = $field;
			}
		}
	}
	return $t;
}

function view() {
	foreach(list_tables() as $table) {
		show($table);
	}
	if( isset($_GET['debug']) and $_GET['debug']==1 ) {
		viewDebugInfo();
		viewDebugInfo('debugQueries');
	}
}

function show($table) {
	//DEBUG START
	$i=0;
	global $debugscript;
	$debugfunc = "show($table)";
	if (DEBUG) debugger(DEBUG_CALL, $debugscript . " : " . $debugfunc);
	try {
	if (!isset($table)) {
		throw new Exception('no argument "table" passed');
	}
	//DEBUG END
	global $pdo;
	if ( isset($_GET['show_deleted']) && $_GET['show_deleted'] == 1 ) {
		$suffix = "_deleted";
		$restore = "&restore=1";
		$delmsg = "Восстановить";
	} else {
		$suffix = "";
		$restore = "";
		$delmsg = "Удалить";
	}
	$query = "select * from `" . $table . "_view" . $suffix . "`";
	if (DEBUG) debugger(DEBUG_QUERY, "$query", "start", "debugQueries");
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	//DEBUG START
	if (DEBUG) debugger(DEBUG_QUERY, "", "end", "debugQueries");
	//DEBUG END
	echo "\n<div class='center'>\n";
	echo "<table id='$table' class='test1'>\n";
	$label = getTableLabel($table);
	echo "<caption><h3>$label</h3></caption>\n";
	echo "\t<tr>\n";
	if ($row) {
		foreach($row as $key => $field) {
			echo "\t\t<th>$key\n";
		}
	}
	if(DEBUG) {$debug = "&debug=1";} else {$debug = "";}
	echo "\t\t<th><a href='add.php?table=$table$debug'>[Добавить]</a>\n";
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
		echo "\t<tr>\n";
		foreach($row as $key => $field) {
			if($i==0) {
				$id = $key;
				$value = $field;
			}
			echo "\t\t<td>" . clean($field) . "\n";
			$i++;
		}
		echo "\t\t<td>\n";
		echo "\t\t<a href='edit.php?table=$table&id=$value$debug'>[Ред.]</a>\n";
		echo "\t\t<a href='del.php?table=$table&id=$value$debug$restore'>[$delmsg]</a>\n";
		$i = 0;
	}
	echo "</table>\n";
	echo "</div>\n\n";
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

function getTableByID($id) {
	global $pdo;
	$query = "select `table` from `meta` where `field` = :id and `primary` is true";
	$st = $pdo->prepare($query);
	$st->bindValue(':id', $id, PDO::PARAM_STR);
	$st->execute();
	if ($result = $st->fetch(PDO::FETCH_NUM)) {
		return $result[0];
	} else {
		return false;
	}
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

function nosql($var) {
	return PDO::quote($var);
}

function nohtml($var) {
	return htmlentities(strip_tags($var));
}

function clean($var) {
	$var = trim($var);
	//$var = str_replace(['"', "'", '\', '/', '$', '#', '&', '^', '%'],'',$var);
	//$var = htmlentities($var);
	$var = strip_tags($var);
	//$var = filter_var($var, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	//$var = preg_replace('/[^ -_0-9A-Za-zА-Яа-я]+/u','',$var);
	$var = preg_replace('/[^(). 0-9\p{Cyrillic}\p{Latin}]+/u','',$var);
	return $var;
}

function getLabelByField($table, $field) {
	global $pdo;
	$query = "select `label` from `meta` where `table` = :table and `field`= :field";
	$st = $pdo->prepare($query);
	$st->bindValue(':table', $table, PDO::PARAM_STR);
	$st->bindValue(':field', $field, PDO::PARAM_STR);
	$st->execute();
	if ($result = $st->fetch(PDO::FETCH_NUM)) {
		return $result[0];
	} else {
		return false;
	}
}

function lookupvalues($field) {
	global $pdo;
	$query = "select * from {$field}_lookup";
	$st = $pdo->prepare($query);
	$st->execute();
	if ( $result = $st->fetchAll(PDO::FETCH_NUM) ) {
		return $result;
	} else {
		return false;
	}
}

function lookupvalues2($table, $field) {
	global $pdo;
	$query = "select `lookup` from `meta` where `table`=:table and `field`=:field and not `lookup` = ''";
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':table', $table, PDO::PARAM_STR);
	$stmt->bindValue(':field', $field, PDO::PARAM_STR);
	$stmt->execute();
	if($row = $stmt->fetch(PDO::FETCH_NUM)) {
		$lfield = $row[0];
		$lid = $field;
		$ltable = getTableByID($lid);
		$lquery = "select $lid, $lfield from $ltable order by $lfield";		$st = $pdo->prepare($lquery);
		$st->execute();
		$result = $st->fetchAll(PDO::FETCH_NUM);		return $result;
	} else {
		return false;
	}
}

function drawselectbox($values, $name, $current = null) {
	echo "<select name='$name'>\n";
	if($current == null) {
		echo "\t<option selected value=''>Выберите значение</option>\n";
	} else {
		$i = 1;
	}
	foreach($values as $row) {
		$id = $row[0];
		$value = $row[1];
		if($id == $current) {
			$selected = " selected";
			$i++;
		} else {
			$selected = "";
		}
		echo "\t<option$selected value='$id'>$value</option>\n";
	}
	if($i != 2) {
		echo "\t<option selected value=''>Выберите значение</option>\n";
	}
	echo "</select>\n";
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

function d($title, $var) {
	echo "<h3><pre>$title\n";
	var_dump($var);
	echo "</pre></h1>\n";
}

?>
