<?php

$dbtype = "mysql";
$host = "localhost";
$user = "root";
$password = "1";
$dbname = "inv";
$charset = "utf8";

$pdo = new PDO("$dbtype:host=$host;dbname=$dbname;charset=$charset", $user, $password);

?>
