<?php
include_once './include/ajax.inc.php';
header('Content-type: text/json');
// $retJson = array (
// "type"	=> $_GET['type'],
// "id"	=> $_GET['id']
// );
if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
	switch ($action) {

	case 'draft-session.join':
		if (isset($_REQUEST['channelName'])) {
			echo joinDraftChannel($_REQUEST['channelName'], $_REQUEST['accessKey']);
		}
		return;

	case 'fetch':
		echo fetchActions();
		return;

	case 'ready':
		echo setClientReady();
		return;

	case 'confirm-pick':
		echo confirmPick();
		return;

	case 'confirm-position':
		echo confirmPosition();
		return;

	case 'pre-select':
		echo preSelect();
		return;

	case 'get-player':
		echo getTeamPlayer();
		return;

	default:
		# code...
		break;
	}
}
//echo json_encode($retJson);
?>