<?php

function server_info() {
	$server = [
		'mysql',
		'localhost',
		'root',
		'1',
		'utf8',
	];
	return $server;
}

function db_info() {
	$db = [
		'items',
		'utf8',
		'utf8_unicode_ci'
	];
	return $db;
}

function db_connect() {
	list($db) = db_info();
	return connect($db);
}

function connect($db = "") {
	list($type, $host, $user, $password, $charset) = server_info();
	$dsn = "{$type}:host={$host};charset={$charset}";
	if( $db != "" ) {
		$dsn .= ";dbname={$db}";
	}
	$conn = new PDO($dsn, $user, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	return $conn;
}

?>
