<?php
function updateBPData($arr) {
	global $sqlObj;
	if ($arr["payload"]["clientData"]) {
		unset($arr["payload"]["clientData"]);
	}
	$arr["payload"]["draftSession"]["version"] = $arr["payload"]["draftSession"]["version"] + 1;
	$sqlObj->setBPData($arr["payload"]["draftSession"]["id"], $arr);
}

function getDraftStrategy($strategyID) {
	$acts = [];
	$strategyName;
	switch ($strategyID) {
	case '2':
		$strategyName = "Heroes 2-Mid BANs";
		$acts = [
			["first", "ban"],
			["second", "ban"],
			["first", "pick"],
			["second", "pick"],
			["second", "pick"],
			["first", "pick"],
			["first", "pick"],
			["second", "ban"],
			["first", "ban"],
			["second", "pick"],
			["second", "pick"],
			["first", "pick"],
			["first", "pick"],
			["second", "pick"],
		];
		break;

	default:
		# code...
		break;
	}
	$ret = [
		"id" => $strategyID,
		"name" => $strategyName,
		"pattern" => $acts,
	];
	return $ret;
}

function getTimerStrategy($strategyID = 1) {
	$strategyName;
	$pickTime;
	$timePool;
	switch ($strategyID) {
	case '1':
		$strategyName = "Heroes Standard Timer Set";
		$pickTime = 30;
		$timePool = 60;
		break;

	default:
		# code...
		break;
	}
	$ret = [
		"id" => $strategyID,
		"name" => $strategyName,
		"pickTime" => $pickTime,
		"timePool" => $timePool,
	];
	return $ret;
}

function draftSession($arr) {
	global $sqlObj;
	//Get time strategy
	$timersPreset = $arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["ruleSet"]["timersPreset"];
	//Lobby Expired Check
	$lobby = $arr["payload"]["draftSession"]["currentRound"]["lobby"];
	if ($lobby["expiresAt"] < time() && !$lobby["bothTeamsAreReady"]) {
		$arr["payload"]["draftSession"]["currentRound"]["status"] = "COMPLETED";
		$arr["payload"]["draftSession"]["status"] = "COMPLETED";
		$sqlObj->setSessionStatus($arr["payload"]["draftSession"]["id"], "completed");
		updateBPData($arr);
		return $arr;
	}
	//return for avoiding mistakes on starting
	if (!$lobby["bothTeamsAreReady"]) {
		return $arr;
	}
	//both ready, let's go
	$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["status"] = "IN_PROGRESS";
	if (!$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["type"]) {
		$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["type"] = "HERO";
	}
	//check current action
	if (isset($arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"])) {
		$currentPick = $arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"];
		$currentTeam = $currentPick["team"]["number"] - 1;
		$currentPickIndex = $currentPick["index"];
		//For disable auto-lockin
		// if (($currentPick["startedAt"] + $arr["payload"]["draftSession"]["teams"][$currentTeam]["availableTimePool"] + $timersPreset["pickTime"] < time())) {
		// 	$currentPick["confirmedAt"] = time();
		// 	$currentPick["updatedAt"] = $currentPick["confirmedAt"];
		// 	$currentPick["isConfirmed"] = true;
		// 	//For Auto-lock-in pre selection
		// 	if (!isset($currentPick["picked"]["hero-code"])) {
		// 		$currentPick["picked"] = [
		// 			"hero-id" => "0",
		// 			"hero-code" => "random",
		// 		];
		// 	}

		// 	$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"] = $currentPick;
		// }
		if ($currentPick["isConfirmed"]) {
			//update team's time pool
			$elapsedTime = $currentPick["confirmedAt"] - $currentPick["startedAt"];
			if ($elapsedTime > $timersPreset["pickTime"]) {
				$timePoolCost = $elapsedTime - $timersPreset["pickTime"];
				$arr["payload"]["draftSession"]["teams"][$currentTeam]["availableTimePool"] = $arr["payload"]["draftSession"]["teams"][$currentPick["team"]["number"] - 1]["availableTimePool"] - $timePoolCost;
				if ($arr["payload"]["draftSession"]["teams"][$currentPick["team"]["number"] - 1]["availableTimePool"] < 0) {
					$arr["payload"]["draftSession"]["teams"][$currentPick["team"]["number"] - 1]["availableTimePool"] = 0;
				}
			}
		}
		//check chogall
		if ($currentPick["allowChogall"] && $currentPick["picked"]["hero-code"] == "cho" && $currentPick["picked"]["hero-id"] == 45 && $currentPick["type"] == "pick" && $currentPick["isConfirmed"]) {
			$gallSlotIndex = $currentPickIndex + 1;
			$gallSlot = $arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"][$gallSlotIndex];
			$gallSlot["confirmedAt"] = $currentPick["confirmedAt"];
			$gallSlot["startedAt"] = $currentPick["startedAt"];
			$gallSlot["updatedAt"] = $currentPick["updatedAt"];
			$gallSlot["isConfirmed"] = $currentPick["isConfirmed"];
			$gallSlot["updatedAt"] = $currentPick["updatedAt"];
			$gallSlot["updatedAt"] = $currentPick["updatedAt"];
			$gallSlot["picked"] = [
				"hero-code" => "gall",
				"hero-id" => 46,
			];

			$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"][$gallSlotIndex] = $gallSlot;
		}
		if ($currentPick["allowChogall"] && $currentPick["picked"]["hero-code"] == "cho" && $currentPick["picked"]["hero-id"] == 45 && $currentPick["picked"]["type"] == "ban") {
			$currentPick["picked"]["hero-id"] == 44;
			$currentPick["picked"]["hero-code"] == "chogall";
		}
		//update current action to original array
		$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"][$currentPickIndex] = $currentPick;
	}

	//goes to next action slot
	foreach ($arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"] as $key => $value) {
		$thisActTeam = $value["team"]["number"];

		//forbid to go to next slot if this one is still avaliable and not confirmed

		//go to next slot
		if (!$value["isConfirmed"] && !$value["startedAt"] && ($key == 0 || $arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"][$key - 1]["isConfirmed"])) {
			$value["updatedAt"] = time();
			$value["startedAt"] = $value["updatedAt"];
			$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"][$key] = $value;
			$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPick"] = $value;
			$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["currentPickIndex"] = $key;
			break;
		}
	}
	//complete the session if last one is confirmed
	$lastAction = end($arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["actions"]);
	if ($lastAction["isConfirmed"]) {
		$arr["payload"]["draftSession"]["status"] = "COMPLETED";
		$arr["payload"]["draftSession"]["currentRound"]["status"] = "COMPLETED";
		$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["status"] = "COMPLETED";
		if (!($arr["payload"]["draftSession"]["teams"][0]["positionSet"] && $arr["payload"]["draftSession"]["teams"][1]["positionSet"])) {
			$arr["payload"]["draftSession"]["status"] = "POSITION";
			$arr["payload"]["draftSession"]["currentRound"]["status"] = "POSITION";
			$arr["payload"]["draftSession"]["currentRound"]["currentDraft"]["status"] = "POSITION";
		} else {
			$sqlObj->setSessionStatus($arr["payload"]["draftSession"]["id"], "completed");
		}
	}

	updateBPData($arr);
	return $arr;
}
?>