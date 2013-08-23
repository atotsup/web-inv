<?php
require_once('db.php');

define('CT_BOOL', 'tinyint(1)');
define('CT_INT', 'int(11)');
define('CT_TEXT', 'text');
define('CT_DATE', 'date');

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
		$querycreateview_select .= "  " . $fname . " AS `" . $flabel . "`";
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
			$id = getIDname($tjoin);
			$querycreateview_from .= "\n natural join `" . $tjoin . "` on `$tname`.`$id`=`$tjoin`.`$id`";
		}
		$querycreateview_from .= "\n" . '}';
	}
	$querycreateview_where = " \nWHERE `$tname`.`deleted` = ";
	$querycreateview_where1 = '0';
	$querycreateview_where2 = '1';
	$querycreateview_order = " \nORDER BY " . $torder . ';';
	$querycreateview1 = $querycreateview_create1 . $querycreateview_select . $querycreateview_from . $querycreateview_where . $querycreateview_where1 . $querycreateview_order;
	$querycreateview2 = $querycreateview_create2 . $querycreateview_select . $querycreateview_from . $querycreateview_where . $querycreateview_where2 . $querycreateview_order;
	
	echo "<h1>$table[0]</h1>\n";
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

function create_views() {
	global $pdo;
	$mass = array();
	$querytables = "select `table`, `joins` from `tables`";
	$stmt = $pdo->prepare($querytables);
	$stmt->execute();
	$records = $stmt->fetchAll(PDO::FETCH_NUM);
	foreach($records as $record) {
		$one = array();
		$two = "";
		$lookup_or_field = "";
		$four = "";
		$three = array();
		$table = $record[0];
		if ($record[1] != "") {
			$joins = explode(",", $record[1]);
		} else {
			$joins = null;
		}
		$one[] = $table;
		$one[] = $joins;
		$querymeta = "select `field`, `label`, `lookup`, `order` from `meta` where `table` = :table order by `order`";
		$stmt2 = $pdo->prepare($querymeta);
		$stmt2->bindValue(':table', $table);
		$stmt2->execute();
		$result = $stmt2->fetchAll(PDO::FETCH_NUM);
		$i = 0;
		$comma = "";
		foreach($result as $meta) {
			$field = $meta[0];
			$label = $meta[1];
			$lookup = $meta[2];
			$order = $meta[3];
			//$two .= "`" . $lookup . "`, ";
			if ($lookup == "") {
				$lookup_or_field = "`" . $field . "`";
			} else {
				$lookup_mod = preg_replace('/([a-z]+)/', '`$1`', $lookup);
				$lookup_or_field = "concat_ws(',', " . $lookup_mod . ") ";
			}
			$three[] = [$lookup_or_field, $label];
			if( $order > 0 ) {
				if( $i > 1 ) {
					$comma = ", ";
				}
				$four .= "$comma$lookup_or_field";
			}
			$i++;
		}
		$one[] = $four;
		$one[] = $three;
		$mass[] = $one;
	}
	foreach($mass as $table) {
		createview($table);
	}
}

function create_lookup_views() {
	global $debugscript;
	$debugfunc = "create_lookup_views()";
	if (DEBUG) debugger(0, $debugfunc);
	try {
		global $pdo;
		$q1 = "select `field`, `lookup` from `meta` where not `lookup` = ''";
		$st1 = $pdo->prepare($q1);
		$st1->execute();
		if ($rows1 = $st1->fetchAll(PDO::FETCH_NUM)) {
			foreach($rows1 as $row1) {
				$field = $row1[0];
				$lookup = $row1[1];
				$lookup_mod = preg_replace('/([a-z]+)/', '`$1`', $lookup);
				$table = getTableByID($row1[0]);
				$id = getIDname($table);
				$q2 = "select `joins` from `tables` where `table` = :table";
				$st2 = $pdo->prepare($q2);
				$st2->bindValue(':table', $table, PDO::PARAM_STR);
				$st2->execute();
				if($rows2 = $st2->fetch(PDO::FETCH_NUM)) {
					$joins = explode(',', $rows2[0]);
				} else {
					throw new Exception("no results from query: $q2");
				}
				$q3 = "create or replace view `{$field}_lookup` as \nselect `$id`, concat_ws(' ', {$lookup_mod}) as `Value` from ";
				if( $joins[0] != "" ) {
					$q3 .= "{ oj `$table` \n";
					foreach($joins as $join) {
						$joinid = getIDname($join);
						$q3 .= "left join `$join` on `$table`.`$joinid` = `$join`.`$joinid` \n";
					}
					$q3 .= "} ";
				} else {
					$q3 .= "`{$table}`";
				}
				//d('create_lookup_views: lookup: ', $lookup);
				//d('create_lookup_views: lookup_mod: ', $lookup_mod);
				$q3 .= " order by {$lookup_mod}";
				d("table = $table; \nfield = $field; \nlookup = $lookup; \nq3=", $q3);
				$st3 = $pdo->prepare($q3);
				$st3->execute();
				//d("field", $field);
				//d("lookup", $lookup);
				//d("table", $table);
				d("joins", $joins);
			}
		} else {
			throw new Exception("no results from query: $q1");
			return false;
		}
	} catch (Exception $e) {
		if (DEBUG) {
			debugger(DEBUG_ERROR, $e->getFile() . ":" . $e->getLine() . ":" . $debugfunc, $e->getMessage());
			viewDebugInfo();
			die();
		} else showError();
	}
}

function add_deleted($table) {
	global $pdo;
	$query = "alter table $table add deleted tinyint(1) default 0 comment 'Удалено';";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
}

?>
