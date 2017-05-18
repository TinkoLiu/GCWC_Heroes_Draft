<?php
include_once "./include/header.inc.php";

//Request MySQL Support & initalize it
include_once './include/mysql.inc.php';
$sqlObj = new sqlHandle();
//Include MemCache Class and initalize
include_once './include/memcache.inc.php';
$memcObj = new memcacheHandle();

include_once './include/judge.inc.php';

//Detecting page type
if (isset($_REQUEST['createRequest']) && $_REQUEST['createRequest'] == 'on') {
	include_once './include/create.result.gcwc.php';
} else {
	include_once "./include/create.gcwc.inc.php";
}

//Resource Preloader
resPreloader('blueBtn');

include_once "./include/footer.inc.php";
?>