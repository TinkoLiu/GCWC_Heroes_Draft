<?php
$act = $_REQUEST['act'];
$key = $_REQUEST['key'];

//Include MySQL Class and initalize
if (!isset($sqlObj)) {
	include_once './include/mysql.inc.php';
	$sqlObj = new sqlHandle();
}
//Include Memcache Class and initalize
if (!isset($memcObj)) {
	include_once './include/memcache.inc.php';
	$memcObj = new memcacheHandle();
}
function broadcastAction($id, $data) {
	global $memcObj;
	$keys = "";
	$keys = implode(" ", $memcObj->getClientKey($id));
	$data = json_encode($data);
	$status = WSPushMsgs($keys, $data);
	if ($status != 0) {
		return "Failed with err code " . $status;
	}
	return "Pushed to " . $keys;
}
function channelBroadcast($data) {
	global $memcObj;
	$keys = $memcObj->getChannelInfo($data["payload"]["draftSession"]["id"]);
	$data = ["fetch" => 1];
	$data = json_encode($data);
	$status = WSPushMsgs($keys, $data);
	if ($status != 0) {
		return "Failed with err code " . $status;
	}
	return "Pushed to " . $keys;
}
?>