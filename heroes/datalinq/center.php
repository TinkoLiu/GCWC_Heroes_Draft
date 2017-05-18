<?php
include_once '../include/mysql.inc.php';
$sqlObj = new sqlHandle();
//Include MemCache Class and initalize
include_once '../include/memcache.inc.php';
$memcObj = new memcacheHandle();
if (isset($_REQUEST["id"])) {
	$bpdata = $sqlObj->getBPData($_REQUEST["id"]);
} else {
	$bpdata = $sqlObj->getLastBPData();
}
$bpdata = $bpdata["payload"]["draftSession"];
//print_r($bpdata);

$i18n = file_get_contents('../resources/localization/en-US/translation.json');
$i18n = json_decode($i18n, true);
$i18n = $i18n["Heroes"];

if ($bpdata["currentRound"]["currentDraft"]["status"] == "IN_PROGRESS") {
	$bluepool = $bpdata["teams"][0]["availableTimePool"];
	$redpool = $bpdata["teams"][1]["availableTimePool"];
	$timer = $bpdata["currentRound"]["currentDraft"]["ruleSet"]["timersPreset"]["pickTime"] - (time() - $bpdata["currentRound"]["currentDraft"]["currentPick"]["startedAt"]);
	if ($timer < 0) {
		$timer = 0;
	}
} else {
	$bluepool = "...";
	$redpool = "...";
	$timer = "...";
}

header("Content-type: text/xml");
echo "<datalinq>";
echo "<timer>";
echo "<bluepool>" . $bluepool . "</bluepool>";
echo "<redpool>" . $redpool . "</redpool>";
echo "<main>" . $timer . "</main>";
echo "</timer>";
echo "</datalinq>";
?>