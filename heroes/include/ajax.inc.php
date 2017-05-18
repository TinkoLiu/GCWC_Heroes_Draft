<?php
//Include MySQL Class and initalize
include_once './include/mysql.inc.php';
$sqlObj = new sqlHandle();
//Include MemCache Class and initalize
include_once './include/memcache.inc.php';
$memcObj = new memcacheHandle();

include_once './include/judge.inc.php';

function getTeamPlayer() {
	global $sqlObj;
	$teamID = $_REQUEST["team"];
	$players = $sqlObj->getPlayersByTeam($teamID);
	return json_encode($players);
}

function getServerInfo() {
	$lastHeroCode = end(getHeroes());
	$lastHeroCode = $lastHeroCode["codename"];
	$ret = [
		"ServerVersion" => "Gold Club World Championship 1.0.0.0",
		"DraftCoreVersion" => "1.2.0.0",
		"LastHeroCode" => $lastHeroCode,
	];
}

//Function: join draft channel
//Argument(s):
//	channelName:int
//			draft id
//Return:json
function joinDraftChannel($channelName, $accessKey) {
	global $sqlObj, $memcObj;
	return $memcObj->addKeyToChannel($channelName, $accessKey);
}

function detectClient() {
	global $memcObj;
	$keys = $memcObj->getClientKey($_REQUEST["id"]);
	switch ($_REQUEST["accessKey"]) {
	case $keys[0]:
		$memcObj->setClientHeartbeatTime($_REQUEST["id"], "blue", time());
		return 1;
		break;

	case $keys[1]:
		$memcObj->setClientHeartbeatTime($_REQUEST["id"], "red", time());
		return 2;
		break;

	default:

		break;
	}
	return "observer";
}
//Function: general handles actions
function fetchActions() {
	global $memcObj, $sqlObj;
	$ret = $sqlObj->getBPData($_REQUEST["id"]);
	$clientData = [];
	switch (detectClient()) {
	case 1:
		$clientData["type"] = "team";
		$clientData["team"] = $ret["payload"]["draftSession"]["teams"][0];
		break;

	case 2:
		$clientData["type"] = "team";
		$clientData["team"] = $ret["payload"]["draftSession"]["teams"][1];
		break;

	default:
		$clientData["type"] = "observer";
		break;
	}
	$ret["payload"]["clientData"] = $clientData;
	$ret["payload"]["draftSession"]["teams"][0]["lastUpdate"] = $memcObj->getClientHeartbeatTime($_REQUEST["id"], "blue");
	$ret["payload"]["draftSession"]["teams"][1]["lastUpdate"] = $memcObj->getClientHeartbeatTime($_REQUEST["id"], "red");
	$ret = draftSession($ret);
	return json_encode($ret);
}

function setClientReady() {
	global $memcObj, $sqlObj;
	$ret = $sqlObj->getBPData($_REQUEST["id"]);
	$status = ["status" => 0];
	switch (detectClient()) {
	case 1:
		$ret["payload"]["draftSession"]["currentRound"]["lobby"]["team1Ready"] = true;
		$status["status"] = 1;
		break;

	case 2:
		$ret["payload"]["draftSession"]["currentRound"]["lobby"]["team2Ready"] = true;
		$status["status"] = 1;
		break;

	default:

		break;
	}
	if ($ret["payload"]["draftSession"]["currentRound"]["lobby"]["team2Ready"] && $ret["payload"]["draftSession"]["currentRound"]["lobby"]["team1Ready"]) {
		$ret["payload"]["draftSession"]["currentRound"]["lobby"]["bothTeamsAreReady"] = true;
		$ret["payload"]["draftSession"]["currentRound"]["status"] = "DRAFT";
	}
	updateBPData($ret);
	return json_encode($status);
}

function confirmPick() {
	global $memcObj, $sqlObj;
	$ret = $sqlObj->getBPData($_REQUEST["id"]);
	$currentPick = $ret["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"];
	$currentPickIndex = $ret["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPickIndex"];
	$status = ["status" => 0];
	if (!(isset($_REQUEST["confirmID"]) && isset($_REQUEST["confirmCode"]))) {
		$status["status"] = "No hero selected! Invalid Request!";
		return json_encode($status);
	}
	$confirmHeroID = $_REQUEST["confirmID"];
	$confirmHeroCode = $_REQUEST["confirmCode"];
	//now will check if this hero is selected
	if ($currentPickIndex != 0) {
		foreach ($ret["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"] as $key => $currAct) {
			if (!$currAct["startedAt"] || !$currAct["isConfirmed"]) {
				break;
			}
			if ($currAct["picked"]["hero-code"] == $_REQUEST["confirmCode"]) {
				$status["status"] = "Seems you double-clicked the confirm button!";
				return json_encode($status);
			}
		}
	}
	$requestFromTeam = detectClient();
	//Judge if request is from valid client
	if (!$requestFromTeam) {
		$status["status"] = "Invalid Client";
		return json_encode($status);
	} elseif ($currentPick["team"]["number"] != $requestFromTeam) {
		$status["status"] = "Not your turn";
		return json_encode($status);
	}
	//check if this turn allows chogall
	if ($confirmHeroCode == "cho" && $confirmHeroID == 45 && !$currentPick["allowChogall"]) {
		$status["status"] = "This turn doesn't allow chogall.";
		return json_encode($status);
	}
	//ok, write picks to currentPick array.
	$currentPick["confirmedAt"] = time();
	$currentPick["isConfirmed"] = true;
	$currentPick["picked"] = [
		"hero-code" => $confirmHeroCode,
		"hero-id" => $confirmHeroID,
	];
	$currentPick["updatedAt"] = $currentPick["confirmedAt"];
	$ret["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"] = $currentPick;
	$status["status"] = 1;
	updateBPData($ret);
	return json_encode($status);
}

function preSelect() {
	global $memcObj, $sqlObj;
	$ret = $sqlObj->getBPData($_REQUEST["id"]);
	$currentPick = $ret["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"];
	$preSeleceID = $_REQUEST["preSeleceID"];
	$preSeleceCode = $_REQUEST["preSeleceCode"];
	$status = ["status" => 0];
	$requestFromTeam = detectClient();
	//Judge if request is from valid client
	if (!$requestFromTeam) {
		$status["status"] = "Invalid Client";
		return json_encode($status);
	} elseif ($currentPick["team"]["number"] != $requestFromTeam) {
		$status["status"] = "Not your turn";
		return json_encode($status);
	}
	//ok, write picks to currentPick array.
	$currentPick["picked"] = [
		"hero-code" => $preSeleceCode,
		"hero-id" => $preSeleceID,
	];
	$currentPick["updatedAt"] = time();
	$ret["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"] = $currentPick;
	$status["status"] = 1;
	updateBPData($ret);
	return json_encode($status);
}

function confirmPosition() {
	global $memcObj, $sqlObj;
	$ret = $sqlObj->getBPData($_REQUEST["id"]);
	$status = ["status" => 0];
	$requestFromTeam = detectClient();
	if (!$requestFromTeam) {
		$status["status"] = "Invalid Client";
		return json_encode($status);
	}
	$playerPos = json_decode($_REQUEST["playerPos"], true);
	$teamID = $requestFromTeam - 1;
	$ret["payload"]["draftSession"]["teams"][$teamID]["picked"] = $playerPos;
	$ret["payload"]["draftSession"]["teams"][$teamID]["positionSet"] = true;
	$status["status"] = 1;
	updateBPData($ret);
	return json_encode($status);
}
?>