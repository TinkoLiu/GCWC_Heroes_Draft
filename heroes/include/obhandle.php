<?php
//Once observer side document.ready
//will access this by ajax
//http://[host]/sessionController.php?act=ob&id=[draftID]
$draftID = $_REQUEST['id'];
if ($draftID == "") {
	die("Undetermined draft ID.");
}
echo $draftID;
?>