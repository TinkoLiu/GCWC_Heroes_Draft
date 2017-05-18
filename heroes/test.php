<?php
if (!isset($_GET["tktest"])) {
	die("Go away, guys.");
}
include_once './include/resourcesController.php';
print_r(resPreloader($_GET["tktest"]));
?>