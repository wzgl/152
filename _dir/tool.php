<?php
error_reporting(E_ALL);
echo 111;

include_once("/var/www/html/_dir/db.php");
echo 223;
global $DB;
echo 222;
$sql = "select * from TUpdateFile";
$a = $DB->getFetchAll($sql);
var_dump($a);
echo 111;
?>