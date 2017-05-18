<?php
include_once "./include/header.inc.php";

//Request MySQL Support & initalize it
include_once './include/mysql.inc.php';
$sqlObj = new sqlHandle();
//Include MemCache Class and initalize
include_once './include/memcache.inc.php';
$memcObj = new memcacheHandle();

include_once "./include/go.inc.php";
//Resource Preloader
resPreloader('blueBtn');
include_once "./include/footer.inc.php";
?>