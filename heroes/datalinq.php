<?php
include_once './include/mysql.inc.php';
$sqlObj = new sqlHandle();
//Include MemCache Class and initalize
include_once './include/memcache.inc.php';
$memcObj = new memcacheHandle();
if (isset($_REQUEST["id"])) {
	# code...
}
header("Content-type: text/xml");

?>