<?php
//Function: general handles actions
function prepareBPData($draftID) {
	$ret = [];
	global $memcObj, $sqlObj;
	$sql = $sqlObj->getDraftInfo($draftID);
	$sql = $sql[0];
	$mapInfo = $sqlObj->getMapInfo($sql["map"]);
	$mapInfo = $mapInfo[0];

	$roundData = [];
	$roundData["map"] = $mapInfo;
	$roundData["status"] = "LOBBY";
	$roundData["lobby"] = [
		"bothTeamsAreReady" => false,
		"expiresAt" => $sql["createStamp"] + 1000,
		"team1Ready" => false,
		"team2Ready" => false,
	];

	$roundData["currentDraft"]["currentPick"] = null;
	$roundData["currentDraft"]["currentPickIndex"] = null;
	$roundData["currentDraft"]["ruleSet"] = [
		"pickBanPattern" => getDraftStrategy(2),
		"timersPreset" => getTimerStrategy(1),
	];

	$teams = [
		[
			"name" => $sql["blueTeam"],
			"number" => 1,
			"availableTimePool" => $roundData["currentDraft"]["ruleSet"]["timersPreset"]["timePool"],
		],
		[
			"name" => $sql["redTeam"],
			"number" => 2,
			"availableTimePool" => $roundData["currentDraft"]["ruleSet"]["timersPreset"]["timePool"],
		],
	];

	$roundData["currentDraft"]["firstPickTeam"] = $teams[$_REQUEST['FirstHand']];

	$roundData["currentDraft"]["actions"] = [];
	foreach ($roundData["currentDraft"]["ruleSet"]["pickBanPattern"]["pattern"] as $key => $current) {
		$tmp = [];
		$tmp["confirmedAt"] = 0;
		$tmp["startedAt"] = 0;
		$tmp["updatedAt"] = 0;
		$tmp["type"] = $current[1];
		$tmp["index"] = $key;
		$tmp["isConfirmed"] = false;
		$tmp["picked"] = [];
		if ($current[0] == "first") {
			$tmp["team"] = $teams[$_REQUEST['FirstHand']];
		} else {
			$tmp["team"] = $teams[!$_REQUEST['FirstHand']];
		}
		//if allow chogall
		if (($tmp["type"] == "ban") || ($roundData["currentDraft"]["ruleSet"]["pickBanPattern"]["pattern"][$key + 1] == $current)) {
			$tmp["allowChogall"] = true;
		} else {
			$tmp["allowChogall"] = false;
		}
		$roundData["currentDraft"]["actions"][] = $tmp;
	}

	$ret["payload"] = [];
	$ret["payload"]["draftSession"] = [];
	$ret["payload"]["draftSession"]["version"] = 0;
	$ret["payload"]["draftSession"]["createdAt"] = $sql["createStamp"];
	$ret["payload"]["draftSession"]["id"] = $sql["id"];
	$ret["payload"]["draftSession"]["status"] = "IN_PROGRESS";
	$ret["payload"]["draftSession"]["teams"] = $teams;
	$ret["payload"]["draftSession"]["currentRound"] = $roundData;

	return $ret;
}

if ($_REQUEST['mapSelectType'] != 2) {
	$_REQUEST['mapId'] = -1;
} else {
	$_REQUEST['mapPools'] = -1;
}
$_REQUEST['weekLimit'] = "on"; //TODO:For Debug and non-finished status.
if (isset($_REQUEST['weekLimit'])) {
	$_REQUEST['weekLimit'] = 1;
} else {
	$_REQUEST['weekLimit'] = 0;
}
// echo $_REQUEST['game'];
// echo $_REQUEST['BlueTeam'];
// echo $_REQUEST['RedTeam'];
// echo $_REQUEST['mapSelectType'];
// echo $_REQUEST['mapId'];
// echo $_REQUEST['mapPools'];
// echo $_REQUEST['numBans'];
// echo $_REQUEST['FirstHand'];
// echo $_REQUEST['weekLimit'];
// echo "</br>";

if (trim($_REQUEST['BlueTeam']) == "" || trim($_REQUEST['RedTeam']) == "" || trim($_REQUEST['game']) == "") {
	echo "<div class=\"createContainer\"><span data-i18n=\"create.invalidInput\"></span></div>";
	include_once "./include/footer.inc.php";
	die();
}
$retArr = $sqlObj->createNewSession($_REQUEST['BlueTeam'], $_REQUEST['RedTeam'], $_REQUEST['game'], $_REQUEST['FirstHand'], $_REQUEST['numBans'], $_REQUEST['mapSelectType'], $_REQUEST['mapId'], $_REQUEST['mapPools'], $_REQUEST['weekLimit']);
print_r($retArr);
$draftID = $retArr['id'];
$createStamp = $retArr['createStamp'];
if (!$draftID) {
	echo "<div class=\"createContainer\"><span data-i18n=\"create.failedDatabase\"></span></div>";
	include_once "./include/footer.inc.php";
	die();
}
$baseURL = dirname("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . "/banpick.php?id=" . $draftID;
$blueKey = md5($_REQUEST['BlueTeam'] . $draftID . $createStamp . "b");
$redKey = md5($_REQUEST['RedTeam'] . $draftID . $createStamp . "r");
$blueURL = $baseURL . "&key=" . $blueKey;
$redURL = $baseURL . "&key=" . $redKey;
$memcObj->createChannel($draftID);
$memcObj->setClientKey($draftID, "blue", $blueKey);
$memcObj->setClientKey($draftID, "red", $redKey);
$sqlObj->setBPData($draftID, prepareBPData($draftID));
?>
<link rel="stylesheet" href="css/create.css" type="text/css" media="screen">
<div class="createContainer">
	<div class="FullWidthColumn">
		<span class="info" data-i18n="create.succeedTip"></span><br><br>
		<span class="info"><?php echo htmlentities($_REQUEST['game']) . "  BAN" . $_REQUEST['numBans'] ?></span>
		<br><br>
		<span class="info"><?php echo htmlentities($_REQUEST['BlueTeam']) . " : " ?></span><input type="text" name="tournament" class="form-control" value=<?php echo $blueURL; ?> readonly="readonly" autocomplete="off"/>
		<br>
		<span class="info"><?php echo htmlentities($_REQUEST['RedTeam']) . " : " ?></span><input type="text" name="tournament" class="form-control" value=<?php echo $redURL; ?> readonly="readonly" autocomplete="off"/>
		<br>
		<span class="info" data-i18n="create.observerLink"></span><a style="color:#00ffdd;" target="_blank" href="<?php echo $baseURL; ?>"><?php echo $baseURL; ?></a>
		<br>
	</div>
</div>