<?php
include_once '../include/mysql.inc.php';
$sqlObj = new sqlHandle();
//Include MemCache Class and initalize
include_once '../include/memcache.inc.php';
$memcObj = new memcacheHandle();
if (isset($_REQUEST["id"])) {
	echo $sqlObj->getBPData($_REQUEST["id"]);
} else {
	$bpdata = $sqlObj->getLastBPData();
}
$bpdata = $bpdata["payload"]["draftSession"];
$actions = $bpdata["currentRound"]["currentDraft"]["actions"];
$pickedHeroes = [];
foreach ($actions as $key => $curract) {
	if (($curract["team"]["number"] == 2) && ($curract["type"] == "pick")) {
		$pickedHeroes[] = $curract["picked"]["hero-code"];
	}
}
header("Content-type: text/xml");
echo "<datalinq>";
echo "<draft>";
echo "<lastHeroPortrait>" . $sqlObj->getHeroImgpath(end($pickedHeroes)) . "</lastHeroPortrait>";
echo "<pos1>";
echo "<heroname>" . $pickedHeroes[0] . "</heroname>";
echo "<imgpath>" . $sqlObj->getHeroImgpath($pickedHeroes[0]) . "</imgpath>";
echo "<portraitpath>" . $sqlObj->getHeroPortraitPath($pickedHeroes[0]) . "</portraitpath>";
echo "</pos1>";
echo "<pos2>";
echo "<heroname>" . $pickedHeroes[1] . "</heroname>";
echo "<imgpath>" . $sqlObj->getHeroImgpath($pickedHeroes[1]) . "</imgpath>";
echo "<portraitpath>" . $sqlObj->getHeroPortraitPath($pickedHeroes[1]) . "</portraitpath>";
echo "</pos2>";
echo "<pos3>";
echo "<heroname>" . $pickedHeroes[2] . "</heroname>";
echo "<imgpath>" . $sqlObj->getHeroImgpath($pickedHeroes[2]) . "</imgpath>";
echo "<portraitpath>" . $sqlObj->getHeroPortraitPath($pickedHeroes[2]) . "</portraitpath>";
echo "</pos3>";
echo "<pos4>";
echo "<heroname>" . $pickedHeroes[3] . "</heroname>";
echo "<imgpath>" . $sqlObj->getHeroImgpath($pickedHeroes[3]) . "</imgpath>";
echo "<portraitpath>" . $sqlObj->getHeroPortraitPath($pickedHeroes[3]) . "</portraitpath>";
echo "</pos4>";
echo "<pos5>";
echo "<heroname>" . $pickedHeroes[4] . "</heroname>";
echo "<imgpath>" . $sqlObj->getHeroImgpath($pickedHeroes[4]) . "</imgpath>";
echo "<portraitpath>" . $sqlObj->getHeroPortraitPath($pickedHeroes[4]) . "</portraitpath>";
echo "</pos5>";
echo "</draft>";
echo "<positioned>";
echo "<pos1>";
echo "<heroname></heroname>";
echo "<imgpath></imgpath>";
echo "<player></player>";
echo "</pos1>";
echo "<pos2>";
echo "<heroname></heroname>";
echo "<imgpath></imgpath>";
echo "<player></player>";
echo "</pos2>";
echo "<pos3>";
echo "<heroname></heroname>";
echo "<imgpath></imgpath>";
echo "<player></player>";
echo "</pos3>";
echo "<pos4>";
echo "<heroname></heroname>";
echo "<imgpath></imgpath>";
echo "<player></player>";
echo "</pos4>";
echo "<pos5>";
echo "<heroname></heroname>";
echo "<imgpath></imgpath>";
echo "<player></player>";
echo "</pos5>";
echo "</positioned>";
echo "</datalinq>"
?>